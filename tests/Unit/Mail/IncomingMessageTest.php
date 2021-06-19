<?php

namespace Tests\Unit\Mail;

use App\Mail\IncomingMessage;
use App\Mail\NotPermittedSenderMessage;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * @see \App\Mail\IncomingMessage
 */
class IncomingMessageTest extends TestCase
{
	use RefreshDatabase;

	protected bool $tenancy = false;

	/**
	 * @test
	 * @dataProvider recipientsToAcceptProvider
	 */
	public function resendToGroup($input): void
	{
		$this->markTestIncomplete('Fails for multiple recipients');

		// Arrange
		Mail::fake();

		$tenant = Tenant::create(
			'tenant1',
			'Tenant One',
			'Australia/Perth',
		);
		$tenant->domains()->create(['domain' => 'tenant1']);

		$group_expected = UserGroup::create([
			'title'     => 'Music Team',
			'slug'      => 'music-team',
			'list_type' => 'chat',
			'tenant_id' => $tenant->id,
		]);
		$user1 = User::factory()->create([
			'email'     => 'permitted@example.com',
			'tenant_id' => $tenant->id,
		]);
		$user2 = User::factory()->create();
		$group_expected->recipient_users()->attach([$user1->id, $user2->id]);

		// @todo test with roles

		$message = (new IncomingMessage())
			->to($input['to'])
			->cc($input['cc'])
			->bcc($input['bcc'])
			->from($input['from'])
			->subject('Just a test');

		// Act
		$message->resendToGroups();

		// Assert
		Mail::assertSent(IncomingMessage::class, 2);
		Mail::assertSent(IncomingMessage::class, static function($mail) use ($input){
			$mail->build();
			return $mail->hasFrom('music-team@tenant1.choirconcierge.test')
				&& $mail->hasReplyTo($input['from']);
		});
	}

    /**
     * @test
     */
    public function resendToGroups_with_one_group(): void
    {
        // Arrange
        Mail::fake();

        $tenant = Tenant::create(
            'tenant1',
            'Tenant One',
            'Australia/Perth',
        );
        $tenant->domains()->create(['domain' => 'tenant1']);

        $group_expected = UserGroup::create([
            'title'     => 'Music Team',
            'slug'      => 'music-team',
            'list_type' => 'chat',
            'tenant_id' => $tenant->id,
        ]);
        $user1 = User::factory()->create([
            'email'     => 'permitted@example.com',
            'tenant_id' => $tenant->id,
        ]);
        $group_expected->recipient_users()->attach([$user1->id]);

        $message = (new IncomingMessage())
            ->to('music-team@tenant1.choirconcierge.test')
//            ->cc('')
//            ->bcc('')
            ->from('permitted@example.com')
            ->subject('Just a test');

        // Act
        $message->resendToGroups();

        // Assert
        Mail::assertNotSent(NotPermittedSenderMessage::class);
        Mail::assertSent(IncomingMessage::class, 1);
        Mail::assertSent(IncomingMessage::class, static function($mail) {
            $mail->build();
            return $mail->hasFrom('music-team@tenant1.choirconcierge.test')
                && $mail->hasReplyTo('permitted@example.com');
        });
    }

	/**
	 * @test
	 * @dataProvider recipientsToAcceptProvider
	 * @dataProvider recipientsToRejectProvider
	 */
    public function getMatchingGroup($groups, $input, $should_match): void
    {
    	// Arrange
	    Tenant::create(
	    	'tenant1',
		    'Tenant One',
		    'Australia/Perth',
	    );
        Tenant::create(
            'tenant2',
            'Tenant Two',
            'Australia/Perth',
        );

	    $groups_expected = collect($groups)->map(fn($group) => Tenant::find($group['tenant'])
            ->run(fn() => UserGroup::create([
	        'title'     => $group['name'],
            'slug'      => Str::slug($group['name']),
            'list_type' => 'chat',
        ])));

    	$message = (new IncomingMessage())
	        ->to($input['to'])
	        ->cc($input['cc'])
	        ->bcc($input['bcc'])
	        ->from($input['from'])
	        ->subject('Just a test');

    	// Act
        $groups_found = $message->getMatchingGroups()->flatten(1);

	    // Assert
        self::assertCount($should_match, $groups_found);
    }

    public function recipientsToAcceptProvider(): array
    {
        $default_groups = [
            ['name' => 'Music Team', 'tenant' => 'tenant1'],
        ];
        $default_input = [
            'to'     => 'anybody@example.com',
            'cc'     => 'somebody@example.com',
            'bcc'    => 'nobody@example.com',
            'from'   => 'permitted@example.com',
        ];

    	return [
			'Single TO' => [
                'groups' => $default_groups,
		   	    'input' => array_merge($default_input, [
                    'to'     => 'music-team@tenant1.choirconcierge.test', // @todo test different subdomains
		        ]),
				1,
			],
			'Match single To in multiple TOs' => [
                'groups' => $default_groups,
                'input' => array_merge($default_input, [
                    'to'     => [
                        'skip@example.com',
                        'music-team@tenant1.choirconcierge.test',
                    ],
                ]),
                1
			],
            'Match multiple Tos' => [
                'groups' => [
                    ['name' => 'Music Team', 'tenant' => 'tenant1'],
                    ['name' => 'Membership Team', 'tenant' => 'tenant1'],
                ],
                'input' => array_merge($default_input, [
                    'to'     => [
                        'skip@example.com',
                        'music-team@tenant1.choirconcierge.test',
                        'membership-team@tenant1.choirconcierge.test',
                    ],
                ]),
                2
            ],
            'Match Tos from multiple tenants' => [
                'groups' => [
                    ['name' => 'Music Team', 'tenant' => 'tenant1'],
                    ['name' => 'Music Team', 'tenant' => 'tenant2'],
                ],
                'input' => array_merge($default_input, [
                    'to'     => [
                        'skip@example.com',
                        'music-team@tenant1.choirconcierge.test',
                        'music-team@tenant2.choirconcierge.test',
                    ],
                ]),
                2
            ],
		    'Single CC' => [
                'groups' => $default_groups,
			    'input' => array_merge($default_input, [
				    'cc'     => 'music-team@tenant1.choirconcierge.test',
			    ]),
			    true
		    ],
		    'Multiple CCs' => [
                'groups' => $default_groups,
                'input' => array_merge($default_input, [
                    'cc'     => [
                        'skip@example.com',
                        'music-team@tenant1.choirconcierge.test',
                    ],
                ]),
			    true
		    ],
		    'Single BCC' => [
                'groups' => $default_groups,
                'input' => array_merge($default_input, [
                    'bcc'     => 'music-team@tenant1.choirconcierge.test',
                ]),
			    true
		    ],
		    'Multiple BCCs' => [
                'groups' => $default_groups,
                'input' => array_merge($default_input, [
				    'bcc'     => [
                        'skip@example.com',
                        'music-team@tenant1.choirconcierge.test',
                    ],
                ]),
			    true
		    ],
	    ];
    }

	public function recipientsToRejectProvider(): array
	{
		return [
			'Reject FROM matches CC' => [
                'groups' => [
                    ['name' => 'Music Team', 'tenant' => 'tenant1'],
                ],
				'input' => [
					'to'     => 'somebody@example.com',
					'cc'     => 'music-team@tenant1.choirconcierge.test',
					'bcc'    => 'nobody@example.com',
					'from'   => 'music-team@tenant1.choirconcierge.test',
				],
				false,
			],
//			@todo reject for non-existent tenant
//			'Reject TO has matching slug but wrong domain' => [
//				'input' => [
//					'to'     => 'music-team@wrong.com',
//					'cc'     => 'somebody@example.com',
//					'bcc'    => 'nobody@example.com',
//					'from'   => 'nothing@example.com',
//				],
//				false,
//			],
		];
	}
}

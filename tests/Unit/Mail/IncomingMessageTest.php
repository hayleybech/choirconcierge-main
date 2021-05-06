<?php

namespace Tests\Unit\Mail;

use App\Mail\IncomingMessage;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
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
		$message->resendToGroup();

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
	 * @dataProvider recipientsToAcceptProvider
	 * @dataProvider recipientsToRejectProvider
	 */
    public function getMatchingGroup($input, $should_match): void
    {
    	// Arrange
	    $tenant = Tenant::create(
	    	'tenant1',
		    'Tenant One',
		    'Australia/Perth',
	    );
    	$group_expected = UserGroup::create([
    		'title'     => 'Music Team',
		    'slug'      => 'music-team',
		    'list_type' => 'chat',
		    'tenant_id' => $tenant->id,
	    ]);
    	$message = (new IncomingMessage())
	        ->to($input['to'])
	        ->cc($input['cc'])
	        ->bcc($input['bcc'])
	        ->from($input['from'])
	        ->subject('Just a test');

    	// Act
	    $group_found = $message->getMatchingGroup();

	    // Assert
	    self::assertEquals($should_match, $group_expected->is($group_found), $should_match ? 'Not the same group.' : 'No group should be returned.');
    }

    public function recipientsToAcceptProvider(): array
    {
    	return [
			'Single TO' => [
		   	    'input' => [
				   'to'     => 'music-team@tenant1.choirconcierge.test', // @todo test different subdomains
				   'cc'     => 'somebody@example.com',
				   'bcc'    => 'nobody@example.com',
				   'from'   => 'permitted@example.com',
		        ],
				true
			],
			'Multiple TOs' => [
		        'input' => [
			        'to'     => [
			        	'skip@example.com',
			        	'music-team@tenant1.choirconcierge.test',
			        ],
			        'cc'     => 'somebody@example.com',
			        'bcc'    => 'nobody@example.com',
			        'from'   => 'permitted@example.com',
		        ],
				true
			],
		    'Single CC' => [
			    'input' => [
				    'to'     => 'somebody@example.com',
				    'cc'     => 'music-team@tenant1.choirconcierge.test',
				    'bcc'    => 'nobody@example.com',
				    'from'   => 'permitted@example.com',
			    ],
			    true
		    ],
		    'Multiple CCs' => [
			    'input' => [
				    'to'     => 'somebody@example.com',
				    'cc'     => [
					    'skip@example.com',
					    'music-team@tenant1.choirconcierge.test',
				    ],
				    'bcc'    => 'nobody@example.com',
				    'from'   => 'permitted@example.com',
			    ],
			    true
		    ],
		    'Single BCC' => [
			    'input' => [
				    'to'     => 'somebody@example.com',
				    'cc'    => 'nobody@example.com',
				    'bcc'     => 'music-team@tenant1.choirconcierge.test',
				    'from'   => 'permitted@example.com',
			    ],
			    true
		    ],
		    'Multiple BCCs' => [
			    'input' => [
				    'to'     => 'somebody@example.com',
				    'cc'    => 'nobody@example.com',
				    'bcc'     => [
					    'skip@example.com',
					    'music-team@tenant1.choirconcierge.test',
				    ],
				    'from'   => 'permitted@example.com',
			    ],
			    true
		    ],
	    ];
    }

	public function recipientsToRejectProvider(): array
	{
		return [
			'Reject FROM matches CC' => [
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

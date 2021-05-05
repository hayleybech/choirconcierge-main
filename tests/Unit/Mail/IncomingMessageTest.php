<?php

namespace Tests\Unit\Mail;

use App\Mail\IncomingMessage;
use App\Models\Tenant;
use App\Models\UserGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @see \App\Mail\IncomingMessage
 */
class IncomingMessageTest extends TestCase
{
	use RefreshDatabase;

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
				   'to'     => 'music-team@tenant1.choirconcierge.com', // @todo test different subdomains
				   'cc'     => 'somebody@example.com',
				   'bcc'    => 'nobody@example.com',
				   'from'   => 'nothing@example.com',
		        ],
				true
			],
			'Multiple TOs' => [
		        'input' => [
			        'to'     => [
			        	'skip@example.com',
			        	'music-team@tenant1.choirconcierge.com',
			        ],
			        'cc'     => 'somebody@example.com',
			        'bcc'    => 'nobody@example.com',
			        'from'   => 'nothing@example.com',
		        ],
				true
			],
		    'Single CC' => [
			    'input' => [
				    'to'     => 'somebody@example.com',
				    'cc'     => 'music-team@tenant1.choirconcierge.com',
				    'bcc'    => 'nobody@example.com',
				    'from'   => 'nothing@example.com',
			    ],
			    true
		    ],
		    'Multiple CCs' => [
			    'input' => [
				    'to'     => 'somebody@example.com',
				    'cc'     => [
					    'skip@example.com',
					    'music-team@tenant1.choirconcierge.com',
				    ],
				    'bcc'    => 'nobody@example.com',
				    'from'   => 'nothing@example.com',
			    ],
			    true
		    ],
		    'Single BCC' => [
			    'input' => [
				    'to'     => 'somebody@example.com',
				    'cc'    => 'nobody@example.com',
				    'bcc'     => 'music-team@tenant1.choirconcierge.com',
				    'from'   => 'nothing@example.com',
			    ],
			    true
		    ],
		    'Multiple BCCs' => [
			    'input' => [
				    'to'     => 'somebody@example.com',
				    'cc'    => 'nobody@example.com',
				    'bcc'     => [
					    'skip@example.com',
					    'music-team@tenant1.choirconcierge.com',
				    ],
				    'from'   => 'nothing@example.com',
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
					'cc'     => 'music-team@tenant1.choirconcierge.com',
					'bcc'    => 'nobody@example.com',
					'from'   => 'music-team@tenant1.choirconcierge.com',
				],
				false,
			],
		];
	}
}

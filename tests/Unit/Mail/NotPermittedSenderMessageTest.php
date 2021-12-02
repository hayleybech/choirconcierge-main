<?php

namespace Tests\Unit\Mail;

use App\Mail\NotPermittedSenderMessage;
use App\Models\Tenant;
use App\Models\UserGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class NotPermittedSenderMessageTest extends TestCase
{
	use RefreshDatabase;

	protected bool $tenancy = false;

	/** @test */
	public function it_renders_the_correct_content()
	{
		$tenant = $this->createTestTenants(2)[0];

		$tenant->run(function() {
			$group = UserGroup::create([
				'title' => 'Music Team',
				'slug' => 'music-team',
				'list_type' => 'chat',
				'tenant_id' => 'test-tenant-1',
			]);

			$mailable = new NotPermittedSenderMessage($group);

			$mailable->assertSeeInHtml('You are not a permitted sender');
			$mailable->assertSeeInHtml('Music Team');
			$mailable->assertSeeInText('Test Tenant 1');
		});

	}

	/**
	 * @param int $numTenants
	 * @return Collection<Tenant>
	 */
	private function createTestTenants(int $numTenants = 1): Collection
	{
		return Collection::times($numTenants, function($index) {
			return [
				'test-tenant-'.$index,
				'Test Tenant '.$index,
				'Australia/Perth'
			];
		})
			->map(function($data) {
				$tenant = Tenant::create(...$data);
				$tenant->domains()->create(['domain' => $tenant->id]);

				tenancy()->end();

				return $tenant;
			});
	}
}

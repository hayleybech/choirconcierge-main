<?php

use App\Models\Membership;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Support\VisualRegression\ScreenshotCatalog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Tests\Browser\VisualRegressionTestCase;

uses(VisualRegressionTestCase::class)->group('visual');

test('captures every catalog case at every viewport without JavaScript errors', function () {
	$catalog = app(ScreenshotCatalog::class);
	$latestDirectory = config('visual-regression.latest_directory');
	$baselineDirectory = config('visual-regression.baseline_directory');

	expect(app()->environment())
		->toBe('testing')
		->and(config('database.connections.mysql.database'))
		->toMatch('/^choirconcierge_test(?:_[0-9]+(?:\.[0-9]+)?)?$/');

	config(['app.debug' => false]);
	Carbon::setTestNow('2026-10-23 12:00:00');
	$authenticatedUser = null;
	$visualRegressionTenant = null;
	$tenantAdminSetUp = false;

	try {
		foreach ($catalog->cases() as $case) {
			if (in_array($case['persona'], ['authenticated-user', 'tenant-admin'], true)) {
				if ($authenticatedUser === null) {
					$authenticatedUser = User::factory()->create([
						'first_name' => 'Visual',
						'last_name' => 'Regression',
						'email' => 'visual-regression@example.test',
						'dob' => '1990-01-15',
						'phone' => '555-0100',
						'ice_name' => 'Emergency Contact',
						'ice_phone' => '555-0101',
						'address_street_1' => '1 Test Street',
						'address_street_2' => 'Suite 2',
						'address_suburb' => 'Testville',
						'address_state' => 'WA',
						'address_postcode' => '6000',
						'profession' => 'Synthetic Test Profile',
						'skills' => 'Choir singing',
						'height' => 170,
						'bha_id' => '1234',
					]);
				}

				$this->actingAs($authenticatedUser);
			}

			if (
				in_array($case['id'], ['central-tenant-show', 'central-tenant-onboarding'], true) ||
				$case['persona'] === 'tenant-admin'
			) {
				$visualRegressionTenant ??= Tenant::factory()
					->withDomain()
					->create([
						'id' => 'visual-regression',
						'name' => 'Visual Regression Choir',
						'timezone' => 'Australia/Perth',
						'created_by' => $authenticatedUser->id,
						'data' => ['has_gratis' => true],
					]);
			}

			if ($case['persona'] === 'tenant-admin' && !$tenantAdminSetUp) {
				tenancy()->initialize($visualRegressionTenant);

				try {
					$role = Role::create([
						'name' => 'Admin',
						'abilities' => Role::ALL_ABILITIES,
					]);

					$membership = Membership::factory()->create([
						'user_id' => $authenticatedUser->id,
						'joined_at' => '2000-01-01 00:00:00',
					]);

					$membership->roles()->attach($role->id);
					$authenticatedUser->unsetRelation('membership');
					expect($membership->tenant_id)->toBe($visualRegressionTenant->id);
				} finally {
					tenancy()->end();
				}

				$this->actingAs($authenticatedUser);
				$tenantAdminSetUp = true;
			}

			foreach ($catalog->viewports() as $viewport) {
				File::ensureDirectoryExists($latestDirectory . DIRECTORY_SEPARATOR . $case['id']);
				File::ensureDirectoryExists($baselineDirectory . DIRECTORY_SEPARATOR . $case['id']);

				$page = visit(route($case['route'], $case['parameters'], false))->resize(
					$viewport['width'],
					$viewport['height'],
				);

				expect($page->script("JSON.parse(document.querySelector('#app').dataset.page).component"))->toBe(
					$case['component'],
				);

				$page->assertSee($case['assert_text']);
				$page->script(
					"document.head.insertAdjacentHTML('beforeend', '<style>*{animation:none!important;transition:none!important;font-family:Arial,sans-serif!important;}</style>')",
				);
				$page
					->wait(0.25)
					->assertNoJavaScriptErrors()
					->screenshot(true, 'Latest/' . $case['id'] . '/' . $viewport['id'] . '.png');
			}
		}
	} finally {
		Carbon::setTestNow();
	}
});

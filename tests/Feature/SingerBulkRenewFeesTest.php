<?php

use App\Models\Membership;
use Illuminate\Support\Carbon;

test('renews selected singers from their expiry or today and remembers the selected period', function (string $period, string $expiry, string $expectedExpiry, string $expectedNullExpiry) {
    $this->actingAsRole('Accounts Team');
    $this->travelTo(Carbon::parse('2026-01-31 12:00:00'));

    $singers = Membership::factory()->count(2)->sequence(
        ['paid_until' => $expiry],
        ['paid_until' => null],
    )->create();

    $this->post(the_tenant_route('singers.bulk-fees.update'), [
        'singer_ids' => $singers->modelKeys(),
        'freq' => $period,
    ])
        ->assertRedirect(route('singers.index'))
        ->assertSessionHasNoErrors();

    expect($singers[0]->refresh()->paid_until->toDateString())->toBe($expectedExpiry);
    expect($singers[1]->refresh()->paid_until->toDateString())->toBe($expectedNullExpiry);
    expect(tenant()->refresh()->fee_freq)->toBe($period);
})->with([
    'month' => ['month', '2026-01-31', '2026-02-28', '2026-02-28'],
    'quarter' => ['quarter', '2026-01-31', '2026-04-30', '2026-04-30'],
    'half-year' => ['halfyear', '2026-08-31', '2027-02-28', '2026-07-31'],
    'year' => ['year', '2024-02-29', '2025-02-28', '2027-01-31'],
]);

test('forbids bulk fee renewal without finance permission', function () {
    $this->actingAsRole('Membership Team');
    $singer = Membership::factory()->create(['paid_until' => '2026-06-30']);

    $this->post(the_tenant_route('singers.bulk-fees.update'), [
        'singer_ids' => [$singer->id],
        'freq' => 'year',
    ])->assertForbidden();

    expect($singer->refresh()->paid_until->toDateString())->toBe('2026-06-30');
    expect(tenant()->refresh()->fee_freq)->toBeNull();
});

test('rejects unsupported bulk fee renewal periods', function () {
    $this->actingAsRole('Accounts Team');
    $singer = Membership::factory()->create(['paid_until' => '2026-06-30']);
    $this->post(the_tenant_route('singers.bulk-fees.update'), [
        'singer_ids' => [$singer->id],
        'freq' => 'decade',
    ])->assertSessionHasErrors(['freq']);

    expect($singer->refresh()->paid_until->toDateString())->toBe('2026-06-30');
    expect(tenant()->refresh()->fee_freq)->toBeNull();
});

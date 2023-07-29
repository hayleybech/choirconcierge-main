<?php

use App\Models\Enrolment;
use App\Models\VoicePart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\put;

uses(RefreshDatabase::class);

test('update saves the voice part', function () {
    actingAs($this->createUserWithRole('Membership Team'));

    $enrolment = Enrolment::factory()->create();
    $part = VoicePart::pluck('id')->random(1)[0];

    put(the_tenant_route('singers.enrolments.update', [$enrolment->membership, $enrolment]), [
            'voice_part_id' => $part,
        ])
        ->assertRedirect();

    assertDatabaseHas('enrolments', [
        'id' => $enrolment->id,
        'voice_part_id' => $part,
    ]);
});

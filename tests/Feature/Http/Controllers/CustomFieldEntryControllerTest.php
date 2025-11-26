<?php

use App\Models\CustomField;
use App\Models\Membership;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

uses(RefreshDatabase::class, WithFaker::class);

it('can create custom field entries', function () {
    $this->actingAsRole('Admin');
    $this->setUpFaker();

    $singer = Membership::factory()->create();
    $field = CustomField::factory()->create();
    $value = $this->faker->words(3, true);

    $this->post(the_tenant_route('singers.custom-fields.store', [
        'singer' => $singer->id,
    ]), [
        'customFieldId' => $field->id,
        'value' => $value,
    ])
        ->assertRedirect();

    $this->assertDatabaseHas('custom_field_entries', [
        'value' => $value,
        'custom_field_id' => $field->id,
        'membership_id' => $singer->id,
    ]);
});

it('can update custom field entries', function () {
    $this->actingAsRole('Admin');
    $this->setUpFaker();

    $field = CustomField::factory()->create();
    $singer = Membership::factory()
        ->hasAttached($field, fn() => ['value' => $this->faker->words(3, true)])
        ->create();

    $newValue = $this->faker->words(3, true);

    $this->put(the_tenant_route('singers.custom-fields.update', [
        'singer' => $singer->id,
        'entry' => $singer->customFields()->first()->entry->id,
    ]), [
        'customFieldId' => $field->id,
        'value' => $newValue,
    ])
        ->assertRedirect();

    $this->assertDatabaseHas('custom_field_entries', [
        'value' => $newValue,
        'custom_field_id' => $field->id,
        'membership_id' => $singer->id,
    ]);
});
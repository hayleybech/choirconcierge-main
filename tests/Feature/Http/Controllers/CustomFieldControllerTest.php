<?php

use App\Models\CustomField;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

uses(RefreshDatabase::class, WithFaker::class);

it('can create custom fields', function () {
    $this->actingAsRole('Admin');
    $this->setUpFaker();

    $name = $this->faker->words(3, true);

    $this->post(the_tenant_route('custom-fields.store'), [
            'name' => $name,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('custom_fields', ['name' => $name]);
});

it('can delete custom fields', function () {
    $this->actingAsRole('Admin');
    $this->setUpFaker();

    $field = CustomField::factory()->create();

    $this->delete(the_tenant_route('custom-fields.destroy', $field))
        ->assertRedirect();

    $this->assertDatabaseMissing('custom_fields', ['id' => $field->id]);
});
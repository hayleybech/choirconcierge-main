<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tenant>
 */
class TenantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->word,
            'name' => $this->faker->sentence,
        ];
    }

    public function withDomain(): static
    {
        return $this->afterCreating(function (Tenant $tenant) {
            $tenant->domains()->create(['domain' => $tenant->id]);
        });
    }

    public function withEnsemble(): static
    {
        return $this->afterCreating(function (Tenant $tenant) {
            $tenant->ensembles()->create(['name' => $tenant->name]);
        });
    }

    /**
     * Indicate that the tenant should have a subscription plan.
     *
     * @return $this
     */
    public function withSubscription(string|int $planId = null): static
    {
        return $this->afterCreating(function (Tenant $tenant) use ($planId) {
            optional($tenant->customer)->update(['trial_ends_at' => null]);

            $tenant->subscriptions()->create([
                'name' => 'default',
                'paddle_id' => fake()->unique()->numberBetween(1, 1000),
                'paddle_status' => 'active',
                'paddle_plan' => $planId,
                'quantity' => 1,
                'trial_ends_at' => null,
                'paused_from' => null,
                'ends_at' => null,
            ]);
        });
    }
}

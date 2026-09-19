<?php

namespace Database\Seeders\Dummy;

use App\Models\Ensemble;
use App\Models\Membership;
use App\Models\RiserStack;
use Illuminate\Database\Seeder;

class DummyRiserStacksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ensembles = Ensemble::all();

        if ($ensembles->isEmpty()) {
            return;
        }

        RiserStack::factory()
            ->count(5)
            ->create([
                'tenant_id' => $ensembles->first()->tenant_id,
            ])
            ->each(function (RiserStack $stack) use ($ensembles) {
                $ensemble = $ensembles->random();
                $stack->ensembles()->attach($ensemble->id);

                // Add some random members to the stack
                $members = Membership::query()
                    ->whereHas('enrolments', fn($query) => $query->where('ensemble_id', $ensemble->id))
                    ->inRandomOrder()
                    ->get();

                $positions = [];
                for ($row = 1; $row <= $stack->rows; $row++) {
                    for ($column = 1; $column <= $stack->columns; $column++) {
                        $positions[] = ['row' => $row, 'column' => $column];
                    }
                }

                shuffle($positions);

                foreach ($members as $member) {
                    if (empty($positions)) {
                        break;
                    }

                    $position = array_pop($positions);

                    $stack->members()->attach($member->id, [
                        'row' => $position['row'],
                        'column' => $position['column'],
                    ]);
                }
            });
    }
}

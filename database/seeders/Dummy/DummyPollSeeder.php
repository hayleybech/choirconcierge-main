<?php

namespace Database\Seeders\Dummy;

use App\Models\Poll;
use App\Models\PollOption;
use Illuminate\Database\Seeder;

class DummyPollSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Poll::factory()
            ->count(5)
            ->has(PollOption::factory()->count(3), 'options')
            ->create();

        // Create a closed poll
        Poll::factory()
            ->count(2)
            ->state([
                'is_closed' => true,
                'close_at' => now()->subDay(),
            ])
            ->has(PollOption::factory()->count(4), 'options')
            ->create();

        // Create a poll that allows multiple votes
        Poll::factory()
            ->count(1)
            ->state([
                'title' => 'Which days can you attend the extra rehearsal?',
                'can_vote_multiple' => true,
            ])
            ->has(PollOption::factory()->count(5)->sequence(
                ['label' => 'Monday'],
                ['label' => 'Tuesday'],
                ['label' => 'Wednesday'],
                ['label' => 'Thursday'],
                ['label' => 'Friday'],
            ), 'options')
            ->create();
    }
}

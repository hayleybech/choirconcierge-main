<?php

namespace Database\Seeders\Dummy;

use App\Models\ForumCategory;
use App\Models\ForumTopic;
use App\Models\Membership;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use RuntimeException;

class DummyForumTopicSeeder extends Seeder
{
    /**
     * @var array<int, array{
     *     category: string,
     *     title: string,
     *     body: string,
     *     is_pinned: bool,
     *     days_ago: int,
     *     replies: array<int, array{body: string, author_offset: int, child: string}>
     * }>
     */
    private const TOPICS = [
        [
            'category' => 'Rehearsal & Performance',
            'title' => 'A gentler start to Thursday rehearsal',
            'body' => 'We have a long set to get through this week. Could we start with five minutes of breathing and a familiar round before moving into the new repertoire?',
            'is_pinned' => true,
            'days_ago' => 6,
            'replies' => [
                [
                    'body' => 'I like this idea. The round helps me settle in after work.',
                    'author_offset' => 1,
                    'child' => 'Same here. I can bring the round cards if that helps.',
                ],
                [
                    'body' => 'Could we keep the warm-up to ten minutes so we still have time for the new piece?',
                    'author_offset' => 2,
                    'child' => 'That sounds like a good balance to me.',
                ],
            ],
        ],
        [
            'category' => 'Rehearsal & Performance',
            'title' => 'Saturday concert call time and lifts',
            'body' => 'The venue has confirmed that we can get into the hall at 2:00 pm. Let us share lifts here and aim to be ready for sound check by 3:15.',
            'is_pinned' => false,
            'days_ago' => 2,
            'replies' => [
                [
                    'body' => 'I have three spare seats leaving from the library car park.',
                    'author_offset' => 1,
                    'child' => 'I would love a seat, thank you. I will message you directly.',
                ],
                [
                    'body' => 'I can drive from the north side and pick up two people.',
                    'author_offset' => 3,
                    'child' => 'Perfect, I will add my name to the lift list.',
                ],
            ],
        ],
        [
            'category' => 'Rehearsal & Performance',
            'title' => 'Warm-up suggestions for the outdoor set',
            'body' => 'We will be singing outside next month, so it would be useful to practise projecting without pushing. Has anyone got a favourite exercise for that?',
            'is_pinned' => false,
            'days_ago' => 4,
            'replies' => [
                [
                    'body' => 'The gentle consonant exercise from last term worked well for me.',
                    'author_offset' => 2,
                    'child' => 'Let us add it to the warm-up list for next week.',
                ],
                [
                    'body' => 'A few minutes of humming first might help us avoid over-singing.',
                    'author_offset' => 4,
                    'child' => 'Agreed. We can try it before we run the outdoor pieces.',
                ],
            ],
        ],
        [
            'category' => 'Music & Repertoire',
            'title' => 'Could we bring back the Welsh lullaby?',
            'body' => 'I found my copy of the Welsh lullaby we sang a couple of years ago. It could fit beautifully into a quieter part of the winter programme.',
            'is_pinned' => false,
            'days_ago' => 3,
            'replies' => [
                [
                    'body' => 'That is one of my favourites. I still have the pronunciation notes.',
                    'author_offset' => 1,
                    'child' => 'Would you mind bringing them to rehearsal? They would be very helpful.',
                ],
                [
                    'body' => 'I think the altos have the score in the shared folder too.',
                    'author_offset' => 3,
                    'child' => 'I found it there. I will bring a few printed copies.',
                ],
            ],
        ],
        [
            'category' => 'Music & Repertoire',
            'title' => 'Suggestions for our winter repertoire',
            'body' => 'We have space for one more piece in the winter set. Please share suggestions that work well for mixed voices and can be learned over a few rehearsals.',
            'is_pinned' => false,
            'days_ago' => 1,
            'replies' => [
                [
                    'body' => 'A simple arrangement of In the Bleak Midwinter would give us a lovely contrast.',
                    'author_offset' => 2,
                    'child' => 'I can bring a couple of versions for us to compare.',
                ],
                [
                    'body' => 'Could we include something upbeat as well? The audience always enjoys that.',
                    'author_offset' => 4,
                    'child' => 'Good point. A short round might make a nice closing number.',
                ],
            ],
        ],
        [
            'category' => 'Choir Community',
            'title' => 'Welcome to our new singers',
            'body' => 'A warm welcome to everyone who joined us this term. Introduce yourself, share your voice part, or ask any questions about rehearsals and performances.',
            'is_pinned' => false,
            'days_ago' => 5,
            'replies' => [
                [
                    'body' => 'I joined the altos this month and have already felt very welcome.',
                    'author_offset' => 1,
                    'child' => 'We are glad you are here! Let us know if you need any music links.',
                ],
                [
                    'body' => 'Welcome from the tenors. We usually meet for a quick coffee before rehearsal.',
                    'author_offset' => 3,
                    'child' => 'That sounds lovely. I will join you next Thursday.',
                ],
            ],
        ],
    ];

    public function run(): void
    {
        $memberships = Membership::query()->active()->orderBy('id')->get();

        if ($memberships->isEmpty()) {
            $this->command?->warn('Forum topics were not seeded because this tenant has no active members.');

            return;
        }

        $categoryTitles = collect(self::TOPICS)->pluck('category')->unique();
        $categories = ForumCategory::query()->whereIn('title', $categoryTitles)->get()->keyBy('title');

        if ($categories->count() !== $categoryTitles->count()) {
            throw new RuntimeException('Seed forum categories before forum topics.');
        }

        collect(self::TOPICS)->each(function (array $topicData, int $topicIndex) use ($categories, $memberships): void {
            $daysAgo = $topicData['days_ago'];
            $category = $categories->get($topicData['category']);

            if (! $category instanceof ForumCategory) {
                throw new RuntimeException('The configured forum category could not be found.');
            }

            $topic = $category->topics()->firstOrCreate(
                ['title' => $topicData['title']],
                [
                    'membership_id' => $this->memberAt($memberships, $topicIndex)->id,
                    'body' => $topicData['body'],
                    'is_pinned' => $topicData['is_pinned'],
                ],
            );

            $this->backdateNewModel($topic, $daysAgo);
            $this->seedTopicReactions($topic, $memberships, $topicIndex);
            $this->seedReplies($topic, $topicData['replies'], $memberships, $topicIndex, $daysAgo);

            $this->memberAt($memberships, $topicIndex + 1)
                ->forumTopicFollows()
                ->firstOrCreate(['forum_topic_id' => $topic->id]);
        });
    }

    private function seedTopicReactions(ForumTopic $topic, Collection $memberships, int $topicIndex): void
    {
        collect([
            ['member_offset' => 1, 'emoji' => '👍'],
            ['member_offset' => 2, 'emoji' => '👍'],
            ['member_offset' => 3, 'emoji' => '❤️'],
        ])->each(function (array $reactionData) use ($memberships, $topic, $topicIndex): void {
            $topic->reactions()->firstOrCreate([
                'membership_id' => $this->memberAt($memberships, $topicIndex + $reactionData['member_offset'])->id,
                'emoji' => $reactionData['emoji'],
            ]);
        });
    }

    /**
     * @param array<int, array{body: string, author_offset: int, child: string}> $replies
     */
    private function seedReplies(ForumTopic $topic, array $replies, Collection $memberships, int $topicIndex, int $daysAgo): void
    {
        collect($replies)->each(function (array $replyData, int $replyIndex) use ($daysAgo, $memberships, $topic, $topicIndex): void {
            $reply = $topic->replies()->firstOrCreate([
                'parent_id' => null,
                'membership_id' => $this->memberAt($memberships, $topicIndex + $replyData['author_offset'])->id,
                'body' => $replyData['body'],
            ]);

            $this->backdateNewModel($reply, $daysAgo, 1);

            $reply->reactions()->firstOrCreate([
                'membership_id' => $this->memberAt($memberships, $topicIndex + $replyIndex + 3)->id,
                'emoji' => $replyIndex === 0 ? '🎉' : '😂',
            ]);

            $childReply = $topic->replies()->firstOrCreate([
                'parent_id' => $reply->id,
                'membership_id' => $this->memberAt($memberships, $topicIndex + $replyData['author_offset'] + 1)->id,
                'body' => $replyData['child'],
            ]);

            $this->backdateNewModel($childReply, $daysAgo);
        });
    }

    private function memberAt(Collection $memberships, int $index): Membership
    {
        return $memberships->get($index % $memberships->count())
            ?? throw new RuntimeException('Forum seed data requires at least one active member.');
    }

    private function backdateNewModel(Model $model, int $daysAgo, int $hoursAgo = 0): void
    {
        if (! $model->wasRecentlyCreated) {
            return;
        }

        $createdAt = now()->subDays($daysAgo)->subHours($hoursAgo);

        $model->forceFill([
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ])->saveQuietly();
    }
}

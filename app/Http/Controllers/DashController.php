<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\LearningStatus;
use App\Models\Singer;
use App\Models\Song;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\In;
use Inertia\Inertia;

class DashController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     */
    public function index(): \Inertia\Response|View
    {
        $birthdays = User::query()
            ->whereHas('singers', fn ($query) => $query->active())
            ->birthdays()
            ->get()
            ->each->append('birthday')
            ->sort(static function (User $user1, User $user2): int {
                // Sort by birthday

                if ($user1->birthday->equalTo($user2->birthday)) {
                    return 0;
                }

                return $user1->birthday < $user2->birthday ? -1 : 1;
            });

        $memberversaries = Singer::query()
            ->with('user')
            ->active()
            ->memberversaries()
            ->get()
            ->sort(static function (Singer $singer1, Singer $singer2): int {
                // Sort by joined date
                if ($singer1->joined_at->equalTo($singer2->joined_at)) {
                    return 0;
                }

                return $singer1->joined_at < $singer2->joined_at ? -1 : 1;
            });

        $empty_dobs = Singer::query()
            ->with('user')
            ->emptyDobs()
            ->count();

        $events = Event::query()
            ->with(['my_rsvp'])
            ->where('call_time', '>', today())
            ->where('call_time', '<', today()->addMonth())
            ->orderBy('call_time')
            ->get();

        $songs = Song::whereHas('status', static function (Builder $query) {
            return $query->where('title', 'Learning');
        })
            ->orderBy('title')
            ->get()
            ->each->append('my_learning');

        return Inertia::render('Dash/Show', [
            'events' => $events->values(),
            'songs' => $songs->values(),
            'birthdays' => $birthdays->values(),
            'emptyDobs' => $empty_dobs,
            'memberversaries' => $memberversaries->values(),
        ]);
    }
}

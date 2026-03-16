<?php

namespace App\Http\Controllers;

use App\Http\Requests\PollRequest;
use App\Models\Membership;
use App\Models\Poll;
use App\Models\PollOption;
use App\Models\Ensemble;
use App\Notifications\PollCreated;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;
use Inertia\Response;

class PollController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Poll::class);
    }

    public function index(): Response
    {
        $query = Poll::query()
            ->ensembleRestricted()
            ->with(['options', 'ensembles'])
            ->withCount('votes');

        /** @var LengthAwarePaginator $pagination */
        $pagination = QueryBuilder::for($query)
            ->allowedFilters([
                AllowedFilter::partial('title'),
                AllowedFilter::callback('status', function (Builder $query, $value) {
                    if ($value === 'open') {
                        $query->where('is_closed', false)
                            ->where(function (Builder $query) {
                                $query->whereNull('close_at')
                                    ->orWhere('close_at', '>', now());
                            });
                    } elseif ($value === 'closed') {
                        $query->where(function (Builder $query) {
                            $query->where('is_closed', true)
                                ->orWhere('close_at', '<=', now());
                        });
                    }
                }),
                AllowedFilter::exact('ensembles.id'),
            ])
            ->allowedSorts([
                'title',
                'created_at',
                'votes_count',
                'close_at',
            ])
            ->defaultSort('-created_at')
            ->paginate(15)
            ->appends(request()->query());

        return Inertia::render('Polls/Index', [
            'polls' => $pagination->getCollection(),
            'pagination' => $pagination,
            'ensembles' => Ensemble::ensembleRestricted()->get()->values(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Polls/Create', [
            'ensembles' => Ensemble::all(),
        ]);
    }

    public function store(PollRequest $request): RedirectResponse
    {
        $poll = Poll::create($request->safe()->except(['options', 'send_notification', 'ensemble_ids']));

        $options = collect($request->input('options', []))
            ->map(fn ($label) => ['label' => $label]);
        $poll->options()->createMany($options->all());

        $poll->ensembles()->sync($request->input('ensemble_ids', []));

        if ($request->input('send_notification')) {
            $members = Membership::active()->with('user');

            if ($request->filled('ensemble_ids') && count($request->input('ensemble_ids')) > 0) {
                $members->whereHas('enrolments', function ($query) use ($request) {
                    $query->whereIn('ensemble_id', $request->input('ensemble_ids'));
                });
            }

            Notification::send($members->get()->pluck('user'), new PollCreated($poll));
        }

        return redirect()->route('polls.show', [$poll])->with(['status' => 'Poll created.']);
    }

    public function show(Poll $poll): Response
    {
        $poll->load(['options' => fn ($q) => $q->withCount('votes'), 'ensembles']);

        $membership = auth()->user()?->memberships()->firstWhere('tenant_id', '=', $poll->tenant_id);
        $myVoteOptionIds = [];
        if ($membership) {
            $myVoteOptionIds = $poll->votes()
                ->where('membership_id', $membership->id)
                ->pluck('poll_option_id')
                ->toArray();
        }

        return Inertia::render('Polls/Show', [
            'poll' => $poll,
            'my_vote_option_ids' => $myVoteOptionIds,
        ]);
    }

    public function edit(Poll $poll): Response
    {
        $poll->load(['options', 'ensembles']);
        return Inertia::render('Polls/Edit', [
            'poll' => $poll,
            'ensembles' => Ensemble::all(),
        ]);
    }

    public function update(Poll $poll, PollRequest $request): RedirectResponse
    {
        $poll->update($request->safe()->except(['options', 'send_notification', 'ensemble_ids']));

        // Replace options with provided list
        $poll->options()->delete();
        $options = collect($request->input('options', []))
            ->map(fn ($label) => ['label' => $label]);
        $poll->options()->createMany($options->all());

        $poll->ensembles()->sync($request->input('ensemble_ids', []));

        return redirect()->route('polls.show', [$poll])->with(['status' => 'Poll updated.']);
    }

    public function destroy(Poll $poll): RedirectResponse
    {
        $poll->delete();
        return redirect()->route('polls.index')->with(['status' => 'Poll deleted.']);
    }

    public function vote(Request $request, Poll $poll): RedirectResponse
    {
        if ($poll->is_closed) {
            return back()->with(['status' => 'Poll is closed.', 'success' => false]);
        }

        $membership = auth()->user()?->memberships()->firstWhere('tenant_id', '=', $poll->tenant_id);
        if (! $membership) {
            abort(403);
        }

        $optionIds = $request->input('option_ids');
        if (! is_array($optionIds)) {
            $optionId = $request->input('option_id');
            $optionIds = $optionId ? [$optionId] : [];
        }

        // Validate that options belong to this poll
        $validOptionIds = $poll->options()->whereIn('id', $optionIds)->pluck('id')->toArray();

        if (! $poll->can_vote_multiple && count($validOptionIds) > 1) {
            $validOptionIds = array_slice($validOptionIds, 0, 1);
        }

        // Replace votes with the new selection for idempotency
        $poll->votes()->where('membership_id', $membership->id)->delete();

        foreach ($validOptionIds as $id) {
            $poll->options()->where('id', $id)->first()?->votes()->create([
                'membership_id' => $membership->id,
            ]);
        }

        return back()->with(['status' => 'Your vote has been recorded.']);
    }

    public function close(Poll $poll): RedirectResponse
    {
        $poll->update(['is_closed' => true]);
        return back()->with(['status' => 'Poll closed.']);
    }

    public function open(Poll $poll): RedirectResponse
    {
        $poll->update([
            'is_closed' => false,
            'close_at' => null,
        ]);
        return back()->with(['status' => 'Poll re-opened.']);
    }
}

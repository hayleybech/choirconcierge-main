<?php

namespace App\Http\Controllers;

use App\Http\Requests\PollRequest;
use App\Models\Membership;
use App\Models\Poll;
use App\Models\PollOption;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PollController extends Controller
{
    public function index(): Response
    {
        /** @var LengthAwarePaginator $pagination */
        $pagination = Poll::query()
            ->with('options')
            ->withCount('votes')
            ->orderByDesc('created_at')
            ->paginate(15);

        return Inertia::render('Polls/Index', [
            'polls' => $pagination->getCollection(),
            'pagination' => $pagination,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Polls/Create');
    }

    public function store(PollRequest $request): RedirectResponse
    {
        $poll = Poll::create($request->safe()->except(['options']));

        $options = collect($request->input('options', []))
            ->map(fn ($label) => ['label' => $label]);
        $poll->options()->createMany($options->all());

        return redirect()->route('polls.show', [$poll])->with(['status' => 'Poll created.']);
    }

    public function show(Poll $poll): Response
    {
        $poll->load(['options' => fn ($q) => $q->withCount('votes')]);

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
        $poll->load('options');
        return Inertia::render('Polls/Edit', [
            'poll' => $poll,
        ]);
    }

    public function update(Poll $poll, PollRequest $request): RedirectResponse
    {
        $poll->update($request->safe()->except(['options']));

        // Replace options with provided list
        $poll->options()->delete();
        $options = collect($request->input('options', []))
            ->map(fn ($label) => ['label' => $label]);
        $poll->options()->createMany($options->all());

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
}

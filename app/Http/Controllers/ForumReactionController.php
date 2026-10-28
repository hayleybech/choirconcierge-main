<?php

namespace App\Http\Controllers;

use App\Models\ForumReply;
use App\Models\ForumReaction;
use App\Models\ForumTopic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ForumReactionController extends Controller
{
    public function storeTopic(Request $request, ForumTopic $forumTopic): RedirectResponse
    {
        $this->authorize('view', $forumTopic);
        $this->authorize('view', $forumTopic->category);
        abort_if($forumTopic->trashed(), 404);

        $data = $request->validate(['emoji' => ['required', 'string', Rule::in(ForumReaction::SUPPORTED_EMOJIS)]]);

        $forumTopic->reactions()->firstOrCreate([
            'membership_id' => $request->user()->membership->id,
            'emoji' => $data['emoji'],
        ]);

        return back();
    }

    public function destroyTopic(Request $request, ForumTopic $forumTopic): RedirectResponse
    {
        $this->authorize('view', $forumTopic);
        $this->authorize('view', $forumTopic->category);
        abort_if($forumTopic->trashed(), 404);

        $data = $request->validate(['emoji' => ['required', 'string', Rule::in(ForumReaction::SUPPORTED_EMOJIS)]]);

        $forumTopic->reactions()
            ->where('membership_id', $request->user()->membership->id)
            ->where('emoji', $data['emoji'])
            ->delete();

        return back();
    }

    public function storeReply(Request $request, ForumReply $forumReply): RedirectResponse
    {
        $this->authorize('view', $forumReply);
        $this->authorize('view', $forumReply->topic->category);
        abort_if($forumReply->trashed(), 404);

        $data = $request->validate(['emoji' => ['required', 'string', Rule::in(ForumReaction::SUPPORTED_EMOJIS)]]);

        $forumReply->reactions()->firstOrCreate([
            'membership_id' => $request->user()->membership->id,
            'emoji' => $data['emoji'],
        ]);

        return back();
    }

    public function destroyReply(Request $request, ForumReply $forumReply): RedirectResponse
    {
        $this->authorize('view', $forumReply);
        $this->authorize('view', $forumReply->topic->category);
        abort_if($forumReply->trashed(), 404);

        $data = $request->validate(['emoji' => ['required', 'string', Rule::in(ForumReaction::SUPPORTED_EMOJIS)]]);

        $forumReply->reactions()
            ->where('membership_id', $request->user()->membership->id)
            ->where('emoji', $data['emoji'])
            ->delete();

        return back();
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventActivity;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class MoveActivityController extends Controller
{
    public function __invoke(Request $request, Event $event, EventActivity $activity)
    {
        $this->authorize('update', $event);

        $request->validate(['direction' => 'required|in:up,down']);

        $activities = $event->activities()->orderBy('order')->get();

        abort_if(
            $activities->first()->is($activity) && $request->input('direction') === 'up'
            || $activities->last()->is($activity) && $request->input('direction') === 'down',
            500
        );

        $this->swap($activities, $activity, $request->input('direction'))
            ->each(fn (EventActivity $item, $key) => $item->update(['order' => $key + 1]));

        return redirect()->back()->with(['status' => 'Item moved.']);
    }

    private function swap(Collection $items, Model $target, string $direction): Collection
    {
        $targetItemIndex = $items->search(fn (Model $value) => $value->is($target));

        $swappedItemIndex = $direction === 'up' ? $targetItemIndex - 1 : $targetItemIndex + 1;

        $swappedItem = $items->get($swappedItemIndex);

        $items[$targetItemIndex] = $swappedItem;
        $items[$swappedItemIndex] = $target;

        return $items;
    }
}

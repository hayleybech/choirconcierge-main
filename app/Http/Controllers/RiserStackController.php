<?php

namespace App\Http\Controllers;

use App\Http\Requests\RiserStackRequest;
use App\Models\RiserStack;
use App\Models\Singer;
use App\Models\VoicePart;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class RiserStackController extends Controller
{
    public function index(Request $request): View
    {
        // Base query
        $stacks = RiserStack::all();

        return view('stacks.index', compact('stacks') );
    }

    public function create(): View
    {
        $voice_parts = VoicePart::with(['singers'])->get();

        return view('stacks.create', compact('voice_parts') );
    }

    public function store(RiserStackRequest $request): RedirectResponse
    {
        $stack = RiserStack::create($request->validated());

        $positions = $this->prepPositions($request);
        $stack->singers()->sync($positions);

        return redirect()->route('stacks.show', [$stack])->with(['status' => 'Riser stack created. ', ]);
    }

    public function show(RiserStack $stack): View
    {
        $stack->load('singers');

        return view('stacks.show', compact('stack' ));
    }

    public function edit(RiserStack $stack): View
    {
        // Get singers that are already on the riser stack.
        $stack->load('singers');

        // Get singers (by voice part) who are not already on the riser stack.
        $voice_parts = VoicePart::with(['singers' => static function($query) use ($stack) {
            $query->whereDoesntHave('riser_stacks', static function($query) use ($stack) {
                $query->where('riser_stack_id', '=', $stack->id);
            });
        }])->get();

        return view('stacks.edit', compact('stack', 'voice_parts'));
    }

    public function update(RiserStack $stack, RiserStackRequest $request): RedirectResponse
    {
        $stack->update($request->validated());

        $positions = $this->prepPositions($request);
        $stack->singers()->sync($positions);

        return redirect()->route('stacks.show', [$stack])->with(['status' => 'Riser stack updated. ', ]);
    }

    public function delete(RiserStack $stack): RedirectResponse
    {
        $stack->delete();

        return redirect()->route('stacks.index')->with(['status' => 'Riser stack deleted. ', ]);
    }

    /**
     * Takes the crappy array format I sent the controller from Vue,
     * and turns it into a format compatible with sync().
     *
     * @todo Convert the riser position data within the Vue component.
     *
     * @param RiserStackRequest $request
     *
     * @return array
     */
    private function prepPositions(RiserStackRequest $request): array
    {
        $position_data = json_decode( $request->validated()['singer_positions'] );
        $positions = [];
        foreach($position_data as $item) {
            $positions[$item->id] = [
                'row'    => $item->position->row,
                'column' => $item->position->column,
            ];
        }
        return $positions;
    }
}

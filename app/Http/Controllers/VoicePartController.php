<?php

namespace App\Http\Controllers;

use App\Models\VoicePart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VoicePartController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(VoicePart::class, 'voice_part');
    }

    public function index(): Response
    {
        return Inertia::render('VoiceParts/Index', [
            'parts' => VoicePart::withCount('enrolments as singers_count')->get()->values(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('VoiceParts/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        VoicePart::create($request->validate([
            'title' => 'required|max:255',
            'colour' => 'required|max:255',
        ]));

        return redirect()
            ->route('voice-parts.index')
            ->with(['status' => 'Voice part created.']);
    }

    public function edit(VoicePart $voice_part): Response
    {
	    $voice_part->can = [
		    'update_voice_part' => auth()->user()?->can('update', $voice_part),
		    'delete_voice_part' => auth()->user()?->can('delete', $voice_part),
	    ];

        return Inertia::render('VoiceParts/Edit', [
            'voice_part' => $voice_part,
        ]);
    }

    public function update(Request $request, VoicePart $voice_part): RedirectResponse
    {
        $voice_part->update($request->validate([
            'title' => 'required|max:255',
            'colour' => 'required|max:255',
        ]));

        return redirect()
            ->route('voice-parts.index')
            ->with(['status' => 'Voice part saved.']);
    }

    public function destroy(VoicePart $voice_part): RedirectResponse
    {
        $voice_part->delete();

        return redirect()
            ->route('voice-parts.index')
            ->with(['status' => 'Voice part deleted.']);
    }
}

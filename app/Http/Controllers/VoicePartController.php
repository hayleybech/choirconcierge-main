<?php

namespace App\Http\Controllers;

use App\Models\VoicePart;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VoicePartController extends Controller
{
	public function __construct()
	{
		$this->authorizeResource(VoicePart::class, 'voice_part');
	}

	public function index(): View
	{
		$parts = VoicePart::all();
		return view('voice-parts.index')->with(compact('parts'));
	}

	public function create(): View
	{
		return view('voice-parts.create');
	}

	public function store(Request $request): RedirectResponse
	{
		$data = $request->validate([
			'title' => 'required|max:255',
			'colour' => 'required|max:255',
		]);
		$part = VoicePart::create($data);
		return redirect()
			->route('voice-parts.show', $part)
			->with(['status' => 'Voice part created.']);
	}

	public function show(VoicePart $voice_part): View
	{
		return view('voice-parts.show')->with(compact('voice_part'));
	}

	public function edit(VoicePart $voice_part): View
	{
		return view('voice-parts.edit')->with(compact('voice_part'));
	}
	public function update(Request $request, VoicePart $voice_part): RedirectResponse
	{
		$data = $request->validate([
			'title' => 'required|max:255',
			'colour' => 'required|max:255',
		]);
		$voice_part->update($data);

		return redirect()
			->route('voice-parts.show', $voice_part)
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

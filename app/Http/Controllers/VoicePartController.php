<?php

namespace App\Http\Controllers;

use App\Models\VoicePart;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VoicePartController extends Controller
{
    public function index(): View
    {
        $parts = VoicePart::all();
        return view('voice-parts.index')->with(compact('parts'));
    }
}

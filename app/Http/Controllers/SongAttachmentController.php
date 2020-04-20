<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\SongAttachment;
use App\Models\SongAttachmentCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SongAttachmentController extends Controller
{
    public function store(Song $song, Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title'             => 'required|max:255',
            'category'          => 'required|exists:song_attachment_categories,id',
            'attachment_upload' => 'required|file'
        ]);

        $attachment = SongAttachment::create([
            'title'             => $data['title'],
            'song_id'           => $song->id,
            'category_id'       => $data['category'],
            'file'              => $request->file('attachment_upload'),
        ]);

        return redirect()->route('songs.show', [$song])->with(['status' => 'Attachment added. ', ]);
    }

    public function delete(Song $song, SongAttachment $attachment): RedirectResponse
    {
        $attachment->delete();

        return redirect()->route('songs.show', [$song])->with(['status' => 'Attachment deleted. ', ]);
    }
}

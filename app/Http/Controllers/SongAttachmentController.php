<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSongAttachment;
use App\Models\Song;
use App\Models\SongAttachment;
use Illuminate\Http\RedirectResponse;

class SongAttachmentController extends Controller
{
    public function store(Song $song, StoreSongAttachment $request): RedirectResponse
    {
        $data = $request->validated();

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

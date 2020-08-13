<?php

namespace App\Http\Controllers;

use App\Http\Requests\SongAttachmentRequest;
use App\Models\Song;
use App\Models\SongAttachment;
use Illuminate\Http\RedirectResponse;

class SongAttachmentController extends Controller
{
    public function show(Song $song, SongAttachment $attachment)
    {
        $this->authorize('view', $song);

        return response()->download( $attachment->path );
    }

    public function store(Song $song, SongAttachmentRequest $request): RedirectResponse
    {
        $this->authorize('update', $song);

        $data = $request->validated();

        $attachment = SongAttachment::create([
            'title'             => '',
            'song_id'           => $song->id,
            'category_id'       => $data['category'],
            'file'              => $request->file('attachment_upload'),
        ]);

        return redirect()->route('songs.show', [$song])->with(['status' => 'Attachment added. ', ]);
    }

    public function destroy(Song $song, SongAttachment $attachment): RedirectResponse
    {
        $this->authorize('update', $song);

        $attachment->delete();

        return redirect()->route('songs.show', [$song])->with(['status' => 'Attachment deleted. ', ]);
    }
}

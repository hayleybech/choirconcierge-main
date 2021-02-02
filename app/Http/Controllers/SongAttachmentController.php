<?php

namespace App\Http\Controllers;

use App\Http\Requests\SongAttachmentRequest;
use App\Models\Song;
use App\Models\SongAttachment;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SongAttachmentController extends Controller
{
    public function show(Song $song, SongAttachment $attachment): BinaryFileResponse
    {
        $this->authorize('view', $song);

        return response()->download( $attachment->path );
    }

    public function store(Song $song, SongAttachmentRequest $request): RedirectResponse
    {
        $this->authorize('update', $song);

        $data = $request->validated();

        $files = $request->file('attachment_uploads') ;
        foreach($files as $file){
            SongAttachment::create([
                'title'             => '',
                'song_id'           => $song->id,
                'category_id'       => $data['category'],
                'file'              => $file,
            ]);
        }

        return redirect()->route('songs.show', [$song])->with(['status' => 'Attachment(s) added. ', ]);
    }

    public function destroy(Song $song, SongAttachment $attachment): RedirectResponse
    {
        $this->authorize('update', $song);

        $attachment->delete();

        return redirect()->route('songs.show', [$song])->with(['status' => 'Attachment deleted. ', ]);
    }
}

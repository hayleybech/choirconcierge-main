<?php

namespace App\Http\Controllers;

use App\Http\Requests\SongAttachmentRequest;
use App\Models\Song;
use App\Models\SongAttachment;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SongAttachmentController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Song::class);
    }

    public function show(Song $song, SongAttachment $attachment): BinaryFileResponse
    {
        return response()->download($attachment->path);
    }

    public function store(Song $song, SongAttachmentRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $files = $request->file('attachment_uploads');
        if(is_countable($files)) {
            foreach ($files as $file) {
                SongAttachment::create([
                    'title' => '',
                    'song_id' => $song->id,
                    'type' => $data['type'],
                    'file' => $file,
                ]);
            }
        } else {
            SongAttachment::create([
                'title' => $data['title'],
                'song_id' => $song->id,
                'type' => $data['type'],
                'url' => $data['url'],
            ]);
        }

        return redirect()
            ->route('songs.show', [$song])
            ->with(['status' => 'Attachment(s) added. ']);
    }

    public function destroy(Song $song, SongAttachment $attachment): RedirectResponse
    {
        $attachment->delete();

        return redirect()
            ->route('songs.show', [$song])
            ->with(['status' => 'Attachment deleted. ']);
    }
}

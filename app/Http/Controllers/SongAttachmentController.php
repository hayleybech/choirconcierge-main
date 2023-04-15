<?php

namespace App\Http\Controllers;

use App\Http\Requests\SongAttachmentRequest;
use App\Models\Song;
use App\Models\SongAttachment;
use App\Rules\FileDoesntExist;
use App\Rules\Filename;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
                    'filepath' => $file->getClientOriginalName(),
                ]);
            }
        } else {
            SongAttachment::create([
                'title' => $data['title'],
                'song_id' => $song->id,
                'type' => $data['type'],
                'filepath' => $data['url'],
            ]);
        }

        return redirect()
            ->route('songs.show', [$song])
            ->with(['status' => 'Attachment(s) added. ']);
    }

    public function update(Song $song, SongAttachment $attachment, Request $request): RedirectResponse
    {
        $data = $request->validate([
            'filename' => [
                'bail', // Abort immediately on first failure. Abort if directory to prevent user from abusing "file exists" validation in any directory.
                'max:255',
                new Filename,
                new FileDoesntExist('public', $attachment->getPathSong()),
            ]
        ]);

        $old_name = $attachment->filepath;

        $attachment->update([
            'filepath' => $data['filename'],
        ]);

        Storage::disk('public')
            ->move(
                'songs/'.$song->id.'/'.$old_name,
                'songs/'.$song->id.'/'.$attachment->filepath,
            );

        return redirect()
            ->back()
            ->with(['status' => 'Attachment renamed.']);
    }

    public function destroy(Song $song, SongAttachment $attachment): RedirectResponse
    {
        $attachment->delete();

        return redirect()
            ->route('songs.show', [$song])
            ->with(['status' => 'Attachment deleted. ']);
    }
}

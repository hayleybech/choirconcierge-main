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
        $request->validate([
            'title'             => 'required|max:255',
            'category'       => 'required|exists:song_attachment_categories,id',
            'attachment_upload' => 'required|file'
        ]);

        $attachment = new SongAttachment();
        $attachment->title = $request->title;
        $attachment->category_id = $request->category;

        // Save file details
        $file_data = $request->file('attachment_upload');
        $file_name = $file_data->getClientOriginalName();
        $attachment->filepath = $file_name;

        // Associate category
        /*
        $category = SongAttachmentCategory::find($request->category);
        $category->attachments()->save($attachment);
        */

        // Attach song
        $song->attachments()->save($attachment);

        $attachment->save();

        // Upload file
        Storage::disk('public')->makeDirectory( SongAttachment::getPathSongs() );
        Storage::disk('public')->makeDirectory( $attachment->getPathSong() );

        if (Storage::disk('public')->exists( $attachment->getPath() )) {
            Storage::disk('public')->delete( $attachment->getPath() );
        }

        Storage::disk('public')->putFileAs( $attachment->getPathSong(), $file_data, $file_name );

        return redirect()->route('songs.show', [$song])->with(['status' => 'Attachment added. ', ]);
    }

    public function delete(Song $song, SongAttachment $attachment): RedirectResponse
    {
        $attachment->delete();

        return redirect()->route('songs.show', [$song])->with(['status' => 'Attachment deleted. ', ]);
    }
}

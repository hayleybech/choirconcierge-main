<?php

namespace App\Http\Controllers;

use App\Song;
use App\SongAttachment;
use App\SongAttachmentCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SongAttachmentController extends Controller
{
    public function store($songId, Request $request) {
        $request->validate([
            'title'             => 'required|max:255',
            'category_id'       => 'required|exists:song_attachment_category,id',
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
        $song = Song::find($songId);
        $song->attachments()->save($attachment);

        $attachment->save();

        // Upload file
        Storage::disk('public')->makeDirectory( SongAttachment::getPathSongs() );
        Storage::disk('public')->makeDirectory( $attachment->getPathSong() );

        if (Storage::disk('public')->exists( $attachment->getPath() )) {
            Storage::disk('public')->delete( $attachment->getPath() );
        }

        Storage::disk('public')->putFileAs( $attachment->getPathSong(), $file_data, $file_name );

        return redirect()->route('songs.show', [$songId])->with(['category' => 'Attachment added. ', ]);
    }

    public function delete($songId, $attachmentId) {
        $attachment = SongAttachment::find($attachmentId);

        if (Storage::disk('public')->exists( $attachment->getPath() )) {
            Storage::disk('public')->delete( $attachment->getPath() );
        }

        $attachment->delete();

        return redirect()->route('songs.show', [$songId])->with(['category' => 'Attachment deleted. ', ]);
    }
}

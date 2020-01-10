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
        $attachment = new SongAttachment();
        $attachment->title = $request->title;
        $attachment->category_id = $request->category;

        // Upload file
        $file_data = $request->file('attachment_upload');
        $file_name = $file_data->getClientOriginalName();
        $path_songs = 'songs';
        $path_song = $path_songs.'/'.$songId;
        $path_file = $path_song.'/'.$file_name;

        Storage::disk('public')->makeDirectory($path_songs);
        Storage::disk('public')->makeDirectory($path_song);

        if (Storage::disk('public')->exists($path_file)) {
            Storage::disk('public')->delete($path_file);
        }

        Storage::disk('public')->putFileAs( $path_song, $file_data, $file_name );
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

        return redirect()->route('songs.show', [$songId])->with(['category' => 'Attachment added. ', ]);
    }
}

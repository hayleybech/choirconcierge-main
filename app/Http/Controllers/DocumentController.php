<?php

namespace App\Http\Controllers;

use App\Http\Requests\DocumentRequest;
use App\Models\Document;
use App\Models\Folder;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param Folder $folder
     * @param DocumentRequest $request
     * @return RedirectResponse
     */
    public function store(Folder $folder, DocumentRequest $request): RedirectResponse
    {
        $files = $request->file('document_uploads') ;
        foreach($files as $file) {
            $folder->documents()->create([
                'document_upload' => $file
            ]);
        }

        return redirect()->back()->with(['status' => 'Document added. ', ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Document $document
     *
     * @return BinaryFileResponse
     */
    public function show(Folder $folder, Document $document)
    {
        return response()->download( $document->path );
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param Folder $folder
     * @param Document $document
     * @return RedirectResponse
     * @throws Exception
     */
    public function destroy(Folder $folder, Document $document): RedirectResponse
    {
        $document->delete();

        return redirect()->back()->with(['status' => 'Document deleted. ', ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\DocumentRequest;
use App\Models\Document;
use App\Models\Folder;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Document::class, 'document');
    }

    public function store(Folder $folder, DocumentRequest $request): RedirectResponse
    {
        $files = $request->file('document_uploads');
        foreach ($files as $file) {
            $folder->documents()->create([
                'document_upload' => $file,
            ]);
        }

        return redirect()
            ->back()
            ->with(['status' => 'Document added. ']);
    }

    public function show(Folder $folder, Document $document): BinaryFileResponse
    {
        return response()->download($document->path);
    }

    public function destroy(Folder $folder, Document $document): RedirectResponse
    {
        $document->delete();

        return redirect()
            ->back()
            ->with(['status' => 'Document deleted. ']);
    }
}

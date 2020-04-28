<?php

namespace App\Http\Controllers;

use App\Http\Requests\DocumentRequest;
use App\Models\Document;
use App\Models\Folder;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Folder $folder
     * @param DocumentRequest $request
     * @return RedirectResponse
     */
    public function store(Folder $folder, DocumentRequest $request): RedirectResponse
    {
        $folder->documents()->create($request->validated());

        return redirect()->route('folders.show', [$folder])->with(['status' => 'Document added. ', ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function show(Document $document)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function edit(Document $document)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Document $document)
    {
        //
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

        return redirect()->route('folders.show', [$folder])->with(['status' => 'Document deleted. ', ]);
    }
}

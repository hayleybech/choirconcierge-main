<?php

namespace App\Http\Controllers;

use App\Http\Requests\FolderRequest;
use App\Models\Folder;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FolderController extends Controller
{
	public function __construct()
	{
		$this->authorizeResource(Folder::class, 'folder');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return View
	 */
	public function index(): View
	{
		$folders = Folder::with([
			'documents' => static function ($query) {
				$query->orderBy('title'); // documents by document title
			},
		])
			->orderBy('title')
			->get(); // folders by folder title

		return view('folders.index', compact('folders'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return View
	 */
	public function create(): View
	{
		return view('folders.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param FolderRequest $request
	 * @return RedirectResponse
	 */
	public function store(FolderRequest $request): RedirectResponse
	{
		$folder = Folder::create($request->validated());

		return redirect()
			->route('folders.index')
			->with(['status' => 'Folder created. ']);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param Folder $folder
	 * @return View
	 */
	public function show(Folder $folder): View
	{
		return view('folders.show', compact('folder'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param Folder $folder
	 * @return View
	 */
	public function edit(Folder $folder): View
	{
		return view('folders.edit', compact('folder'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param FolderRequest $request
	 * @param Folder $folder
	 * @return RedirectResponse
	 */
	public function update(FolderRequest $request, Folder $folder): RedirectResponse
	{
		$folder->update($request->validated());

		return redirect()
			->route('folders.index')
			->with(['status' => 'Folder updated. ']);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Folder $folder
	 * @return RedirectResponse
	 */
	public function destroy(Folder $folder): RedirectResponse
	{
		$folder->delete();

		return redirect()
			->route('folders.index')
			->with(['status' => 'Folder deleted.']);
	}
}

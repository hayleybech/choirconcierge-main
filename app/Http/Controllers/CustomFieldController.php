<?php

namespace App\Http\Controllers;

use App\Models\CustomField;
use App\Http\Requests\StoreCustomFieldRequest;
use Illuminate\Http\RedirectResponse;

class CustomFieldController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(CustomField::class, 'custom_field');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomFieldRequest $request): RedirectResponse
    {
        CustomField::create($request->validated());

        return redirect()
            ->back()
            ->with(['status' => 'Custom Field created. ']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CustomField $customField): RedirectResponse
    {
        $customField->delete();

        return redirect()
            ->back()
            ->with(['status' => 'Custom Field deleted. ']);
    }
}

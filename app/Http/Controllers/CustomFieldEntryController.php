<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomFieldEntryRequest;
use App\Models\CustomFieldEntry;
use App\Models\Membership;
use Illuminate\Http\RedirectResponse;

class CustomFieldEntryController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(CustomFieldEntry::class, 'entry');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CustomFieldEntryRequest $request, Membership $singer): RedirectResponse
    {
        $singer->customFields()->attach($request->customFieldId, ['value' => $request->value]);

        return redirect()
            ->back()
            ->with(['status' => 'Entry saved. ']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CustomFieldEntryRequest $request, Membership $singer, CustomFieldEntry $entry): RedirectResponse
    {
        $entry->update(['value' => $request->value]);

        return redirect()
            ->back()
            ->with(['status' => 'Entry saved. ']);
    }
}

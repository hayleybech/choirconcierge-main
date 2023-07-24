<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditEnrolmentRequest;
use App\Models\Enrolment;
use App\Models\Membership;
use Illuminate\Http\RedirectResponse;

class EnrolmentController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Enrolment::class, 'enrolment');
    }

    public function update(Membership $singer, Enrolment $enrolment, EditEnrolmentRequest $request): RedirectResponse
    {
        $enrolment->update($request->safe()->all());

        return redirect()->back()->with(['status' => 'Enrolment details saved']);
    }

    public function destroy(Membership $singer, Enrolment $enrolment): RedirectResponse {
        $enrolment->delete();

        return redirect()->back()->with(['status' => 'Enrolment deleted.']);
    }
}

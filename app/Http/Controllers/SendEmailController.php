<?php

namespace App\Http\Controllers;

use App\Models\UserGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class SendEmailController extends Controller
{
    public function create(): InertiaResponse
    {
//        $this->authorize('send', Song::class);

        return Inertia::render('MailingLists/SendEmail', [
            'lists' => UserGroup::with('tenant')->get()->values(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        //        $this->authorize('send', Song::class);

        $request->validate([
            'list' => ['required', 'exists:user_groups,id'],
            'subject' => ['required', 'max:255'],
            'body' => ['required', 'max:5000'],
        ]);


    }
}

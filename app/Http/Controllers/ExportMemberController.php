<?php

namespace App\Http\Controllers;

use App\Exports\MembersExport;
use App\Models\Membership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportMemberController extends Controller
{
    public function __invoke(Request $request): BinaryFileResponse
    {
	    abort_if(! Auth::user()?->isSuperAdmin && ! Auth::user()?->can('create', Membership::class), 403);

		return (new MembersExport)->download('members.csv');
    }
}

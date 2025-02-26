<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\SingerCategory;
use App\Models\User;
use App\Models\VoicePart;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $users = User::with('roles')->get();
        $roles_all = Role::all();

        return view('users.index', compact('users', 'roles_all'));
    }

    public function addRoles(Request $request, $userid): RedirectResponse
    {
        $user = User::find($userid);
        $roles = $request->input('roles');

        $user->addRoles($roles);

        return redirect('/users');
    }

    public function detachRole($userid, $role): RedirectResponse
    {
        $user = User::find($userid);
        $user->detachRole($role);

        return redirect('/users');
    }

    public function findUsers(Request $request): JsonResponse
    {
        $term = trim($request->q);

        if (empty($term)) {
            return Response::json([]);
        }

        $formatted_results = [];
        $users = User::whereRaw("CONCAT(first_name, ' ', last_name) LIKE '%$term%'")
            ->limit(5)
            ->get();

        foreach ($users as $user) {
            $role_string = '';
            if ($user->membership) {
                $role_string = $user->membership->roles->count() ? ' ['.$user->membership->roles->implode('name', ', ').']' : '';
            }

            $formatted_results[] = [
                'id' => $user->id,
                'text' => $user->name.' ('.$user->email.')'.$role_string,
            ];
        }

        return Response::json($formatted_results);
    }

    public function findRoles(Request $request): JsonResponse
    {
        $term = trim($request->q);

        if (empty($term)) {
            return Response::json([]);
        }

        $formatted_results = [];
        $roles = Role::where('name', 'like', "%$term%")
            ->limit(5)
            ->get();

        foreach ($roles as $role) {
            $formatted_results[] = [
                'id' => $role->id,
                'text' => $role->name,
            ];
        }

        return Response::json($formatted_results);
    }

    public function findVoiceParts(Request $request): JsonResponse
    {
        $term = trim($request->q);

        if (empty($term)) {
            return Response::json([]);
        }

        $formatted_results = [];
        $parts = VoicePart::where('title', 'like', "%$term%")
            ->limit(5)
            ->get();

        foreach ($parts as $part) {
            $formatted_results[] = [
                'id' => $part->id,
                'text' => $part->title,
            ];
        }

        return Response::json($formatted_results);
    }

    public function findSingerCategories(Request $request): JsonResponse
    {
        $term = trim($request->q);

        if (empty($term)) {
            return Response::json([]);
        }

        $formatted_results = [];
        $cats = SingerCategory::where('name', 'like', "%$term%")
            ->limit(5)
            ->get();

        foreach ($cats as $cat) {
            $formatted_results[] = [
                'id' => $cat->id,
                'text' => $cat->name,
            ];
        }

        return Response::json($formatted_results);
    }
}

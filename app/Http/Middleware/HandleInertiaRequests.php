<?php

namespace App\Http\Middleware;

use App\Models\Attendance;
use App\Models\Document;
use App\Models\Event;
use App\Models\Folder;
use App\Models\RiserStack;
use App\Models\Role;
use App\Models\Singer;
use App\Models\Song;
use App\Models\Task;
use App\Models\Tenant;
use App\Models\UserGroup;
use App\Models\VoicePart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request)
    {
        return array_merge(parent::share($request), [
            'flash' => [
                'message' => fn () => $request->session()->get('status'),
            ],
            'can' => [
                'view_dash' => true,
                'list_singers' => auth()->user()?->can('viewAny', Singer::class),
                'create_singer' => auth()->user()?->can('create', Singer::class),
                'create_voice_part' => auth()->user()?->can('create', VoicePart::class),
                'list_voice_parts' => auth()->user()?->can('viewAny', VoicePart::class),
                'list_roles' => auth()->user()?->can('viewAny', Role::class),
                'create_role' => auth()->user()?->can('create', Role::class),
                'list_songs' => auth()->user()?->can('viewAny', Song::class),
                'create_song' => auth()->user()?->can('create', Song::class),
                'list_events' => auth()->user()?->can('viewAny', Event::class),
                'create_event' => auth()->user()?->can('create', Event::class),
                'list_attendances' => auth()->user()?->can('viewAny', Attendance::class),
                'create_attendance' => auth()->user()?->can('create', Attendance::class),
                'list_folders' => auth()->user()?->can('viewAny', Folder::class),
                'create_folder' => auth()->user()?->can('create', Folder::class),
                'update_folder' => auth()->user()?->can('update', Folder::class),
                'delete_folder' => auth()->user()?->can('delete', Folder::class),
                'create_document' => auth()->user()?->can('create', Document::class),
                'update_document' => auth()->user()?->can('update', Document::class),
                'delete_document' => auth()->user()?->can('delete', Document::class),
                'list_stacks' => auth()->user()?->can('viewAny', RiserStack::class),
                'create_stack' => auth()->user()?->can('create', RiserStack::class),
                'list_groups' => auth()->user()?->can('viewAny', UserGroup::class),
                'create_group' => auth()->user()?->can('create', UserGroup::class),
                'create_broadcast' => auth()->user()?->can('createBroadcast', UserGroup::class),
                'list_tasks' => auth()->user()?->can('viewAny', Task::class),
                'create_task' => auth()->user()?->can('create', Task::class),
                'impersonate' => auth()->user()?->singer?->hasRole('Admin'),
	            'manage_finances' => Gate::allows('update-fees'),
            ],
            'googleApiKey' => config('services.google.key'),
            'tenant' => tenancy()?->tenant,
            'user' => auth()->user(),
            'impersonationActive' => session()->has('impersonation:active'),
            'userChoirs' => auth()->user()
                ?->singers()
                ->withoutTenancy()
                ->with('tenant.domains')
                ->get()
                ->map(fn ($singer) => $singer->tenant),
        ]);
    }
}

<?php

namespace App\Http\Middleware;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\Folder;
use App\Models\RiserStack;
use App\Models\Role;
use App\Models\Singer;
use App\Models\Song;
use App\Models\Task;
use App\Models\UserGroup;
use App\Models\VoicePart;
use Illuminate\Http\Request;
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
            'can' => [
                'view_dash' => true,
                'list_singers' => auth()->user()?->can('viewAny', Singer::class),
                'create_singer' => auth()->user()?->can('create', Singer::class),
                'list_voice_parts' => auth()->user()?->can('viewAny', VoicePart::class),
                'list_roles' => auth()->user()?->can('viewAny', Role::class),
                'list_songs' => auth()->user()?->can('viewAny', Song::class),
                'create_song' => auth()->user()?->can('create', Song::class),
                'list_events' => auth()->user()?->can('viewAny', Event::class),
                'create_event' => auth()->user()?->can('create', Event::class),
                'list_attendances' => auth()->user()?->can('viewAny', Attendance::class),
                'create_attendance' => auth()->user()?->can('create', Attendance::class),
                'list_folders' => auth()->user()?->can('viewAny', Folder::class),
                'create_folder' => auth()->user()?->can('create', Folder::class),
                'list_stacks' => auth()->user()?->can('viewAny', RiserStack::class),
                'create_stack' => auth()->user()?->can('create', RiserStack::class),
                'list_groups' => auth()->user()?->can('viewAny', UserGroup::class),
                'create_group' => auth()->user()?->can('create', UserGroup::class),
                'list_tasks' => auth()->user()?->can('viewAny', Task::class),
                'create_task' => auth()->user()?->can('create', Task::class),
                'impersonate' => auth()->user()?->singer?->hasRole('Admin'),
            ],
            'googleApiKey' => config('services.google.key'),
            'tenant' => tenancy()?->tenant,
            'user' => auth()->user(),
            'impersonationActive' => session()->has('impersonation:active'),
        ]);
    }
}

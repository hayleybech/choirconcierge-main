<?php

namespace App\Http\Middleware;

use App\Models\Attendance;
use App\Models\CustomField;
use App\Models\CustomFieldEntry;
use App\Models\Document;
use App\Models\Event;
use App\Models\Folder;
use App\Models\MailLog;
use App\Models\RiserStack;
use App\Models\Role;
use App\Models\Membership;
use App\Models\Poll;
use App\Models\Song;
use App\Models\Task;
use App\Models\Tenant;
use App\Models\UserGroup;
use App\Models\VoicePart;
use App\Navigation\Navigation;
use App\Navigation\UserNavigation;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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
     * @param Request $request
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
     * @param Request $request
     * @return array
     */
    public function share(Request $request)
    {
        $user = auth()->user();
        $tenantId = tenancy()->initialized ? tenant()->id : null;

        return array_merge(parent::share($request), [
            'notifications' => fn () => $this->getNotifications($request, $this->getUserChoirs()),
            'flash' => [
                'message' => fn() => $request->session()->get('status'),
                'success' => fn() => $request->session()->get('success', true),
                'preview' => fn() => $request->session()->get('preview'),
            ],
            'can' => fn () => [
                'view_dash' => true,
                'list_singers' => $user?->can('viewAny', Membership::class),
                'create_singer' => $user?->can('create', Membership::class),
                'update_singer' => $user?->can('update', [Membership::class, null]),
                'delete_singer' => $user?->can('delete', [Membership::class, null]),
                'import_singers' => $user?->isSuperAdmin
                    || $user?->membership?->hasRole('Admin')
                    || $user?->can('create', Membership::class),
                'export_singers' => $user?->isSuperAdmin
                    || $user?->membership?->hasRole('Admin')
                    || $user?->can('create', Membership::class),
                'create_voice_part' => $user?->can('create', VoicePart::class),
                'list_voice_parts' => $user?->can('viewAny', VoicePart::class),
                'list_roles' => $user?->can('viewAny', Role::class),
                'create_role' => $user?->can('create', Role::class),
                'create_custom_field' => $user?->can('create', CustomField::class),
                'list_custom_field_entries' => $user?->can('viewAny', CustomFieldEntry::class),
                'update_custom_field_entries' => $user?->can('update', CustomFieldEntry::class),
                'list_songs' => $user?->can('viewAny', Song::class),
                'create_song' => $user?->can('create', Song::class),
                'update_song' => $user?->can('update', [Song::class, null]),
                'delete_song' => $user?->can('delete', [Song::class, null]),
                'list_events' => $user?->can('viewAny', Event::class),
                'create_event' => $user?->can('create', Event::class),
                'update_event' => $user?->can('update', [Event::class, null]),
                'delete_event' => $user?->can('delete', [Event::class, null]),
                'list_attendances' => $user?->can('viewAny', Attendance::class),
                'create_attendance' => $user?->can('create', Attendance::class),
                'list_folders' => $user?->can('viewAny', Folder::class),
                'create_folder' => $user?->can('create', Folder::class),
                'update_folder' => $user?->can('update', [Folder::class, null]),
                'delete_folder' => $user?->can('delete', [Folder::class, null]),
                'create_document' => $user?->can('create', Document::class),
                'update_document' => $user?->can('update', [Document::class, null]),
                'delete_document' => $user?->can('delete', [Document::class, null]),
                'list_stacks' => $user?->can('viewAny', RiserStack::class),
                'create_stack' => $user?->can('create', RiserStack::class),
                'update_stack' => $user?->can('update', [RiserStack::class, null]),
                'delete_stack' => $user?->can('delete', [RiserStack::class, null]),
                'list_groups' => $user?->can('viewAny', UserGroup::class),
                'create_group' => $user?->can('create', UserGroup::class),
                'update_group' => $user?->can('update', [UserGroup::class, null]),
                'delete_group' => $user?->can('delete', [UserGroup::class, null]),
                'create_broadcast' => $user?->can('createBroadcast', UserGroup::class),
                'view_mail_logs' => $user?->can('viewAny', MailLog::class),
                'list_tasks' => $user?->can('viewAny', Task::class),
                'create_task' => $user?->can('create', Task::class),
                'list_polls' => $user?->can('viewAny', Poll::class),
                'create_poll' => $user?->can('create', Poll::class),
                'update_poll' => $user?->can('update', [Poll::class, null]),
                'delete_poll' => $user?->can('delete', [Poll::class, null]),
                'impersonate' => $user?->isSuperAdmin || $user?->membership?->hasRole('Admin'),
                'manage_finances' => Gate::allows('update-fees'),
                'update_tenant' => $user?->can('update', [Tenant::class, null]),
                'list_tenants' => $user?->isSuperAdmin,

                'view_member_history' => $user?->isSuperAdmin,
            ],
            'googleApiKey' => config('services.google.key'),
            'tenant' => fn () => tenancy()?->tenant?->loadMissing('ensembles')->append('billing_status'), // billing status needed for global alerts
            'user' => $user,
            'impersonationActive' => session()->has('impersonation:active'),
            'userChoirs' => fn () => $this->getUserChoirs(),
            'isWebView' => $request->attributes->get('isWebView'),
            'navigation' => fn () => (new Navigation())->get($tenantId),
            'userNavigation' => fn () => (new UserNavigation())->get($tenantId),
        ]);
    }

    private function getNotifications(Request $request, ?Collection $userChoirs): array
    {
        $user = auth()->user();
        if (! $user) {
            return [
                'items' => [],
                'unread_count' => 0,
            ];
        }

        $tenantId = tenancy()->initialized ? (string) tenant('id') : null;
        $filter = $request->string('notification_filter', 'all')->toString() === 'unread' ? 'unread' : 'all';
        return [
            'items' => $user->notifications()
                ->with('tenant')
                ->when($tenantId !== null, fn ($query) => $query->where('data->tenant_id', $tenantId))
                ->when($filter === 'unread', fn ($query) => $query->whereNull('read_at'))
                ->limit(50)
                ->get()
                ->each(fn ($notification) => $notification->showTenantName = $userChoirs?->count() > 1)
                ->toArray(),
            'unread_count' => $user->notifications()
                ->whereNull('read_at')
                ->when($tenantId !== null, fn ($query) => $query->where('data->tenant_id', $tenantId))
                ->count(),
            'filter' => $filter,
        ];
    }

    private function getUserChoirs()
    {
        return session()->has('impersonation:active')
            ? collect([tenant()?->loadMissing('domains')])->filter()
            : auth()->user()
                ?->memberships()
                ->withoutTenancy()
                ->with('tenant.domains')
                ->get()
                ->map(fn($singer) => $singer->tenant);
    }
}

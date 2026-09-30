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
        return array_merge(parent::share($request), [
            'notifications' => fn () => $this->getNotifications($request),
            'flash' => [
                'message' => fn() => $request->session()->get('status'),
                'success' => fn() => $request->session()->get('success', true),
                'preview' => fn() => $request->session()->get('preview'),
            ],
            'can' => [
                'view_dash' => true,
                'list_singers' => auth()->user()?->can('viewAny', Membership::class),
                'create_singer' => auth()->user()?->can('create', Membership::class),
                'update_singer' => auth()->user()?->can('update', [Membership::class, null]),
                'delete_singer' => auth()->user()?->can('delete', [Membership::class, null]),
                'import_singers' => auth()->user()?->isSuperAdmin
                    || auth()?->user()?->membership?->hasRole('Admin')
                    || auth()->user()?->can('create', Membership::class),
                'export_singers' => auth()->user()?->isSuperAdmin
                    || auth()?->user()?->membership?->hasRole('Admin')
                    || auth()->user()?->can('create', Membership::class),
                'create_voice_part' => auth()->user()?->can('create', VoicePart::class),
                'list_voice_parts' => auth()->user()?->can('viewAny', VoicePart::class),
                'list_roles' => auth()->user()?->can('viewAny', Role::class),
                'create_role' => auth()->user()?->can('create', Role::class),
                'create_custom_field' => auth()->user()?->can('create', CustomField::class),
                'list_custom_field_entries' => auth()->user()?->can('viewAny', CustomFieldEntry::class),
                'update_custom_field_entries' => auth()->user()?->can('update', CustomFieldEntry::class),
                'list_songs' => auth()->user()?->can('viewAny', Song::class),
                'create_song' => auth()->user()?->can('create', Song::class),
                'update_song' => auth()->user()?->can('update', [Song::class, null]),
                'delete_song' => auth()->user()?->can('delete', [Song::class, null]),
                'list_events' => auth()->user()?->can('viewAny', Event::class),
                'create_event' => auth()->user()?->can('create', Event::class),
                'update_event' => auth()->user()?->can('update', [Event::class, null]),
                'delete_event' => auth()->user()?->can('delete', [Event::class, null]),
                'list_attendances' => auth()->user()?->can('viewAny', Attendance::class),
                'create_attendance' => auth()->user()?->can('create', Attendance::class),
                'list_folders' => auth()->user()?->can('viewAny', Folder::class),
                'create_folder' => auth()->user()?->can('create', Folder::class),
                'update_folder' => auth()->user()?->can('update', [Folder::class, null]),
                'delete_folder' => auth()->user()?->can('delete', [Folder::class, null]),
                'create_document' => auth()->user()?->can('create', Document::class),
                'update_document' => auth()->user()?->can('update', [Document::class, null]),
                'delete_document' => auth()->user()?->can('delete', [Document::class, null]),
                'list_stacks' => auth()->user()?->can('viewAny', RiserStack::class),
                'create_stack' => auth()->user()?->can('create', RiserStack::class),
                'update_stack' => auth()->user()?->can('update', [RiserStack::class, null]),
                'delete_stack' => auth()->user()?->can('delete', [RiserStack::class, null]),
                'list_groups' => auth()->user()?->can('viewAny', UserGroup::class),
                'create_group' => auth()->user()?->can('create', UserGroup::class),
                'update_group' => auth()->user()?->can('update', [UserGroup::class, null]),
                'delete_group' => auth()->user()?->can('delete', [UserGroup::class, null]),
                'create_broadcast' => auth()->user()?->can('createBroadcast', UserGroup::class),
                'view_mail_logs' => auth()->user()?->can('viewAny', MailLog::class),
                'list_tasks' => auth()->user()?->can('viewAny', Task::class),
                'create_task' => auth()->user()?->can('create', Task::class),
                'list_polls' => auth()->user()?->can('viewAny', Poll::class),
                'create_poll' => auth()->user()?->can('create', Poll::class),
                'update_poll' => auth()->user()?->can('update', [Poll::class, null]),
                'delete_poll' => auth()->user()?->can('delete', [Poll::class, null]),
                'impersonate' => auth()->user()?->isSuperAdmin || auth()->user()?->membership?->hasRole('Admin'),
                'manage_finances' => Gate::allows('update-fees'),
                'update_tenant' => auth()->user()?->can('update', [Tenant::class, null]),
                'list_tenants' => auth()->user()?->isSuperAdmin,

                'view_member_history' => auth()->user()?->isSuperAdmin,
            ],
            'googleApiKey' => config('services.google.key'),
            'tenant' => tenancy()?->tenant?->loadMissing('ensembles')->append('billing_status'), // billing status needed for global alerts
            'user' => auth()->user(),
            'impersonationActive' => session()->has('impersonation:active'),
            'userChoirs' => $this->getUserChoirs(),
            'isWebView' => $request->attributes->get('isWebView'),
            'navigation' => (new Navigation())->get(tenancy()->initialized ? tenant()->id : null),
            'userNavigation' => (new UserNavigation())->get(tenancy()->initialized ? tenant()->id : null),
        ]);
    }

    private function getNotifications(Request $request): array
    {
        $user = auth()->user();

        if (! $user) {
            return [
                'items' => [],
                'unread_count' => 0,
            ];
        }

        $tenantId = tenancy()->initialized ? (string) tenant('id') : null;
        $filter = $request->string('notification_filter', 'unread')->toString();
        $notifications = $user->notifications()
            ->latest()
            ->when($filter === 'read', fn ($query) => $query->whereNotNull('read_at'))
            ->when($filter !== 'read', fn ($query) => $query->whereNull('read_at'))
            ->limit(50)
            ->get()
            ->filter(function ($notification) use ($tenantId): bool {
                if ($tenantId === null) {
                    return true;
                }

                return (string) ($notification->data['tenant_id'] ?? '') === $tenantId;
            })
            ->take(10)
            ->values();

        $unreadCount = $user->notifications()
            ->whereNull('read_at')
            ->get()
            ->filter(function ($notification) use ($tenantId): bool {
                if ($tenantId === null) {
                    return true;
                }

                return (string) ($notification->data['tenant_id'] ?? '') === $tenantId;
            })
            ->count();

        return [
            'items' => $notifications->map(fn ($notification): array => [
                'id' => $notification->id,
                'type' => $notification->data['type'] ?? class_basename($notification->type),
                'subject' => $notification->data['subject'] ?? 'Notification',
                'body' => $notification->data['body'] ?? '',
                'url' => $notification->data['url'] ?? null,
                'read_url' => tenancy()->initialized ? route('notifications.read', ['tenant' => tenant('id'), 'notification' => $notification->id]) : null,
                'toggle_url' => tenancy()->initialized ? route('notifications.update', ['tenant' => tenant('id'), 'notification' => $notification->id]) : null,
                'read_at' => $notification->read_at,
                'created_at' => $notification->created_at,
            ])->all(),
            'unread_count' => $unreadCount,
            'filter' => $filter === 'read' ? 'read' : 'unread',
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

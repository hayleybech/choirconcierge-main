const navigation = [
    { name: 'Dashboard', route: 'dash', icon: 'fa-chart-line', can: 'view_dash', showAsActiveForRoutes: ['dash'], items: [] },
    {
        name: 'Singers',
        route: 'singers.index',
        icon: 'fa-users',
        can: 'list_singers',
        showAsActiveForRoutes: ['singers.*', 'voice-parts.*', 'roles.*'],
        items: [
            { name: 'Add New', route: 'singers.create', icon: 'fa-plus-square', can: 'create_singer', showAsActiveForRoutes: ['singers.create'], },
            { name: 'Voice Parts', route: 'voice-parts.index', icon: 'fa-users-class', can: 'list_voice_parts', showAsActiveForRoutes: ['voice-parts.*'], },
            { name: 'Roles', route: 'roles.index', icon: 'fa-user-tag', can: 'list_roles', showAsActiveForRoutes: ['roles.*'], }
        ]
    },
    {
        name: 'Songs',
        route: 'songs.index',
        icon: 'fa-list-music',
        can: 'list_songs',
        showAsActiveForRoutes: ['songs.*'],
        items: [
            { name: 'Add New', route: 'songs.create', icon: 'fa-plus-square', can: 'create_song', showAsActiveForRoutes: ['singers.create'], },
            { name: 'Song Categories', route: 'song-categories.index', icon: 'tags', can: 'list_songs', showAsActiveForRoutes: ['song-categories.*'], }
        ]
    },
    {
        name: 'Events',
        route: 'events.index',
        icon: 'calendar',
        can: 'list_events',
        showAsActiveForRoutes: ['events.*'],
        items: [
            { name: 'Add New', route: 'events.create', icon: 'fa-plus-square', can: 'create_event', showAsActiveForRoutes: ['events.create'], },
            { name: 'Event Types', route: 'event-types.index', icon: 'tags', can: 'list_events', showAsActiveForRoutes: ['event-types.*'], },
            { name: 'Calendar View', route: 'events.calendar.month', icon: 'calendar-alt', can: 'list_events', showAsActiveForRoutes: ['events.calendar.month'], },
            { name: 'Attendance Report', route: 'events.reports.attendance', icon: 'fa-analytics', can: 'list_attendances', showAsActiveForRoutes: ['events.reports.attendance'], },
        ]
    },
    {
        name: 'Documents',
        route: 'folders.index',
        icon: 'fa-folders',
        can: 'list_folders',
        showAsActiveForRoutes: ['folders.*'],
        items: [
            { name: 'Add Folder', route: 'folders.create', icon: 'fa-plus-square', can: 'create_folder', showAsActiveForRoutes: ['folders.create'], },
        ]
    },
    {
        name: 'Riser Stacks',
        route: 'stacks.index',
        icon: 'fa-people-arrows',
        can: 'list_stacks',
        showAsActiveForRoutes: ['stacks'],
        items: [
            { name: 'Add New', route: 'stacks.create', icon: 'fa-plus-square', can: 'create_stack', showAsActiveForRoutes: ['stacks.create'], },
        ]
    },
    {
        name: 'Mailing Lists',
        route: 'groups.index',
        icon: 'fa-mail-bulk',
        can: 'list_groups',
        showAsActiveForRoutes: ['groups.*'],
        items: [
            { name: 'Add New', route: 'groups.create', icon: 'fa-plus-square', can: 'create_group', showAsActiveForRoutes: ['groups.create'], },
            { name: 'Send Broadcast', route: 'groups.broadcasts.create', icon: 'inbox-out', can: 'create_broadcast', showAsActiveForRoutes: ['groups.broadcasts.create'], },
        ]
    },
    {
        name: 'Onboarding',
        route: 'tasks.index',
        icon: 'fa-tasks',
        can: 'list_tasks',
        showAsActiveForRoutes: ['tasks.*'],
        items: [
            { name: 'Add New', route: 'tasks.create', icon: 'fa-plus-square', can: 'create_task', showAsActiveForRoutes: ['tasks.create'] },
        ]
    },
];

export default navigation;
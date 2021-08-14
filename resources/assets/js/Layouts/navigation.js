const navigation = [
    { name: 'Dashboard', route: 'dash', icon: 'fa-chart-line', showAsActiveForRoutes: ['dash'], items: [] },
    {
        name: 'Singers',
        route: 'singers.index',
        icon: 'fa-users',
        showAsActiveForRoutes: ['singers.*', 'voice-parts.*', 'roles.*'],
        items: [
            { name: 'All Singers', route: 'singers.index', icon: 'fa-list', showAsActiveForRoutes: ['singers.index'] },
            { name: 'Add New', route: 'singers.create', icon: 'fa-plus-square', showAsActiveForRoutes: ['singers.create'], },
            { name: 'Voice Parts', route: 'voice-parts.index', icon: 'fa-users-class', showAsActiveForRoutes: ['voice-parts.*'], },
            { name: 'Roles', route: 'roles.index', icon: 'fa-user-tag', showAsActiveForRoutes: ['roles.*'], }
        ]
    },
    {
        name: 'Songs',
        route: 'songs.index',
        icon: 'fa-list-music',
        showAsActiveForRoutes: ['songs.*'],
        items: [
            { name: 'All Songs', route: 'songs.index', icon: 'fa-list', showAsActiveForRoutes: ['singers.index'], },
            { name: 'Add New', route: 'songs.create', icon: 'fa-plus-square', showAsActiveForRoutes: ['singers.create'], },
        ]
    },
    {
        name: 'Events',
        route: 'events.index',
        icon: 'fa-calendar-alt',
        showAsActiveForRoutes: ['events.*'],
        items: [
            { name: 'All Events', route: 'events.index', icon: 'fa-list', showAsActiveForRoutes: ['events.index'], },
            { name: 'Add New', route: 'events.create', icon: 'fa-plus-square', showAsActiveForRoutes: ['events.create'], },
            { name: 'Attendance Report', route: 'events.reports.attendance', icon: 'fa-analytics', showAsActiveForRoutes: ['events.reports.attendance'], },
        ]
    },
    {
        name: 'Documents',
        route: 'folders.index',
        icon: 'fa-folders',
        showAsActiveForRoutes: ['folders.*'],
        items: [
            { name: 'All Folders', route: 'folders.index', icon: 'fa-list', showAsActiveForRoutes: ['folders.index'], },
            { name: 'Add Folder', route: 'folders.create', icon: 'fa-plus-square', showAsActiveForRoutes: ['folders.create'], },
        ]
    },
    {
        name: 'Riser Stacks',
        route: 'stacks.index',
        icon: 'fa-people-arrows',
        showAsActiveForRoutes: ['stacks'],
        items: [
            { name: 'All Stacks', route: 'stacks.index', icon: 'fa-list', showAsActiveForRoutes: ['stacks.index'], },
            { name: 'Add New', route: 'stacks.create', icon: 'fa-plus-square', showAsActiveForRoutes: ['stacks.create'], },
        ]
    },
    {
        name: 'Mailing Lists',
        route: 'groups.index',
        icon: 'fa-mail-bulk',
        showAsActiveForRoutes: ['groups.*'],
        items: [
            { name: 'All Lists', route: 'groups.index', icon: 'fa-list', showAsActiveForRoutes: ['groups.index'], },
            { name: 'Add New', route: 'groups.create', icon: 'fa-plus-square', showAsActiveForRoutes: ['groups.create'], },
        ]
    },
    {
        name: 'Onboarding',
        route: 'tasks.index',
        icon: 'fa-tasks',
        showAsActiveForRoutes: ['tasks.*'],
        items: [
            { name: 'All Tasks', route: 'tasks.index', icon: 'fa-list', showAsActiveForRoutes: ['tasks.index'], },
            { name: 'Add New', route: 'tasks.create', icon: 'fa-plus-square', showAsActiveForRoutes: ['tasks.create'], },
        ]
    },
];

export default navigation;
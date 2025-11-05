const centralNavigation = [
    { name: 'Dashboard', route: 'central.dash', icon: 'fa-chart-line', can: 'view_dash', showAsActiveForRoutes: ['central.dash'], items: [] },
    { name: 'Tenants', route: 'central.tenants.index', icon: 'building', can: 'list_tenants', showAsActiveForRoutes: ['central.tenants.*'], items: []},
    { name: 'Users', route: 'central.users.index', icon: 'users', can: 'list_tenants', showAsActiveForRoutes: ['central.users.*'], items: []},
    { name: 'Mail Logs', route: 'central.mail-logs.index', icon: 'history', can: 'list_tenants', showAsActiveForRoutes: ['central.mail-logs.*'], items: []},
];

export default centralNavigation;
const centralNavigation = [
    { name: 'Dashboard', route: 'central.dash', icon: 'fa-chart-line', can: 'view_dash', showAsActiveForRoutes: ['central.dash'], items: [] },
    { name: 'Tenants', route: 'central.tenants.index', icon: 'building', can: 'list_tenants', showAsActiveForRoutes: ['central.tenants.*'], items: []},
];

export default centralNavigation;
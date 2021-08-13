import React from 'react';
import {Link} from "@inertiajs/inertia-react";

const nav = [
    { label: 'Dashboard', route: 'dash', icon: 'fa-chart-line', items: [],  },
    {
        label: 'Singers',
        route: 'singers.index',
        icon: 'fa-users',
        on: true,
        items: [
            { label: 'All Singers', route: 'singers.index', icon: 'fa-list' },
            { label: 'Add New', route: 'singers.create', icon: 'fa-plus-square' },
            { label: 'Voice Parts', route: 'voice-parts.index', icon: 'fa-users-class' },
            { label: 'Roles', route: 'roles.index', icon: 'fa-user-tag' }
        ]
    },
    {
        label: 'Songs',
        route: 'songs.index',
        icon: 'fa-list-music',
        items: [
            { label: 'All Songs', route: 'songs.index', icon: 'fa-list' },
            { label: 'Add New', route: 'songs.create', icon: 'fa-plus-square' },
        ]
    },
    {
        label: 'Events',
        route: 'events.index',
        icon: 'fa-calendar-alt',
        items: [
            { label: 'All Events', route: 'events.index', icon: 'fa-list' },
            { label: 'Add New', route: 'events.create', icon: 'fa-plus-square' },
            { label: 'Attendance Report', route: 'events.reports.attendance', icon: 'fa-analytics' },
        ]
    },
    {
        label: 'Documents',
        route: 'folders.index',
        icon: 'fa-folders',
        items: [
            { label: 'All Folders', route: 'folders.index', icon: 'fa-list' },
            { label: 'Add Folder', route: 'folders.create', icon: 'fa-plus-square' },
        ]
    },
    {
        label: 'Riser Stacks',
        route: 'stacks.index',
        icon: 'fa-people-arrows',
        items: [
            { label: 'All Stacks', route: 'stacks.index', icon: 'fa-list' },
            { label: 'Add New', route: 'stacks.create', icon: 'fa-plus-square' },
        ]
    },
    {
        label: 'Mailing Lists',
        route: 'groups.index',
        icon: 'fa-mail-bulk',
        items: [
            { label: 'All Lists', route: 'groups.index', icon: 'fa-list' },
            { label: 'Add New', route: 'groups.create', icon: 'fa-plus-square' },
        ]
    },
    {
        label: 'Onboarding',
        route: 'tasks.index',
        icon: 'fa-tasks',
        items: [
            { label: 'All Tasks', route: 'tasks.index', icon: 'fa-list' },
            { label: 'Add New', route: 'tasks.create', icon: 'fa-plus-square' },
        ]
    },
];

const TenantLayout = ({children}) =>  (
    <div className="flex items-stretch h-full">
        <div className="w-250px h-full bg-brand-purple-dark">
            <Link href={route('dash')} className="flex py-6 px-8">
                <img src="/img/logo.svg" alt="Choir Concierge" />
            </Link>

            <Link href={route('dash')} className="flex justify-center mb-4 py-4 px-6 bg-brand-off-white">
                <img src="/img/choir-logo.png" className="h-12 w-auto" />
            </Link>

            <div className="flex flex-col">

                {nav.map((item) => (
                    <>
                    {item.on ? (
                        <div className="bg-brand-light-pink ml-3 my-2 py-4 px-6 flex flex-col rounded-l-3xl text-brand-purple-dark">
                            <Link href={route(item.route)} className="uppercase font-semibold hover:opacity-75 mb-1">
                                <i className={"fal fa-fw fa-chart-line mr-3 " + item.icon} /> {item.label}
                            </Link>
                            <>
                                {item.items.map((child) => (
                                    <Link href={route(child.route)} className="py-1 text-sm hover:opacity-75">
                                        <i className={"fal fa-fw mr-3 " + child.icon} /> {child.label}
                                    </Link>
                                ))}
                            </>
                        </div>
                    ): (
                        <div className="ml-3 py-3 px-6 flex flex-col">
                            <Link href={route(item.route)} className="text-white opacity-75 uppercase font-semibold hover:opacity-50">
                                <i className={"fal fa-fw mr-3 " + item.icon} /> {item.label}
                            </Link>
                        </div>
                    )}
                    </>
                ))}
            </div>
        </div>

        <div>{children}</div>
    </div>
);

export default TenantLayout;
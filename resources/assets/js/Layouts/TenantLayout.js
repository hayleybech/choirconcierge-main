import React from 'react';
import {Link, usePage} from "@inertiajs/inertia-react";
import Icon from "../components/Icon";

const nav = [
    { label: 'Dashboard', route: 'dash', icon: 'chart-line', items: [],  },
    {
        label: 'Singers',
        route: 'singers.index',
        icon: 'users',
        on: true,
        items: [
            { label: 'All Singers', route: 'singers.index', icon: 'list' },
            { label: 'Add New', route: 'singers.create', icon: 'plus-square' },
            { label: 'Voice Parts', route: 'voice-parts.index', icon: 'users-class' },
            { label: 'Roles', route: 'roles.index', icon: 'user-tag' }
        ]
    },
    {
        label: 'Songs',
        route: 'songs.index',
        icon: 'list-music',
        items: [
            { label: 'All Songs', route: 'songs.index', icon: 'list' },
            { label: 'Add New', route: 'songs.create', icon: 'plus-square' },
        ]
    },
    {
        label: 'Events',
        route: 'events.index',
        icon: 'calendar-alt',
        items: [
            { label: 'All Events', route: 'events.index', icon: 'list' },
            { label: 'Add New', route: 'events.create', icon: 'plus-square' },
            { label: 'Attendance Report', route: 'events.reports.attendance', icon: 'analytics' },
        ]
    },
    {
        label: 'Documents',
        route: 'folders.index',
        icon: 'folders',
        items: [
            { label: 'All Folders', route: 'folders.index', icon: 'list' },
            { label: 'Add Folder', route: 'folders.create', icon: 'plus-square' },
        ]
    },
    {
        label: 'Riser Stacks',
        route: 'stacks.index',
        icon: 'people-arrows',
        items: [
            { label: 'All Stacks', route: 'stacks.index', icon: 'list' },
            { label: 'Add New', route: 'stacks.create', icon: 'plus-square' },
        ]
    },
    {
        label: 'Mailing Lists',
        route: 'groups.index',
        icon: 'mail-bulk',
        items: [
            { label: 'All Lists', route: 'groups.index', icon: 'list' },
            { label: 'Add New', route: 'groups.create', icon: 'plus-square' },
        ]
    },
    {
        label: 'Onboarding',
        route: 'tasks.index',
        icon: 'tasks',
        items: [
            { label: 'All Tasks', route: 'tasks.index', icon: 'list' },
            { label: 'Add New', route: 'tasks.create', icon: 'plus-square' },
        ]
    },
];

const TenantLayout = ({ children }) => {
    const { tenant } = usePage().props;

    return (
        <div className="flex items-stretch h-full">
            <div className="w-250px h-full bg-brand-purple-dark">
                <Link href={route('dash')} className="flex py-6 px-8">
                    <img src="/img/vibrant/logo.svg" alt="Choir Concierge" />
                </Link>

                <Link href={route('dash')} className="flex justify-center mb-4 py-4 px-6 bg-brand-off-white">
                    <img src={tenant.logo_url} alt={tenant.choir_name} className="h-12 w-auto" />
                </Link>

                <div className="flex flex-col">

                    {nav.map((item) => (
                        <>
                            {item.on ? (
                                <div className="bg-brand-light-pink ml-3 my-2 py-4 px-6 flex flex-col rounded-l-3xl text-brand-purple-dark">
                                    <Link href={route(item.route)} className="uppercase font-semibold hover:opacity-75 mb-1">
                                        <Icon icon={item.icon} type="light" className="mr-3" />
                                        {item.label}
                                    </Link>
                                    <>
                                        {item.items.map((child) => (
                                            <Link href={route(child.route)} className="py-1 text-sm hover:opacity-75">
                                                <Icon icon={child.icon} type="light" className="mr-3" />
                                                {child.label}
                                            </Link>
                                        ))}
                                    </>
                                </div>
                            ): (
                                <div className="ml-3 py-3 px-6 flex flex-col">
                                    <Link href={route(item.route)} className="text-white opacity-75 uppercase font-semibold hover:opacity-50">
                                        <Icon icon={item.icon} type="light" className="mr-3" />
                                        {item.label}
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
}

export default TenantLayout;
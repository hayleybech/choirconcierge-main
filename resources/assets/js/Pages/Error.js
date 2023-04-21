import React from 'react'
import AppHead from "../components/AppHead";
import Icon from "../components/Icon";
import classNames from "../classNames";
import Button from "../components/inputs/Button";
import useRoute from "../hooks/useRoute";
import {usePage} from "@inertiajs/react";

const Show = ({ status, choirAdmins, isMember }) => {
    const { tenant } = usePage().props;
    console.log(usePage().props);

    const title = {
        403: 'Forbidden',
        404: 'Page Not Found',
        500: 'Server Error',
        503: 'Service Unavailable',
    }[status];

    const description = {
        403: 'Sorry, you are forbidden from accessing this page.',
        404: 'Sorry, the page you are looking for could not be found.',
        500: 'Whoops, something went wrong on our servers.',
        503: 'Sorry, we are doing some maintenance. Please check back soon.',
    }[status];

    const caption = {
        403: "Hey buddy, you're singing my notes again!",
        404: "Darn it, what were my notes in this part?",
        500: "... And 1, 2, 3, 4. Oops, gotta scratch my nose. Wait altos, that wasn't your cue!",
        503: "We're in rehearsals now, come back when we're ready to perform!",
    }[status];

    const callToAction = {
        403: <>
            If you think you should be allowed here, <br />
            please contact one of your choir's admins.
        </>,
        404: "Check the address and try again.",
        500: "Try again. If the issue happens again, please contact us.",
        503: <>
            While you wait, why not add us on social media? <br />
            We'll probably post an update there when the site is back up.
        </>,
    }[status];

    const action = {
        403: <NavButtons />,
        404: <NavButtons />,
        500: <NavButtons />,
        503: <SocialButtons />,
    }[status];

    const extraDetails = {
        403: isMember ? <ChoirAdmins admins={choirAdmins} /> : null,
        404: tenant ? <PopularPages /> : null,
        500: null,
        503: null,
    }[status];

    return (
        <>
            <AppHead title="Error" />

            <div className="bg-white">
                <main className="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex-shrink-0 pt-16">
                        <a href={route('central.dash')} className="flex">
                            <img src="/img/vibrant/logo-dark.svg" alt="Choir Concierge" className="h-12 w-auto mx-auto" />
                        </a>
                    </div>

                    <div className="max-w-xl mx-auto py-16 sm:py-16">
                        <div className="text-center">

                            <div className="text-2xl text-center italic">{caption}</div>

                            <p className="mt-16 text-sm font-semibold text-purple-600 uppercase tracking-wide">{status} error</p>
                            <h1 className="mt-2 text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl">
                                {title}
                            </h1>
                            <p className="mt-2 text-base text-gray-500">{description}</p>

                            <p className="mt-6 text-base">{callToAction}</p>

                            <div className="mt-4">{action}</div>
                        </div>

                        <div>{extraDetails}</div>
                    </div>

                </main>
                <footer className="flex-shrink-0 bg-gray-50">
                    <div className="mx-auto max-w-7xl w-full px-4 py-16 sm:px-6 lg:px-8">
                        <nav className="flex space-x-4 justify-center">
                            <Link href="mailto:hello@choirconcierge.com" target="_blank" variant="secondary">
                                Email Us
                            </Link>
                            <span className="inline-block border-l border-gray-300" aria-hidden="true" />
                            <Link href="https://stats.uptimerobot.com/99Y2MUmKA5" target="_blank" variant="secondary">
                                Status
                            </Link>
                            <span className="inline-block border-l border-gray-300" aria-hidden="true" />
                            <Link href="https://twitter.com/ChoirConcierge" target="_blank" variant="secondary">
                                Twitter
                            </Link>
                            <span className="inline-block border-l border-gray-300" aria-hidden="true" />
                            <Link href="https://www.facebook.com/choirconcierge" target="_blank" variant="secondary">
                                Facebook
                            </Link>
                        </nav>
                    </div>
                </footer>
            </div>
        </>
    );
}

export default Show;

const Link = ({ variant = 'primary', className, children, ...extraProps }) => (
    <a
        {...extraProps}
        className={classNames(
            'font-medium',
            variant === 'primary' ? 'text-purple-600 hover:text-purple-500' : 'text-gray-500 hover:text-gray-600',
            className
        )}
    >
        {children}
    </a>
);

const NavButtons = () => {
    const { route } = useRoute();
    const { tenant } = usePage().props;

    return (
        <div className="flex gap-x-4 items-center justify-center">
            <Button href={tenant ? route('dash') : route('central.dash')} variant="primary">
                Go to dashboard
                <Icon icon="long-arrow-right" />
            </Button>
            <Button href="mailto:hello@choirconcierge.com" target="_blank" external variant="secondary">
                <Icon icon="envelope" />
                Email Us
            </Button>
        </div>
    );
}

const SocialButtons = () => (
    <div className="flex gap-x-2 text-2xl justify-center">
        <Link href="https://www.facebook.com/choirconcierge" target="_blank">
            <Icon icon="facebook" type="brand" />
        </Link>
        <Link href="https://twitter.com/ChoirConcierge" target="_blank">
            <Icon icon="twitter" type="brand" />
        </Link>
    </div>
);

const ChoirAdmins = ({ admins }) => (
    <div className="mt-12">
        <h2 className="text-sm font-semibold text-gray-500 tracking-wide uppercase">Choir Site Admins</h2>
        <ul role="list" className="mt-4 border-t border-b border-gray-200 divide-y divide-gray-200">
            {admins.map((singer) => (
                <li key={singer.user.email} className="py-4 flex">
                    <img className="h-10 w-10 rounded-full" src={singer.user.avatar_url} alt="" />
                    <div className="ml-3">
                        <p className="text-sm font-medium text-gray-900">{singer.user.name}</p>
                        <p className="text-sm text-gray-500">{singer.user.email}</p>
                    </div>
                </li>
            ))}
        </ul>
    </div>
);

const PopularPages = () => {
    const { tenant } = usePage().props;

    const popularLinks = [
        { title: 'Songs', icon: 'list-music', description: 'View all of your music and start practising!', href: route('songs.index', {tenant: tenant.id}) },
        { title: 'Events', icon: 'calendar', description: 'Your gigs for the year, right here.', href: route('events.index', {tenant: tenant.id}) },
        { title: 'Documents', icon: 'folders', description: 'Important minutes, documents and more.', href: route('folders.index', {tenant: tenant.id}) },
    ];

    return (
        <div className="mt-12">
            <h2 className="text-sm font-semibold text-gray-500 tracking-wide uppercase">Popular pages</h2>
            <ul role="list" className="mt-4 border-t border-b border-gray-200 divide-y divide-gray-200">
                {popularLinks.map((link, linkIdx) => (
                    <li key={linkIdx} className="relative py-6 flex items-start space-x-4">
                        <div className="flex-shrink-0">
                        <span className="flex items-center justify-center h-12 w-12 rounded-lg bg-purple-50">
                          <Icon icon={link.icon} className="h-6 w-6 text-purple-700" aria-hidden="true" />
                        </span>
                        </div>
                        <div className="min-w-0 flex-1">
                            <h3 className="text-base font-medium text-gray-900">
                      <span className="rounded-sm focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-purple-500">
                        <a href={link.href} className="focus:outline-none">
                          <span className="absolute inset-0" aria-hidden="true" />
                            {link.title}
                        </a>
                      </span>
                            </h3>
                            <p className="text-base text-gray-500">{link.description}</p>
                        </div>
                        <div className="flex-shrink-0 self-center">
                            <Icon icon="chevron-right" className="h-5 w-5 text-gray-400" aria-hidden="true" />
                        </div>
                    </li>
                ))}
            </ul>
        </div>
    );
}
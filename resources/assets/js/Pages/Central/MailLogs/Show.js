import React from 'react'
import PageHeader from "../../../components/PageHeader";
import classNames from "../../../classNames";
import AppHead from "../../../components/AppHead";
import DateTag from "../../../components/DateTag";
import CollapseGroup from "../../../components/CollapseGroup";
import useRoute from "../../../hooks/useRoute";
import CentralLayout from "../../../Layouts/CentralLayout";
import Prose from '../../../components/Prose';
import Icon from '../../../components/Icon';
import { mailIconColours, mailIcons } from '../../../components/MailStatusTag';
import { Link } from '@inertiajs/react';
const Show = ({ log }) => {
    const { route } = useRoute();

    const isBroadcast = log.uid.startsWith('broadcast');

    return (
        <>
            <AppHead title={`${log.subject} - Mail Logs`} />
            <PageHeader
                title={log.subject}
                icon={isBroadcast ? 'satellite-dish' : "envelope"}
                meta={[
                    <DateTag date={log.created_at} label="Created" />,
                    <DateTag date={log.updated_at} label="Updated" />,
                    <div>From: {log.from}</div>,
                    <div>To: {log.to}</div>,
                    <div>Cc: {log.cc}</div>,
                    <div>Bcc: {log.bcc}</div>,
                    <div>
                        <Icon icon="paperclip" mr /> {log.has_attachments ? 'Has attachments' : 'No attachments'}
                    </div>,
                ]}
                breadcrumbs={[
                    { name: 'Dashboard', url: route('central.dash')},
                    { name: 'Mail Logs', url: route('central.mail-logs.index')},
                    { name: log.subject, url: route('central.mail-logs.show', {mail_log: log}) },
                ]}
                actions={[]}
            />

            <div className="grid grid-cols-1 sm:grid-cols-3 xl:grid-cols-3 divide-y divide-gray-300 sm:divide-y-0 sm:divide-x">

                <div className="sm:col-span-2 xl:col-span-2 divide-y divide-y-gray-300">
                    <CollapseGroup items={[
                        { title: 'Body', show: true, defaultOpen: true, content: <div className="py-4 px-8"><Prose content={log.body} /></div>},
                    ]} />
                </div>

                <div className="sm:col-span-1 divide-y divide-y-gray-300">
                    <CollapseGroup items={[
                        { title: 'Activity', show: true, defaultOpen: true, content: <Activity log={log} />},
                    ]} />
                </div>

            </div>
        </>
    );
}

Show.layout = page => <CentralLayout children={page} />

export default Show;

const Activity = ({log}) => {
    const isBroadcast = log.uid.startsWith('broadcast');

    // @todo PAGINATION FOR THE LOVE OF GOD

    const events = [
        {
            id: 0,
            status: 'received',
            context: '',
            created_at: log.received_at,
        },
        ...log.events,
    ].map(event => ({
        ...event,
        iconColour: mailIconColours[event.status] ?? 'bg-gray-400',
        icon: mailIcons[event.status] ?? 'question',
    })).reverse();


    return (
        <>
            <div className="flow-root px-6 py-8">
                <ul role="list" className="-mb-8">
                    {events.map((event, eventIdx) => (
                        <li key={event.id}>
                            <div className="relative pb-8">
                                {eventIdx !== events.length - 1 ? (
                                    <div aria-hidden="true" className="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-400" />
                                ) : null}
                                <div className="relative flex space-x-3">
                                    <div>
                                        <span
                                          className={classNames(
                                              event.iconColour,
                                              'flex h-8 w-8 items-center justify-center rounded-full ring-8 ring-gray-100',
                                          )}
                                        >
                                            <Icon icon={event.icon} type="regular" className="text-white text-sm" />
                                        </span>
                                    </div>
                                    <div className="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                        <div>
                                            {event.status === 'received' && isBroadcast && (
                                                <p className="text-sm text-gray-500">
                                                    Broadcast created by{' '}
                                                    <span className="font-medium text-gray-900">
                                                        {log.from}
                                                    </span>
                                                </p>
                                            )}
                                            {event.status === 'received' && !isBroadcast && (
                                                <p className="text-sm text-gray-500">
                                                    Email delivered to{' '}
                                                    <span className="font-medium text-gray-900">
                                                        our inbox
                                                    </span>
                                                </p>
                                            )}
                                            {event.status === 'pending' && (
                                                <p className="text-sm text-gray-500">
                                                    Email found by {' '}
                                                    <span className="font-medium text-gray-900">
                                                        our email system
                                                    </span>
                                                </p>
                                            )}
                                            {event.status === 'group-not-found' && (
                                                <p className="text-sm text-gray-500">
                                                    Recipient skipped: Mailing group{' '}
                                                    <span className="font-medium text-gray-900">
                                                        {event.context}
                                                    </span>
                                                    {' '}does not exist.
                                                </p>
                                            )}
                                            {event.status === 'group-found' && (
                                                <p className="text-sm text-gray-500">
                                                    A recipient matches group{' '}
                                                    <Link
                                                        href={route('groups.show', {group: event.user_group, tenant: event.user_group.tenant_id})}
                                                        className="font-medium text-purple-600 hover:text-purple-800 focus:text-purple-800"
                                                    >
                                                        {event.user_group?.title ?? event.context}
                                                    </Link>
                                                </p>
                                            )}
                                            {event.status === 'rejected-sender' && (
                                                <p className="text-sm text-gray-500">
                                                    Sender rejected: Mailing group{' '}
                                                    <span className="font-medium text-gray-900">
                                                        {event.context}
                                                    </span>{' '}exists but{' '}
                                                    <span className="font-medium text-gray-900">
                                                        {log.from}
                                                    </span>
                                                    {' '}is not permitted to contact it.
                                                </p>
                                            )}
                                            {event.status === 'malformed-recipient' && (
                                                <p className="text-sm text-gray-500">
                                                    It looks like
                                                    <span className="font-medium text-gray-900">
                                                        {event.context}
                                                    </span>{' '} is not a valid email address.
                                                </p>
                                            )}
                                            {event.status === 'clones-sent' && (
                                                <p className="text-sm text-gray-500">
                                                    A copy was sent to {' '}
                                                    <span className="font-medium text-gray-900">
                                                        all recipients
                                                    </span>
                                                </p>
                                            )}
                                            {! ['received', 'pending', 'clones-sent', 'group-not-found', 'group-found', 'rejected-sender'].includes(event.status) && (
                                                <p className="text-sm text-gray-500">
                                                    Other Status:{' '}
                                                    <span className="font-medium text-gray-900">
                                                        {event.status}
                                                    </span>
                                                </p>
                                            )}
                                        </div>
                                        <div className="text-right text-sm whitespace-nowrap text-gray-500">
                                            <DateTag date={event.created_at} format={'DATETIME_SHORT'} />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    ))}
                </ul>
            </div>
        </>
    );
}
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
import MailStatusTag, { mailIconColours, mailIcons } from '../../../components/MailStatusTag';
import { Link } from '@inertiajs/react';
import MailStatusDetail from '../../../components/MailStatusDetail';
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
                                            <MailStatusDetail log={log} event={event} isBroadcast={isBroadcast} />
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
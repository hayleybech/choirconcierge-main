import React from 'react'
import Layout from "../../Layouts/Layout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import {DateTime} from "luxon";
import AttendanceTag from "../../components/Event/AttendanceTag";

const AttendanceReport = ({ events, voiceParts, numSingers, avgSingersPerEvent, avgEventsPerSinger }) => (
    <>
        <AppHead title="Attendance Report" />
        <PageHeader
            title="Attendance Report"
            icon="analytics"
            breadcrumbs={[
                { name: 'Dashboard', url: route('dash')},
                { name: 'Events', url: route('events.index')},
                { name: 'Attendance Report', url: route('events.reports.attendance') },
            ]}
            meta={[
                <div>Avg. singers per event: {avgSingersPerEvent}</div>,
                <div>Avg. events per singer: {avgEventsPerSinger}</div>,
            ]}
        />

        <div className="overflow-auto pb-8 pr-8">
            <table className="bg-white">
                <thead>
                    <tr>
                        <th />
                        {events.map((event) => (
                            <th key={event.id} className="p-5 border border-gray-300 align-bottom">
                                <div className="flex justify-center transform rotate-180">
                                    <div className="text-ellipsis overflow-hidden text-sm text-left" style={{ writingMode: 'vertical-lr' }}>
                                        {event.title}
                                    </div>
                                </div>
                            </th>
                        ))}
                        <th className="p-5 border border-gray-300 align-bottom bg-gray-100">
                            <div className="flex justify-center transform rotate-180">
                                <div className="text-ellipsis overflow-hidden text-sm text-left" style={{ writingMode: 'vertical-lr' }}>
                                    Events Present
                                </div>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th />
                        {events.map((event) => (
                        <th key={event.id} className="font-medium text-gray-500 text-sm whitespace-nowrap border border-gray-300 p-5">
                            {DateTime.fromISO(event.start_date).toFormat('y')}<br />
                            {DateTime.fromISO(event.start_date).toFormat('MM-dd')}<br />
                        </th>
                        ))}
                        <td className="border border-gray-300 bg-gray-100">
                            &nbsp;
                        </td>
                    </tr>
                </thead>
                <tbody>
                {voiceParts.map((voicePart) => (<React.Fragment key={voicePart.id}>
                    <tr>
                        <th colSpan="100000" className="text-left px-5 py-3 bg-gray-100 border border-gray-300">{voicePart.title}</th>
                    </tr>
                    {voicePart.singers.map((singer) => (
                        <tr key={singer.id}>
                            <th className="px-5 py-3 text-left whitespace-nowrap border border-gray-300">
                                <div className="flex flex-nowrap items-center">
                                    <div className="shrink-0 h-10 w-10 mr-4">
                                        <img className="h-10 w-10 rounded-md" src={singer.user.avatar_url} alt={singer.user.name} />
                                    </div>

                                    {singer.user.name}
                                </div>
                            </th>
                            {events.map((event) => getAttendanceBySingerAndEvent(singer, event)).map((attendance, key) => (
                                <td className="border border-gray-300 text-center" key={key}>
                                    {attendance
                                        ? <AttendanceTag icon={attendance.icon} colour={attendance.colour} />
                                        : <AttendanceTag icon="question" colour="gray" />
                                    }
                                </td>
                            ))}
                            <td className="border border-gray-300 text-gray-500 bg-gray-100 text-center px-5">
                                <div>{singer.percentPresent}%</div>
                                <div className="text-xs">{singer.timesPresent}&nbsp;/&nbsp;{events.length}</div>
                            </td>
                        </tr>
                    ))}
                </React.Fragment>))}
                </tbody>
                <tfoot>
                    <tr>
                        <th className="border border-gray-300 bg-gray-100 text-left p-5">Singers Present</th>
                        {events.map((event) => (
                            <td className="border border-gray-300 text-gray-500 bg-gray-100 text-center px-5" key={event.id}>
                                <div>{event.percentPresent}%</div>
                                <div className="text-xs">{event.singersPresent} / {numSingers}</div>
                            </td>
                        ))}
                    </tr>
                </tfoot>
            </table>
        </div>
    </>
);

AttendanceReport.layout = page => <Layout children={page} />

export default AttendanceReport;

const getAttendanceBySingerAndEvent = (singer, event) => singer.attendances.filter((attendance) => attendance.event_id === event.id)[0] ?? null;
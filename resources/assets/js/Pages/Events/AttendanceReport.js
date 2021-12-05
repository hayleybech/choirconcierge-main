import React from 'react'
import Layout from "../../Layouts/Layout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import {DateTime} from "luxon";

const AttendanceReport = ({ events, voiceParts, avgSingersPerEvent, avgEventsPerSinger }) => (
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
        />

        <div>
            <table>
                <thead>
                    <tr>
                    {events.map((event) => (
                        <th className="relative p-5 h-72 -translate-x-2 whitespace-nowrap border border-gray-300">
                            <div className="
                                absolute w-72 overflow-ellipsis overflow-hidden bottom-0 left-0
                                transform -translate-x-24 -translate-y-40
                                -rotate-90
                                text-sm text-left
                            ">
                                {event.title}
                            </div>
                        </th>
                    ))}
                        <th>% Events Present</th>
                    </tr>
                    <tr>
                    {events.map((event) => (
                        <th className="font-medium text-gray-500 text-sm whitespace-nowrap border border-gray-300 p-5">
                            {DateTime.fromISO(event.start_date).toFormat('y')}<br />
                            {DateTime.fromISO(event.start_date).toFormat('MM-dd')}<br />
                        </th>
                    ))}
                    </tr>
                </thead>
            </table>
        </div>
    </>
);

AttendanceReport.layout = page => <Layout children={page} />

export default AttendanceReport;
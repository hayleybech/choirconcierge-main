import React, {useState} from 'react';
import PageHeader from "../../../components/PageHeader";
import Layout from "../../../Layouts/Layout";
import Button from "../../../components/inputs/Button";
import AppHead from "../../../components/AppHead";
import AttendanceTag from "../../../components/Event/AttendanceTag";
import Icon from "../../../components/Icon";
import TextInput from "../../../components/inputs/TextInput";
import Label from "../../../components/inputs/Label";

const Index = ({ event, voice_parts }) => {
    const [absentReasons, setAbsentReasons] = useState({});
    return (
        <>
            <AppHead title={`Attendance Summary - ${event.title}`} />
            <PageHeader
                title="Attendance Summary"
                icon="calendar"
                breadcrumbs={[
                    { name: 'Dashboard', url: route('dash') },
                    { name: 'Events', url: route('events.index') },
                    { name: event.title, url: route('events.show', event) },
                    { name: 'Attendance', url: route('events.attendances.index', event) },
                ]}
            />

            <nav className="h-full overflow-y-auto" aria-label="Directory">
                {voice_parts.map((part) => (
                    <div key={part.id} className="relative">
                        <div className="z-10 sticky top-0 border-t border-b border-gray-200 bg-gray-50 px-6 py-1 text-sm font-medium text-gray-500">
                            <h3>{part.title}</h3>
                        </div>
                        <ul role="list" className="relative z-0 divide-y divide-gray-200">
                            {part.singers.map((attendance) => (
                                <li key={attendance.singer.id} className="bg-white">
                                    <div className="relative px-6 py-5 flex flex-col sm:flex-row items-center space-y-3 sm:space-x-3 hover:bg-gray-50 justify-between items-stretch sm:items-center">
                                        <div className="flex space-x-2">
                                            <div className="shrink-0">
                                                <img className="h-12 w-12 rounded-lg" src={attendance.singer.user.avatar_url} alt={attendance.singer.user.name}/>
                                            </div>
                                            <div className="flex-1 min-w-0">
                                                <p className="text-sm font-medium text-gray-900">{attendance.singer.user.name}</p>
                                                <p className="text-sm">
                                                    <AttendanceTag label={attendance.label} icon={attendance.icon} colour={attendance.colour} />
                                                    {attendance.response.includes('absent') && attendance.absent_reason && (
                                                        <div className="text-gray-500">Reason for absence: {attendance.absent_reason}</div>
                                                    )}
                                                </p>
                                            </div>
                                        </div>
                                        <div className="shrink-0 flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2 items:stretch sm:items-center">
                                            {[
                                                { response: 'present', label: 'On Time', icon: 'check', variant: 'success-outline' },
                                                { response: 'late', label: 'Late', icon: 'alarm-exclamation', variant: 'warning-outline' },
                                                { response: 'absent', label: 'Absent', icon: 'times', variant: 'danger-outline' },
                                                { response: 'absent_apology', label: 'With Apology', icon: 'times', variant: 'danger-outline' },
                                            ].map(({ response, label, icon, variant }) =>
                                                attendance.response !== response &&
                                                <Button
                                                    href={route('events.attendances.update', [event, attendance.singer.id])}
                                                    method="put"
                                                    data={{ response: response, absent_reason: absentReasons[attendance.singer.id] }}
                                                    size="sm"
                                                    variant={variant}
                                                    key={response}
                                                >
                                                    <Icon icon={icon} />
                                                    Mark as {label}
                                                </Button>
                                            )}


                                        </div>

                                        <div className="flex flex-col sm:flex-row items-stretch sm:items-center sm:ml-16 space-y-2 sm:space-x-2">
                                            <Label label="Reason for absence" forInput={`absent_reason_${attendance.singer.id}`} />
                                            <TextInput
                                                name="absent_reason"
                                                id={`absent_reason_${attendance.singer.id}`}
                                                value={absentReasons[attendance.singer.id]}
                                                updateFn={(value) => setAbsentReasons({
                                                    ...absentReasons,
                                                    [attendance.singer.id]: value
                                                })}
                                            />
                                        </div>

                                    </div>
                                </li>
                            ))}
                        </ul>
                    </div>
                ))}
            </nav>
        </>
    );
}

Index.layout = page => <Layout children={page} />

export default Index;
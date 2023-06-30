import React, {useState} from 'react';
import PageHeader from "../../../components/PageHeader";
import TenantLayout from "../../../Layouts/TenantLayout";
import Button from "../../../components/inputs/Button";
import AppHead from "../../../components/AppHead";
import AttendanceTag from "../../../components/Event/AttendanceTag";
import Icon from "../../../components/Icon";
import TextInput from "../../../components/inputs/TextInput";
import Label from "../../../components/inputs/Label";
import CollapseGroup from "../../../components/CollapseGroup";
import useRoute from "../../../hooks/useRoute";

const Index = ({ event, voice_parts }) => {
    const [absentReasons, setAbsentReasons] = useState({});
    const { route } = useRoute();

    return (
        <>
            <AppHead title={`Attendance Summary - ${event.title}`} />
            <PageHeader
                title="Attendance Summary"
                icon="calendar"
                breadcrumbs={[
                    { name: 'Dashboard', url: route('dash') },
                    { name: 'Events', url: route('events.index') },
                    { name: event.title, url: route('events.show', {event}) },
                    { name: 'Attendance', url: route('events.attendances.index', {event}) },
                ]}
            />

          <CollapseGroup items={voice_parts.map((part) => ({
            title: part.title,
            show: true,
            content: (
                <div key={part.id} className="relative">
                  <div className="flex flex-wrap bg-white py-4 border-b border-gray-200 gap-y-4">
                    {[
                      { label: 'On Time', colour: 'emerald-500', icon: 'check', count: part.singers.filter(attendance => attendance.response === 'present').length },
                      { label: 'Late', colour: 'amber-500', icon: 'alarm-exclamation', count: part.singers.filter(attendance => attendance.response === 'late').length },
                      { label: 'Absent', colour: 'red-500', icon: 'times', count: part.singers.filter(attendance => attendance.response === 'absent').length },
                      { label: 'Absent (with apology)', colour: 'red-500', icon: 'times', count: part.singers.filter(attendance => attendance.response === 'absent_apology').length },
                      { label: 'Unknown', colour: 'gray-500', icon: 'question', count: part.singers.filter(attendance => attendance.response === 'unknown').length },
                    ].map(({ label, colour, icon, count}) => (
                      <div className="w-1/2 md:w-1/5 text-center" key={label}>
                        <Icon icon={icon} className={`text-${colour}`} />
                        <p className={`font-semibold text-${colour}`}>{label}</p>
                        {count}
                      </div>
                    ))}
                  </div>
                    <ul role="list" className="relative z-0 divide-y divide-gray-200">
                        {part.singers.map((attendance) => (
                            <li key={attendance.singer.id} className="bg-white">
                                <div className="relative px-6 py-5 flex flex-col xl:flex-row items-stretch xl:items-center gap-y-3 sm:gap-x-3 hover:bg-gray-50 justify-between">
                                    <div className="flex space-x-2 shrink-0">
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
                                                href={route('events.attendances.update', {event, singer: attendance.singer.id})}
                                                method="put"
                                                data={{ response: response, absent_reason: absentReasons[attendance.singer.id] }}
                                                preserveScroll
                                                size="sm"
                                                variant={variant}
                                                key={response}
                                            >
                                                <Icon icon={icon} />
                                                Mark as {label}
                                            </Button>
                                        )}
                                    </div>

                                    <div className="flex flex-col sm:flex-row items-stretch sm:items-center space-y-2 sm:space-x-2">
                                        <Label label="Reason for absence" forInput={`absent_reason_${attendance.singer.id}`} />
                                        <TextInput
                                            name="absent_reason"
                                            id={`absent_reason_${attendance.singer.id}`}
                                            value={absentReasons[attendance.singer.id]}
                                            updateFn={(value) => setAbsentReasons({
                                                ...absentReasons,
                                                [attendance.singer.id]: value
                                            })}
                                            wrapperClasses="grow"
                                        />
                                    </div>

                                </div>
                            </li>
                        ))}
                    </ul>
                </div>
            ),
          }))} />
        </>
    );
}

Index.layout = page => <TenantLayout children={page} />

export default Index;
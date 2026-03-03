import React from 'react';
import TenantLayout from "../../../Layouts/TenantLayout";
import PageHeader from "../../../components/PageHeader/PageHeader";
import AppHead from "../../../components/AppHead";
import AttendanceTag from "../../../components/Event/AttendanceTag";
import useRoute from "../../../hooks/useRoute";
import useFilterPane from "../../../hooks/useFilterPane";
import FilterSortPane from "../../../components/FilterSortPane";
import SingerAttendanceFilters from "./SingerAttendanceFilters";
import useSortFilterForm from "../../../hooks/useSortFilterForm";
import EmptyState from "../../../components/EmptyState";
import { Link } from '@inertiajs/react';
import { DateTime } from 'luxon';

const Index = ({ singer, attendances, eventTypes }) => {
    const { route } = useRoute();
    const [showFilters, setShowFilters, filterAction, hasNonDefaultFilters] = useFilterPane();

    const sorts = [];
    const filters = [
        { name: 'type.id', multiple: true, defaultValue: [] },
        { name: 'starts_after' },
        { name: 'starts_before' },
    ];

    const sortFilterForm = useSortFilterForm(['singers.attendance', { singer }], filters, sorts);

    return (
        <>
            <AppHead title={`Attendance - ${singer.user.name}`} />
            <PageHeader
                title="Attendance Records"
                icon="calendar-check"
                breadcrumbs={[
                    { name: 'Dashboard', url: route('dash') },
                    { name: 'Singers', url: route('singers.index') },
                    { name: singer.user.name, url: route('singers.show', { singer }) },
                    { name: 'Attendance', url: route('singers.attendance', { singer }) },
                ]}
                actions={[filterAction]}
            />

            <div className="flex flex-col overflow-auto lg:flex-row divide-y lg:divide-y-0 lg:divide-x divide-gray-300 h-full">
                {showFilters && (
                    <div className="lg:w-1/5 xl:w-1/6 lg:z-10 h-full">
                        <FilterSortPane
                            filters={<SingerAttendanceFilters eventTypes={eventTypes} form={sortFilterForm} singer={singer} />}
                            closeFn={() => setShowFilters(false)}
                        />
                    </div>
                )}
                <div className="grow lg:overflow-x-auto">
                    {attendances.length === 0 ? (
                        <EmptyState
                            icon="calendar"
                            title="No records found"
                            description="Try expanding your filters, or maybe this singer hasn't recorded any attendance yet."
                        />
                    ) : (
                        <div className="p-8">
                            <div className="overflow-hidden bg-white shadow sm:rounded-md">
                                <ul className="divide-y divide-gray-200">
                                    {attendances.map((attendance) => (
                                        <li key={attendance.id}>
                                            <Link href={route('events.show', { event: attendance.event })} className="block hover:bg-gray-50">
                                                <div className="flex items-center px-4 py-4 sm:px-6">
                                                    <div className="min-w-0 flex-1 sm:flex sm:items-center sm:justify-between">
                                                        <div className="truncate">
                                                            <div className="flex text-sm">
                                                                <p className="truncate font-medium text-purple-600">{attendance.event.title}</p>
                                                                <p className="ml-1 flex-shrink-0 font-normal text-gray-500">
                                                                    in {attendance.event.type.title}
                                                                </p>
                                                            </div>
                                                            <div className="mt-2 flex">
                                                                <div className="flex items-center text-sm text-gray-500">
                                                                    <p>
                                                                        {DateTime.fromISO(attendance.event.start_date).toLocaleString(DateTime.DATE_MED_WITH_WEEKDAY)}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div className="mt-4 flex-shrink-0 sm:mt-0 sm:ml-5">
                                                            <AttendanceTag
                                                                icon={attendance.icon}
                                                                colour={attendance.colour}
                                                                label={attendance.label}
                                                            />
                                                        </div>
                                                    </div>
                                                </div>
                                            </Link>
                                        </li>
                                    ))}
                                </ul>
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </>
    );
};

Index.layout = page => <TenantLayout children={page} />;

export default Index;

import React from 'react';
import TenantLayout from "../../../Layouts/TenantLayout";
import PageHeader from "../../../components/PageHeader/PageHeader";
import AppHead from "../../../components/AppHead";
import useRoute from "../../../hooks/useRoute";
import useFilterPane from "../../../hooks/useFilterPane";
import FilterSortPane from "../../../components/FilterSortPane";
import SingerAttendanceFilters from "./SingerAttendanceFilters";
import useSortFilterForm from "../../../hooks/useSortFilterForm";
import EmptyState from "../../../components/EmptyState";
import IndexContainer from "../../../components/IndexContainer";
import AttendanceTableDesktop from "./AttendanceTableDesktop";
import AttendanceTableMobile from "./AttendanceTableMobile";

const Index = ({ singer, attendances, eventTypes, pagination }) => {
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
                optionsVariant={hasNonDefaultFilters ? 'success-solid' : 'secondary'}
            />

            <IndexContainer
                showFilters={showFilters}
                filterPane={
                    <FilterSortPane
                        filters={<SingerAttendanceFilters eventTypes={eventTypes} form={sortFilterForm} singer={singer} />}
                        closeFn={() => setShowFilters(false)}
                    />
                }
                tableMobile={
                    <AttendanceTableMobile
                        attendances={attendances}
                        pagination={pagination}
                        hasNonDefaultFilters={hasNonDefaultFilters}
                        setShowFilters={setShowFilters}
                    />
                }
                tableDesktop={
                    <AttendanceTableDesktop
                        attendances={attendances}
                        pagination={pagination}
                    />
                }
                emptyState={
                    attendances.length === 0 ? (
                        <EmptyState
                            icon="calendar"
                            title="No records found"
                            description="Try expanding your filters, or maybe this singer hasn't recorded any attendance yet."
                        />
                    ) : null
                }
            />
        </>
    );
};

Index.layout = page => <TenantLayout children={page} />;

export default Index;

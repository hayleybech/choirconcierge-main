import React from 'react';
import TenantLayout from '../../../Layouts/TenantLayout';
import {
	PageHeader,
	PageHeaderBreadcrumbs,
	PageHeaderContent,
	PageHeaderTitle,
} from '../../../components/PageHeader/PageHeader';
import PageTopBar, { PageTopNavigation } from '../../../components/PageTopBar';
import Icon from '../../../components/Icon';
import AppHead from '../../../components/AppHead';
import useRoute from '../../../hooks/useRoute';
import useFilterPane from '../../../hooks/useFilterPane';
import FilterDrawer from '../../../components/FilterDrawer';
import SingerAttendanceFilters from './SingerAttendanceFilters';
import useSortFilterForm from '../../../hooks/useSortFilterForm';
import EmptyState from '../../../components/EmptyState';
import IndexContainer from '../../../components/IndexContainer';
import IndexTableControls from '../../../components/IndexTableControls';
import AttendanceTableDesktop from './AttendanceTableDesktop';
import AttendanceTableMobile from './AttendanceTableMobile';

const Index = ({ singer, attendances, eventTypes, pagination, setSidebarOpen }) => {
	const { route } = useRoute();
	const [showFilters, setShowFilters] = useFilterPane();

	const sorts = [];
	const filters = [
		{
			name: 'type.id',
			label: 'Event Type',
			multiple: true,
			defaultValue: [],
			options: eventTypes.map(type => ({ id: type.id, name: type.title })),
		},
		{
			name: 'date_range',
			start_name: 'starts_after',
			end_name: 'starts_before',
			label: 'Date Range',
			type: 'daterange',
			defaultValue: '',
		},
	];

	const sortFilterForm = useSortFilterForm(['singers.attendance', { singer }], filters, sorts);

	const breadcrumbs = [
		{ name: 'Singers', url: route('singers.index') },
		{ name: singer.user.name, url: route('singers.show', { singer }) },
		{ name: 'Attendance Records', url: route('singers.attendance', { singer }) },
	];

	return (
		<>
			<AppHead title={`Attendance - ${singer.user.name}`} />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation breadcrumbs={breadcrumbs}></PageTopNavigation>
			</PageTopBar>

			<PageHeader hideOnMobile>
				<PageHeaderContent>
					<PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
					<PageHeaderTitle>
						<Icon icon="calendar-check" type="solid" className="mr-2" />
						Attendance Records
					</PageHeaderTitle>
				</PageHeaderContent>
			</PageHeader>

			<IndexContainer
				showFilters={showFilters}
				tableControls={
					<IndexTableControls
						filters={filters}
						sorts={sorts}
						form={sortFilterForm}
						filterLabel="attendance records"
						onToggleFilters={() => setShowFilters(previousValue => !previousValue)}
					/>
				}
				filterPane={
					<FilterDrawer closeFn={() => setShowFilters(false)}>
						<SingerAttendanceFilters eventTypes={eventTypes} form={sortFilterForm} singer={singer} />
					</FilterDrawer>
				}
				tableMobile={<AttendanceTableMobile attendances={attendances} pagination={pagination} />}
				tableDesktop={<AttendanceTableDesktop attendances={attendances} pagination={pagination} />}
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

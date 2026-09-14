import React from 'react';
import TenantLayout from '../../../Layouts/TenantLayout';
import {
	PageHeader,
	PageHeaderActions,
	PageHeaderBreadcrumbs,
	PageHeaderContent,
	PageHeaderTitle,
} from '../../../components/PageHeader/PageHeader';
import PageTopBar, { PageActionsMenu, PageTopNavigation } from '../../../components/PageTopBar';
import ActionMenuItem from '../../../components/ActionMenu/ActionMenuItem';
import Icon from '../../../components/Icon';
import Button from '../../../components/inputs/Button';
import AppHead from '../../../components/AppHead';
import useRoute from '../../../hooks/useRoute';
import useFilterPane from '../../../hooks/useFilterPane';
import FilterSortPane from '../../../components/FilterSortPane';
import SingerAttendanceFilters from './SingerAttendanceFilters';
import useSortFilterForm from '../../../hooks/useSortFilterForm';
import EmptyState from '../../../components/EmptyState';
import IndexContainer from '../../../components/IndexContainer';
import AttendanceTableDesktop from './AttendanceTableDesktop';
import AttendanceTableMobile from './AttendanceTableMobile';

const Index = ({ singer, attendances, eventTypes, pagination, setSidebarOpen }) => {
	const { route } = useRoute();
	const [showFilters, setShowFilters, filterAction, hasNonDefaultFilters] = useFilterPane();

	const sorts = [];
	const filters = [
		{ name: 'type.id', multiple: true, defaultValue: [] },
		{ name: 'starts_after' },
		{ name: 'starts_before' },
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
					{filterAction && (
						<PageActionsMenu>
							<ActionMenuItem onClick={filterAction.onClick} variant={filterAction.variant}>
								<Icon icon="filter" mr />
								Filter
							</ActionMenuItem>
						</PageActionsMenu>
					)}
			</PageTopBar>

			<PageHeader>
				<PageHeaderContent>
					<PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
					<PageHeaderTitle>
						<Icon icon="calendar-check" type="solid" className="mr-2" />
						Attendance Records
					</PageHeaderTitle>
				</PageHeaderContent>
				<PageHeaderActions>
					{filterAction && (
						<Button onClick={filterAction.onClick} size="sm" variant={filterAction.variant}>
							<Icon icon="filter" mr />
							Filter
						</Button>
					)}
				</PageHeaderActions>
			</PageHeader>

			<IndexContainer
				showFilters={showFilters}
				filterPane={
					<FilterSortPane
						filters={
							<SingerAttendanceFilters eventTypes={eventTypes} form={sortFilterForm} singer={singer} />
						}
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

import React from 'react';
import TenantLayout from '../../../Layouts/TenantLayout';
import { PageHeader2 } from '../../../components/PageHeader/PageHeader';
import Breadcrumbs from '../../../components/PageHeader/Breadcrumbs';
import { PageHeading } from '../../../components/PageHeader/PageHeading';
import PageTopBar, { PageActionsMenu, PageTopBarTitle, PageTopNavigation } from '../../../components/PageTopBar';
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

	return (
		<>
			<AppHead title={`Attendance - ${singer.user.name}`} />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<div className="flex justify-between grow">
					<PageTopNavigation
						backUrl={route('singers.show', { singer })}
						breadcrumbs={[
							{ name: 'Singers', url: route('singers.index') },
							{ name: singer.user.name, url: route('singers.show', { singer }) },
						]}
					>
						<PageTopBarTitle title="Attendance Records" />
					</PageTopNavigation>
					{filterAction && (
						<PageActionsMenu>
							<ActionMenuItem onClick={filterAction.onClick} variant={filterAction.variant}>
								<Icon icon="filter" mr />
								Filter
							</ActionMenuItem>
						</PageActionsMenu>
					)}
				</div>
			</PageTopBar>

			<PageHeader2>
				<div className="lg:flex lg:items-center lg:justify-between">
					<div className="flex-1 min-w-0">
						<div className="hidden lg:block">
							<Breadcrumbs
								breadcrumbs={[
									{ name: 'Singers', url: route('singers.index') },
									{ name: singer.user.name, url: route('singers.show', { singer }) },
									{ name: 'Attendance', url: route('singers.attendance', { singer }) },
								]}
								showLastChevron={false}
							/>
						</div>
						<PageHeading>
							<Icon icon="calendar-check" type="solid" className="mr-2" />
							Attendance Records
						</PageHeading>
					</div>
					<div className="hidden lg:flex mt-0 lg:ml-4 gap-3">
						{filterAction && (
							<Button onClick={filterAction.onClick} size="sm" variant={filterAction.variant}>
								<Icon icon="filter" mr />
								Filter
							</Button>
						)}
					</div>
				</div>
			</PageHeader2>

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

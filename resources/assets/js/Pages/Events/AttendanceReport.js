import React from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import {
	PageHeader,
	PageHeaderActions,
	PageHeaderBreadcrumbs,
	PageHeaderContent,
	PageHeaderMeta,
	PageHeaderTitle,
} from '../../components/PageHeader/PageHeader';
import PageTopBar, { PageActionsMenu, PageTopNavigation } from '../../components/PageTopBar';
import ActionMenuItem from '../../components/ActionMenu/ActionMenuItem';
import AppHead from '../../components/AppHead';
import { DateTime } from 'luxon';
import AttendanceTag from '../../components/Event/AttendanceTag';
import useRoute from '../../hooks/useRoute';
import useFilterPane from '../../hooks/useFilterPane';
import FilterSortPane from '../../components/FilterSortPane';
import AttendanceReportFilters from './AttendanceReportFilters';
import useSortFilterForm from '../../hooks/useSortFilterForm';
import EmptyState from '../../components/EmptyState';
import { Link } from '@inertiajs/react';
import Button from '../../components/inputs/ButtonV2';
import Icon from '../../components/Icon';
import { TableMobileHeader } from '../../components/TableMobile';
import AttendanceChart from '../../components/Event/AttendanceChart';
import Sorts from '../../components/Sorts';
import VoicePartTag from '../../components/VoicePartTag';
import FilterDialog from '../../components/FilterDialog';
import { useMediaQuery } from 'react-responsive';
import DateRangeCalendarInput from '../../components/inputs/DateRangeCalendarInput';

const AttendanceReport = ({
	events,
	eventTypes,
	voiceParts,
	defaultEventType,
	defaultStartsAfter,
	defaultStartsBefore,
	singers,
	numSingers,
	avgSingersPerEvent,
	avgEventsPerSinger,
	setSidebarOpen,
}) => {
	const { route } = useRoute();
	const [isChartCollapsed, setIsChartCollapsed] = React.useState(false);
	const [showFilters, setShowFilters, filterAction, hasNonDefaultFilters] = useFilterPane();
	const isDesktop = useMediaQuery({ query: '(min-width: 1024px)' });

	const sorts = [
		{ id: 'full-name', name: 'First Name', default: true },
		{ id: 'last-name-first', name: 'Last Name' },
		{ id: 'voice-part', name: 'Voice Part' },
		{ id: 'attendance', name: 'Events Present' },
	];
	const today = DateTime.now().startOf('day');
	const dateRangePresets = [
		{ label: 'Last 30 days', start: today.minus({ days: 29 }) },
		{ label: 'Last 90 days', start: today.minus({ days: 89 }) },
		{ label: 'Last 365 days', start: today.minus({ days: 364 }) },
		{ label: 'Last month', start: today.minus({ months: 1 }).startOf('month'), end: today.minus({ months: 1 }).endOf('month') },
		{ label: 'Last quarter', start: today.startOf('quarter').minus({ quarters: 1 }), end: today.startOf('quarter').minus({ days: 1 }) },
		{ label: 'Last year', start: today.minus({ years: 1 }).startOf('year'), end: today.minus({ years: 1 }).endOf('year') },
	];
	const filters = [
		{ name: 'type.id', multiple: true, defaultValue: [defaultEventType] },
		{ name: 'starts_after', defaultValue: defaultStartsAfter },
		{ name: 'starts_before', defaultValue: defaultStartsBefore },
		{ name: 'enrolments.voice_part_id', multiple: true },
	];

	const sortFilterForm = useSortFilterForm('events.reports.attendance', filters, sorts);
	const previousDateRange = React.useRef({
		startsAfter: sortFilterForm.data['starts_after'],
		startsBefore: sortFilterForm.data['starts_before'],
	});
	React.useEffect(() => {
		const startsAfter = sortFilterForm.data['starts_after'];
		const startsBefore = sortFilterForm.data['starts_before'];

		if (previousDateRange.current.startsAfter !== startsAfter || previousDateRange.current.startsBefore !== startsBefore) {
			previousDateRange.current = { startsAfter, startsBefore };
			sortFilterForm.submit();
		}
	}, [sortFilterForm.data['starts_after'], sortFilterForm.data['starts_before']]);
	const filterPane = (
		<FilterSortPane
			sorts={<Sorts sorts={sorts} form={sortFilterForm} />}
			showSortsOnDesktop
			filters={<AttendanceReportFilters eventTypes={eventTypes} voiceParts={voiceParts} form={sortFilterForm} />}
			closeFn={() => setShowFilters(false)}
		/>
	);

	const bulkEdit = {
		isActiveMobile: false,
		noun: 'Attendance',
		selectedIds: [],
		totalItems: events.length,
	};

	const breadcrumbs = [
		{ name: 'Events', url: route('events.index') },
		{ name: 'Attendance Report', url: route('events.reports.attendance') },
	];

	return (
		<>
			<AppHead title="Attendance Report" />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation breadcrumbs={breadcrumbs}></PageTopNavigation>
				{(filterAction || (events.length > 0 && numSingers > 0)) && (
					<PageActionsMenu>
						{events.length > 0 && numSingers > 0 && (
							<ActionMenuItem onClick={() => setIsChartCollapsed(prev => !prev)}>
								<Icon icon="analytics" mr />
								{isChartCollapsed ? 'Show chart' : 'Hide chart'}
							</ActionMenuItem>
						)}
						{filterAction && (
							<ActionMenuItem onClick={filterAction.onClick} intent={filterAction.intent}>
								<Icon icon="filter" mr />
								Filter
							</ActionMenuItem>
						)}
					</PageActionsMenu>
				)}
			</PageTopBar>
			<PageHeader>
				<PageHeaderContent>
					<PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
					<PageHeaderTitle>
						<Icon icon="analytics" type="solid" className="mr-2" />
						Attendance Report
					</PageHeaderTitle>
					<PageHeaderMeta>
						<div>Avg. singers per event: {avgSingersPerEvent}</div>
						<div>Avg. events per singer: {avgEventsPerSinger}</div>
					</PageHeaderMeta>
					<div className="mt-3 w-full max-w-xs">
						<DateRangeCalendarInput
							startDate={sortFilterForm.data['starts_after']}
							endDate={sortFilterForm.data['starts_before']}
							startMonth={new Date(2017, 0)}
							endMonth={new Date(new Date().getFullYear() + 5, 11)}
							updateFn={range => {
								sortFilterForm.setData('starts_after', range?.from ? DateTime.fromJSDate(range.from).toISODate() : '');
								sortFilterForm.setData('starts_before', range?.to ? DateTime.fromJSDate(range.to).toISODate() : '');
							}}
						>
							{setDraftRange => (
								<div className="mb-2 grid grid-cols-2">
									{[dateRangePresets.slice(0, 3), dateRangePresets.slice(3)].map((column, columnIndex) => (
										<div key={columnIndex} className={`flex flex-col gap-1 ${columnIndex === 1 ? 'border-l border-gray-200 pl-2' : 'pr-2'}`}>
											{column.map(({ label, start, end }) => (
												<Button
													key={label}
													intent="secondary"
													weight="clear"
													size="sm"
													onClick={() => setDraftRange({ from: start.toJSDate(), to: (end ?? today).toJSDate() })}
												>
													{label}
												</Button>
											))}
										</div>
									))}
								</div>
							)}
						</DateRangeCalendarInput>
					</div>
				</PageHeaderContent>
				<PageHeaderActions>
					{filterAction && (
						<Button
							onClick={filterAction.onClick}
							size="md"
							intent={filterAction.intent}
							weight="outline"
							className={filterAction.intent === 'success' ? 'bg-white' : undefined}
						>
							<Icon icon="filter" mr />
							Filter
						</Button>
					)}
					{events.length > 0 && numSingers > 0 && (
						<Button
							onClick={() => setIsChartCollapsed(prev => !prev)}
							size="md"
							intent="secondary"
							weight="solid"
						>
							<Icon icon="analytics" mr />
							{isChartCollapsed ? 'Show chart' : 'Hide chart'}
						</Button>
					)}
				</PageHeaderActions>
			</PageHeader>

			{events.length > 0 && numSingers > 0 && (
				<AttendanceChart events={events} isCollapsed={isChartCollapsed} onClose={() => setIsChartCollapsed(true)} />
			)}

			{!isDesktop && (
				<FilterDialog isOpen={showFilters} setIsOpen={setShowFilters}>
					{filterPane}
				</FilterDialog>
			)}
			<TableMobileHeader bulkEdit={bulkEdit} className="lg:hidden">
				<Button
					size="sm"
					intent={hasNonDefaultFilters ? 'success' : 'secondary'}
					weight="clear"
					onClick={() => setShowFilters(prev => !prev)}
				>
					<Icon icon="filter" mr />
					Filter/Sort
				</Button>
			</TableMobileHeader>

			<div className="flex min-w-0 flex-none flex-col divide-y divide-gray-300 lg:min-h-0 lg:flex-1 lg:overflow-auto lg:flex-row lg:divide-x lg:divide-y-0">
				{isDesktop && showFilters && (
					<div className="flex h-full shrink-0 flex-col lg:z-10 lg:w-1/5">{filterPane}</div>
				)}

				<div className="flex min-w-0 flex-none flex-col lg:min-h-0 lg:flex-1 lg:overflow-x-auto">
					<div className="min-w-0 flex-1">
						{events.length === 0 && (
							<EmptyState
								icon="calendar"
								title="No events found"
								description="Try expanding your filters, or maybe you haven't recorded any attendance yet."
							/>
						)}

						<div className="w-full min-w-0 overflow-x-auto pb-8">
							{events.length > 0 && numSingers === 0 && (
								<EmptyState
									icon="users"
									title="No singers found"
									description="Try expanding your filters, or maybe you haven't recorded any attendance yet."
								/>
							)}
							{events.length > 0 && numSingers > 0 && (
								<table className="h-full bg-white">
									<thead className="h-full">
										<tr className="h-full">
											<th />
											{events.map(event => (
												<th
													key={event.id}
													className="border border-gray-300 align-bottom h-full"
												>
													<Link
														href={route('events.show', { event })}
														className="flex px-2 md:px-2 py-3 md:py-5 justify-center transform rotate-180 text-purple-800 hover:bg-purple-100 hover:text-purple-600 h-full"
													>
														<div
															className="text-ellipsis overflow-hidden text-sm text-left"
															style={{ writingMode: 'vertical-lr' }}
														>
															{truncateString(event.title, 30)}
														</div>
													</Link>
												</th>
											))}
											<th className="px-2 md:px-2 py-3 md:py-5 border border-gray-300 align-bottom bg-gray-100">
												<div className="flex justify-center transform rotate-180">
													<div
														className="text-ellipsis overflow-hidden text-sm text-left"
														style={{ writingMode: 'vertical-lr' }}
													>
														Events Present
													</div>
												</div>
											</th>
										</tr>
										<tr>
											<th />
											{events.map(event => (
												<th
													key={event.id}
													className="font-medium text-gray-500 text-xs md:text-sm whitespace-nowrap border border-gray-300 px-1 md:px-5 py-2 md:py-3"
												>
													{DateTime.fromISO(event.start_date).toFormat('y')}
													<br />
													{DateTime.fromISO(event.start_date).toFormat('MM-dd')}
													<br />
												</th>
											))}
											<td className="border border-gray-300 bg-gray-100">&nbsp;</td>
										</tr>
									</thead>
									<tbody>
										{singers.map(singer => (
											<tr key={singer.id}>
												<th className="text-left whitespace-nowrap border border-gray-300">
													<Link
														href={route('singers.show', { singer })}
														className="flex flex-nowrap items-center px-3 md:px-5 py-2 md:py-3 gap-2 md:gap-3 hover:bg-purple-100 hover:text-purple-600 text-purple-800"
													>
														<div className="shrink-0 h-6 md:h-8 w-6 md:w-8">
															<img
																className="h-6 w-6 md:h-8 md: md:w-8 rounded-sm md:rounded-md"
																src={singer.user.avatar_url}
																alt={singer.user.name}
															/>
														</div>

														<div className="flex flex-col items-start gap-1">
															<span className="text-sm md:text-base">
																{singer.user.name}
															</span>
															{singer.enrolments[0]?.voice_part && (
																<VoicePartTag
																	title={singer.enrolments[0].voice_part.title}
																	colour={singer.enrolments[0].voice_part.colour}
																/>
															)}
														</div>
													</Link>
												</th>
												{events.map(event => {
													const attendance = getAttendanceBySingerAndEvent(singer, event);

													return (
														<td
															className="border border-gray-300 text-center"
															key={event.id}
														>
															{!event.isBeforeHistory &&
															!event.consideredSingerIds.includes(singer.id) ? (
																<span
																	className="text-xs text-gray-400"
																	title="Not an active member at the time"
																>
																	N/A
																</span>
															) : (
																<AttendanceTag
																	status={
																		attendance ? attendance.response : 'unknown'
																	}
																	hideLabel
																/>
															)}
														</td>
													);
												})}
												<td className="border border-gray-300 text-gray-500 bg-gray-100 text-center px-1 md:px-2 py-1 md:py-5">
													<div className="text-sm md:text-base">{singer.percentPresent}%</div>
													<div className="text-xs hidden md:block">
														{singer.timesPresent}&nbsp;/&nbsp;{singer.numEvents}
													</div>
												</td>
											</tr>
										))}
									</tbody>
									<tfoot>
										<tr>
											<th className="border border-gray-300 bg-gray-100 text-left px-2 md:px-2 py-3 md:py-3 text-xs md:text-base">
												Singers Present
											</th>
											{events.map(event => (
												<td
													className="border border-gray-300 text-gray-500 bg-gray-100 text-center px-1 md:px-2 py-1 md:py-3"
													key={event.id}
												>
													<div className="text-sm md:text-base">
														{event.percentPresent !== null
															? `${event.percentPresent}%`
															: 'N/A'}
													</div>
													<div className="text-xs hidden md:block">
														{event.singersPresent} / {event.numSingers}
													</div>
												</td>
											))}
										</tr>
									</tfoot>
								</table>
							)}
						</div>
					</div>
				</div>
			</div>
		</>
	);
};

AttendanceReport.layout = page => <TenantLayout children={page} />;

export default AttendanceReport;

const getAttendanceBySingerAndEvent = (singer, event) =>
	singer.attendances.filter(attendance => attendance.event_id === event.id)[0] ?? null;

const truncateString = (string = '', maxLength = 50) =>
	string.length > maxLength ? `${string.substring(0, maxLength)}…` : string;

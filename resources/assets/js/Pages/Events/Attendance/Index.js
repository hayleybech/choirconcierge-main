import React, { Fragment, useState } from 'react';

import PageHeader from '../../../components/PageHeader/PageHeader';
import TenantLayout from '../../../Layouts/TenantLayout';
import AppHead from '../../../components/AppHead';
import Icon from '../../../components/Icon';
import useRoute from '../../../hooks/useRoute';
import Dialog from '../../../components/Dialog';
import { Link, usePage } from '@inertiajs/react';
import QRCode from 'react-qr-code';
import DateTag from '../../../components/DateTag';
import Table, { TableCell, THead, TBody, TableHeading, TableSelectAll, TableCellSelect, TItemRow } from '../../../components/Table';
import IndexContainer from '../../../components/IndexContainer';
import AttendanceTableMobile from './AttendanceTableMobile';
import Pagination from '../../../components/Pagination';
import AttendanceRecord from '../../../components/Event/AttendanceRecord';
import useFilterPane from '../../../hooks/useFilterPane';
import useSortFilterForm from '../../../hooks/useSortFilterForm';
import FilterSortPane from '../../../components/FilterSortPane';
import Sorts from '../../../components/Sorts';
import AttendanceFilters from '../../../components/AttendanceFilters';
import TableHeadingSort from '../../../components/TableHeadingSort';
import VoicePartTag from '../../../components/VoicePartTag';
import Badge from '../../../components/Badge';
import SingerStatus from '../../../SingerStatus';
import SingerCategoryTag from '../../../components/SingerCategoryTag';
import { handleNameSort } from '../../../utils/sortHelpers';
import useBulkEdit from '../../../hooks/useBulkEdit';
import BulkEditBar from '../../../components/BulkEditBar';
import Button from '../../../components/inputs/Button';
import { Menu, Transition } from '@headlessui/react';
import menuItemStyles from '../../../components/ActionMenu/menuItemStyles';
import { router } from '@inertiajs/react';

const sourceLabels = {
	kiosk: 'Kiosk',
	'qr-code': 'QR Code',
	'after-event': 'Auto',
	manual: 'Manual',
};

const Index = ({
	event,
	allSingers,
	pagination,
	totalEnsemblesCount,
	voiceParts,
	ensembles,
	singerCategories,
	counts,
	individualCheckInUrl,
}) => {
	const [checkInDialogIsOpen, setCheckInDialogIsOpen] = useState(false);

	const { route } = useRoute();
	const { props: pageProps } = usePage();

	const [showFilters, setShowFilters, filterAction, hasNonDefaultFilters] = useFilterPane();

	const showEnsemble = event.ensembles.length > 0 || totalEnsemblesCount > 1;

	const sorts = [
		{ id: 'full-name', name: 'First Name', default: true },
		{ id: 'last-name-first', name: 'Last Name' },
		{ id: 'attendance-response', name: 'Attendance Response' },
		{ id: 'attendance-updated', name: 'Updated' },
	];

	const filters = [
		{ name: 'user.name', defaultValue: '' },
		{ name: 'enrolments.voice_part_id', multiple: true },
		{ name: 'enrolments.ensemble_id', multiple: true },
		{ name: 'attendance.response', multiple: true },
		{ name: 'category.id', multiple: true, defaultValue: singerCategories.find(c => c.name === 'Members')?.id ? [singerCategories.find(c => c.name === 'Members').id] : [] },
	];

	const sortFilterForm = useSortFilterForm(['events.attendances.index', { event: event.id }], filters, sorts);

	const bulkEdit = useBulkEdit(allSingers, pageProps.can.create_attendance, false, 'Singer', true);

	const bulkUpdateAttendance = response => {
		router.post(
			route('events.attendances.bulkUpdate', { event }),
			{
				singer_ids: bulkEdit.selectedIds,
				response,
			},
			{
				onSuccess: () => bulkEdit.clearSelections(),
			}
		);
	};

	const bulkActions = (
		<>
			<Button size="xs" variant="clear-inverse" onClick={() => bulkUpdateAttendance('present')}>
				<Icon icon="check" className="text-emerald-500" />
				Present
			</Button>
			<Button size="xs" variant="clear-inverse" onClick={() => bulkUpdateAttendance('absent')}>
				<Icon icon="times" className="text-red-500" />
				Absent
			</Button>
			<Menu as="div" className="relative flex">
				<Menu.Button className="flex items-center px-3 py-1.5 text-xs font-medium rounded-md hover:bg-gray-600 focus:outline-none transition ease-in-out duration-150">
					More
					<Icon icon="chevron-up" ml className="text-[10px]" />
				</Menu.Button>
				<Transition
					as={Fragment}
					enter="transition ease-out duration-100"
					enterFrom="transform opacity-0 scale-95"
					enterTo="transform opacity-100 scale-100"
					leave="transition ease-in duration-75"
					leaveFrom="transform opacity-100 scale-100"
					leaveTo="transform opacity-0 scale-95"
				>
					<Menu.Items className="origin-bottom-right absolute right-0 bottom-full mb-4 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none">
						<Menu.Item>
							{({ active }) => (
								<button
									onClick={() => bulkUpdateAttendance('late')}
									className={menuItemStyles('secondary', active, '', 'xs')}
								>
									<Icon icon="alarm-exclamation" mr className="text-amber-500" />
									Late
								</button>
							)}
						</Menu.Item>
						<Menu.Item>
							{({ active }) => (
								<button
									onClick={() => bulkUpdateAttendance('late_deemed_absent')}
									className={menuItemStyles('secondary', active, '', 'xs')}
								>
									<Icon icon="times" mr className="text-red-500" />
									Late (Deemed Absent)
								</button>
							)}
						</Menu.Item>
					</Menu.Items>
				</Transition>
			</Menu>
		</>
	);

	const countsData = [
		{ label: 'On Time', textColour: 'text-emerald-500', icon: 'check', count: counts.present },
		{ label: 'Late', textColour: 'text-amber-500', icon: 'alarm-exclamation', count: counts.late },
		{ label: 'Absent', textColour: 'text-red-500', icon: 'times', count: counts.absent },
		{ label: 'Not recorded', textColour: 'text-gray-500', icon: 'question', count: counts.unknown },
	];

	return (
		<>
			<AppHead title={`Attendance List - ${event.title}`} />
			<PageHeader
				title="Attendance List"
				icon="calendar"
				breadcrumbs={[
					{ name: 'Dashboard', url: route('dash') },
					{ name: 'Events', url: route('events.index') },
					{ name: event.title, url: route('events.show', { event }) },
					{ name: 'Attendance List', url: route('events.attendances.index', { event }) },
				]}
				actions={[
					{
						label: 'Kiosk',
						icon: 'calendar-check',
						url: route('events.kiosk-check-ins.index', { event }),
						can: 'create_attendance',
					},
					{
						label: 'QR Code',
						icon: 'qrcode',
						onClick: () => setCheckInDialogIsOpen(true),
						can: 'create_attendance',
					},
					filterAction,
					bulkEdit.action,
				].filter(action => (action?.can ? pageProps.can[action.can] : !!action))}
				optionsVariant={hasNonDefaultFilters ? 'success-solid' : 'secondary'}
				meta={
					<div>
						<p className="mb-2">We offer three ways to track attendance: </p>
						<ul className="list-disc list-inside ml-4 mb-4 [&>li]:mb-1">
							<li>
								<strong>Manual</strong> attendance tracking on this page
							</li>
							<li>
								<strong>Kiosk</strong> check-in for shared device use
							</li>
							<li>
								<strong>QR Code</strong> check-in for individual use
							</li>
						</ul>
						<p className="mb-2">
							Both device check-in pages automatically mark singers as late after the call time, or absent
							20 minutes later.
						</p>
						<p className="mb-2">
							If attendance was partially recorded during the event (either manually here or using the
							kiosk), remaining singers will automatically be marked absent once the event ends.
						</p>
						<p className="mb-2">The check-in pages also send an attendance report after each event.</p>
					</div>
				}
			/>

			<Dialog
				title="Individual Check-In Link"
				isOpen={checkInDialogIsOpen}
				setIsOpen={setCheckInDialogIsOpen}
				icon={null}
			>
				<div className="w-full">
					<p className="font-bold mb-2">Let singers check themselves in!</p>
					<p className="mb-2">
						They can scan this QR code while logged in to gain temporary access to the check-in page.
					</p>

					<div className="mb-2 flex justify-center">
						<QRCode value={individualCheckInUrl} />
					</div>
					<p className="break-all text-xs">{individualCheckInUrl}</p>
				</div>
			</Dialog>

			<BulkEditBar bulkEdit={bulkEdit} actions={bulkActions} />

			<div className="bg-white border-b border-gray-200 grid grid-cols-2 md:grid-cols-4">
				{countsData.map(({ label, textColour, icon, count }) => (
					<div
						className="text-center flex flex-col items-center justify-center py-2 lg:py-4 flex-1 border-gray-100"
						key={label}
					>
						<div className={`flex items-center gap-2 font-bold ${textColour} mb-1 text-sm md:text-base`}>
							<Icon icon={icon} />
							{label}
						</div>
						<span className="text-xl md:text-2xl font-bold text-gray-900">{count}</span>
					</div>
				))}
			</div>

			<IndexContainer
				showFilters={showFilters}
				filterPane={
					<FilterSortPane
						sorts={<Sorts sorts={sorts} form={sortFilterForm} />}
						filters={
							<AttendanceFilters
								event={event}
								voiceParts={voiceParts}
								ensembles={ensembles}
								form={sortFilterForm}
								singerCategories={singerCategories}
							/>
						}
						closeFn={() => setShowFilters(false)}
					/>
				}
				tableMobile={
					<AttendanceTableMobile
						singers={allSingers}
						pagination={pagination}
						showEnsemble={showEnsemble}
						event={event}
						bulkEdit={bulkEdit}
					/>
				}
				tableDesktop={
					<Table pagination={<Pagination details={pagination} />}>
						<THead>
							<tr>
								<TableSelectAll bulkEdit={bulkEdit} totalItems={allSingers.length} />
								<TableHeading>
									<TableHeadingSort
										form={sortFilterForm}
										sort={['full-name', 'last-name-first']}
										onClick={() => handleNameSort(sortFilterForm)}
										indicator={sortFilterForm.data.sort === 'full-name' ? 'First' : 'Last'}
									>
										Name
									</TableHeadingSort>
								</TableHeading>
								<TableHeading>Voice Part</TableHeading>
								<TableHeading>
									<TableHeadingSort form={sortFilterForm} sort="attendance-response">
										Attendance
									</TableHeadingSort>
								</TableHeading>
								<TableHeading>
									<TableHeadingSort form={sortFilterForm} sort="attendance-updated">
										Updated
									</TableHeadingSort>
								</TableHeading>
							</tr>
						</THead>
						<TBody>
							{allSingers.map(singer => (
								<TItemRow key={singer.id} bulkEdit={bulkEdit} value={singer.id}>
									<TableCellSelect bulkEdit={bulkEdit} value={singer.id} />
									<TableCell>
										<div className="flex items-center space-x-3">
											<div className="shrink-0">
												<img
													className="h-8 w-8 rounded-md object-cover"
													src={singer.user.avatar_url}
													alt={singer.user.name}
												/>
											</div>
											<div>
												<SingerCategoryTag status={new SingerStatus(singer.category.slug)} />
												<Link
													href={route('singers.show', { singer })}
													className="ml-1 text-sm font-medium text-purple-600 hover:text-purple-700 focus:text-purple-700 hover:underline focus:underline"
												>
													{singer.user.name}
												</Link>
											</div>
										</div>
									</TableCell>
									<TableCell>
										<ul className="flex flex-col gap-1.5">
											{singer.enrolments.map(enrolment => (
												<li key={enrolment.id} className="flex gap-1 items-center">
													{showEnsemble && (
														<Badge colour="bg-purple-100 text-purple-800">
															{enrolment.ensemble.name}
														</Badge>
													)}
													{enrolment.voice_part && (
														<VoicePartTag
															title={enrolment.voice_part.title}
															colour={enrolment.voice_part.colour}
														/>
													)}
												</li>
											))}
										</ul>
									</TableCell>
									<TableCell>
										<AttendanceRecord
											attendance={singer.attendance}
											singerId={singer.id}
											event={event}
										/>
									</TableCell>
									<TableCell>
										{!!singer.attendance.updated_at && (
											<div className="flex flex-col">
												<DateTag
													icon="pencil"
													label="Updated"
													date={singer.attendance.updated_at}
													format="DATETIME_SHORT"
													className="text-gray-400"
												/>
												{singer.attendance.source && (
													<div className="text-[10px] uppercase tracking-wider text-gray-400 mt-0.5 flex items-center gap-1">
														<Icon icon="info-circle" className="text-[9px]" />
														Via{' '}
														{sourceLabels[singer.attendance.source] ||
															singer.attendance.source}
													</div>
												)}
											</div>
										)}
									</TableCell>
								</TItemRow>
							))}
						</TBody>
					</Table>
				}
			/>
		</>
	);
};

Index.layout = page => <TenantLayout children={page} />;

export default Index;

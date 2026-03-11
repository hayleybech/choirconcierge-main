import React, { useState } from 'react';
import PageHeader from '../../../components/PageHeader/PageHeader';
import TenantLayout from '../../../Layouts/TenantLayout';
import Button from '../../../components/inputs/Button';
import AppHead from '../../../components/AppHead';
import AttendanceTag from '../../../components/Event/AttendanceTag';
import Icon from '../../../components/Icon';
import TextInput from '../../../components/inputs/TextInput';
import Label from '../../../components/inputs/Label';
import useRoute from '../../../hooks/useRoute';
import Dialog from '../../../components/Dialog';
import { usePage } from '@inertiajs/react';
import QRCode from 'react-qr-code';
import DateTag from '../../../components/DateTag';
import Table, { TableCell } from '../../../components/Table';
import IndexContainer from '../../../components/IndexContainer';
import AttendanceTableMobile from './AttendanceTableMobile';
import Pagination from '../../../components/Pagination';
import useFilterPane from '../../../hooks/useFilterPane';
import useSortFilterForm from '../../../hooks/useSortFilterForm';
import FilterSortPane from '../../../components/FilterSortPane';
import Sorts from '../../../components/Sorts';
import AttendanceFilters from '../../../components/AttendanceFilters';
import TableHeadingSort from '../../../components/TableHeadingSort';
import collect from 'collect.js';
import VoicePartTag from '../../../components/VoicePartTag';
import Badge from '../../../components/Badge';

const Index = ({
	event,
	allSingers,
	pagination,
	totalEnsemblesCount,
	voiceParts,
	ensembles,
	counts,
	individualCheckInUrl,
}) => {
	const [checkInDialogIsOpen, setCheckInDialogIsOpen] = useState(false);

	const { route } = useRoute();
	const { props: pageProps } = usePage();

	const [showFilters, setShowFilters, filterAction, hasNonDefaultFilters] = useFilterPane();

	const showEnsemble = event.ensembles.length > 0 || totalEnsemblesCount > 1;

	const sorts = [
		{ id: 'full-name', name: 'Name', default: true },
		{ id: 'attendance-response', name: 'Attendance Response' },
		{ id: 'attendance-updated', name: 'Updated' },
	];

	const filters = [
		{ name: 'user.name', defaultValue: '' },
		{ name: 'enrolments.voice_part_id', multiple: true },
		{ name: 'enrolments.ensemble_id', multiple: true },
		{ name: 'attendance.response', multiple: true },
	];

	const sortFilterForm = useSortFilterForm(['events.attendances.index', { event: event.id }], filters, sorts);

	const headings = collect({
		singer: (
			<TableHeadingSort form={sortFilterForm} sort="full-name">
				Name
			</TableHeadingSort>
		),
		voice_part: 'Voice Part',
		attendance: (
			<TableHeadingSort form={sortFilterForm} sort="attendance-response">
				Attendance
			</TableHeadingSort>
		),
		updated: (
			<TableHeadingSort form={sortFilterForm} sort="attendance-updated">
				Updated
			</TableHeadingSort>
		),
	});

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
						label: 'Individual Check-In',
						icon: 'qrcode',
						onClick: () => setCheckInDialogIsOpen(true),
						can: 'create_attendance',
					},
					filterAction,
				].filter(action => (action.can ? pageProps.can[action.can] : true))}
				optionsVariant={hasNonDefaultFilters ? 'success-solid' : 'secondary'}
				meta={
					<div>
						<p className="mb-2">Use this page to manually mark everyone's attendance. </p>
						<p className="mb-2">
							The kiosk page allows singers to mark themselves off from a shared device, while the
							individual check-in page allows singers to use their own device by scanning a QR code. Both
							check-in pages automatically mark singers as late after the call time, or absent 20 minutes
							later.
						</p>
						<p className="mb-2">
							If attendance was recorded during the event (either manually here or using the kiosk),
							remaining singers will automatically be marked absent once the event ends.
						</p>
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
							/>
						}
						closeFn={() => setShowFilters(false)}
					/>
				}
				tableMobile={
					<AttendanceTableMobile singers={allSingers} pagination={pagination} showEnsemble={showEnsemble} />
				}
				tableDesktop={
					<Table
						headings={headings}
						pagination={<Pagination details={pagination} />}
						body={allSingers.map(singer => (
							<tr key={singer.id}>
								<TableCell>
									<div className="flex items-center space-x-3">
										<div className="shrink-0">
											<img
												className="h-8 w-8 rounded-md object-cover"
												src={singer.user.avatar_url}
												alt={singer.user.name}
											/>
										</div>
										<div className="text-sm font-medium text-gray-900">{singer.user.name}</div>
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
										<DateTag
											icon="pencil"
											label="Updated"
											date={singer.attendance.updated_at}
											format="DATETIME_SHORT"
											className="text-gray-400"
										/>
									)}
								</TableCell>
							</tr>
						))}
					/>
				}
			/>
		</>
	);
};

Index.layout = page => <TenantLayout children={page} />;

export default Index;

const AttendanceRecord = ({ attendance, singerId, event }) => {
	const [absentReason, setAbsentReason] = useState(attendance.absent_reason || '');
	const [isEditing, setIsEditing] = useState(attendance.response === 'unknown');

	const { props: pageProps } = usePage();
	const { route } = useRoute();

	return (
		<div className="min-w-[200px]">
			{isEditing ? (
				<div className="flex flex-col space-y-2">
					<div className="flex flex-wrap gap-1">
						{[
							{
								response: 'present',
								label: 'On Time',
								icon: 'check',
								variant: 'success-outline',
							},
							{
								response: 'late',
								label: 'Late',
								icon: 'alarm-exclamation',
								variant: 'warning-outline',
							},
							{
								response: 'late_deemed_absent',
								label: 'Late (Deemed Absent)',
								icon: 'times',
								variant: 'danger-outline',
							},
						].map(
							({ response, label, icon, variant }) =>
								attendance.response !== response && (
									<Button
										href={route('events.attendances.update', {
											event,
											singer: singerId,
											tenant: pageProps.tenant,
										})}
										method="put"
										data={{
											response: response,
											absent_reason: absentReason,
										}}
										preserveScroll
										size="xs"
										variant={variant}
										key={response}
										title={label}
									>
										<Icon icon={icon} />
										{label}
									</Button>
								)
						)}
						{!['absent', 'absent_apology'].includes(attendance.response) && (
							<Button
								href={route('events.attendances.update', {
									event,
									singer: singerId,
									tenant: pageProps.tenant,
								})}
								method="put"
								data={{
									response: !!absentReason ? 'absent_apology' : 'absent',
									absent_reason: absentReason,
								}}
								preserveScroll
								size="xs"
								variant="danger-outline"
								title="Mark as Absent"
							>
								<Icon icon="times" />
								Absent
							</Button>
						)}
					</div>

					{!['absent', 'absent_apology'].includes(attendance.response) && (
						<div className="flex flex-col gap-1">
							<TextInput
								name="absent_reason"
								id={`absent_reason_${singerId}`}
								value={absentReason}
								updateFn={value => setAbsentReason(value)}
								placeholder="Reason for absence (Optional)"
								className="text-xs py-1"
							/>
						</div>
					)}

					{attendance.response !== 'unknown' && (
						<Button variant="secondary" size="xs" onClick={() => setIsEditing(false)}>
							Cancel
						</Button>
					)}
				</div>
			) : (
				<div className="flex flex-col">
					<div className="flex gap-2 items-center">
						<AttendanceTag label={attendance.label} icon={attendance.icon} colour={attendance.colour} />
						<Button
							variant="secondary"
							size="xs"
							onClick={() => setIsEditing(true)}
							className="px-1.5 py-0.5"
						>
							<Icon icon="edit" /> Edit
						</Button>
					</div>
					{attendance.response.includes('absent') && attendance.absent_reason && (
						<div
							className="text-[11px] text-gray-500 italic mt-1 max-w-[200px] truncate"
							title={attendance.absent_reason}
						>
							Reason: {attendance.absent_reason}
						</div>
					)}
				</div>
			)}
		</div>
	);
};

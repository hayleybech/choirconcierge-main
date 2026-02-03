import React, { useState } from 'react';
import PageHeader from '../../../components/PageHeader/PageHeader';
import TenantLayout from '../../../Layouts/TenantLayout';
import Button from '../../../components/inputs/Button';
import AppHead from '../../../components/AppHead';
import AttendanceTag from '../../../components/Event/AttendanceTag';
import Icon from '../../../components/Icon';
import TextInput from '../../../components/inputs/TextInput';
import Label from '../../../components/inputs/Label';
import CollapseGroup from '../../../components/CollapseGroup';
import useRoute from '../../../hooks/useRoute';
import Dialog from '../../../components/Dialog';
import { usePage } from '@inertiajs/react';
import QRCode from 'react-qr-code';
import DateTag from '../../../components/DateTag';

const Index = ({ event, voice_parts, individualCheckInUrl }) => {
	const [checkInDialogIsOpen, setCheckInDialogIsOpen] = useState(false);

	const { route } = useRoute();
	const { props: pageProps } = usePage();

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
				].filter(action => (action.can ? pageProps.can[action.can] : true))}
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

			<CollapseGroup
				items={voice_parts.map(part => ({
					title: part.title,
					show: true,
					defaultOpen: true,
					content: (
						<div key={part.id} className="relative">
							<div className="flex justify-center flex-wrap bg-white py-4 border-b border-gray-200 gap-y-6">
								{[
									{
										label: 'On Time',
										colour: 'emerald',
										icon: 'check',
										count: part.members.filter(attendance => attendance.response === 'present')
											.length,
									},
									{
										label: 'Late',
										colour: 'amber',
										icon: 'alarm-exclamation',
										count: part.members.filter(attendance => attendance.response === 'late').length,
									},
									{
										label: 'Absent',
										colour: 'red',
										icon: 'times',
										count: part.members.filter(
											attendance =>
												attendance.response === 'absent' ||
												attendance.response === 'absent_apology' ||
												attendance.response === 'late_deemed_absent'
										).length,
									},
									{
										label: 'Unknown',
										colour: 'gray',
										icon: 'question',
										count: part.members.filter(attendance => attendance.response === 'unknown')
											.length,
									},
								].map(({ label, colour, icon, count }) => (
									<div
										className="w-1/3 md:w-1/5 text-center flex flex-col items-center justify-between"
										key={label}
									>
										<div className="hidden md:block">
											<AttendanceTag
												label={label}
												icon={icon}
												colour={colour}
												size="md"
												className="font-bold block"
											/>
										</div>
										<div
											className={`flex flex-col items-center md:hidden font-bold text-${colour}-500`}
										>
											<Icon icon={icon} className="text-lg" />
											{label}
										</div>
										{count}
									</div>
								))}
							</div>
							<ul role="list" className="relative z-0 divide-y divide-gray-200">
								{part.members.map(attendance => (
									<AttendanceRecord
										key={attendance.member.id}
										attendance={attendance}
										event={event}
									/>
								))}
							</ul>
						</div>
					),
				}))}
			/>
		</>
	);
};

Index.layout = page => <TenantLayout children={page} />;

export default Index;

const AttendanceRecord = ({ attendance, event }) => {
	const [absentReason, setAbsentReason] = useState();
	const [isEditing, setIsEditing] = useState(attendance.response === 'unknown');

	const { props: pageProps } = usePage();

	return (
		<li className="bg-white">
			<div className="relative px-6 py-5 flex flex-col xl:flex-row items-stretch xl:items-center gap-y-3 sm:gap-x-3 hover:bg-gray-50 justify-between">
				<div className="flex space-x-2 shrink-0 grow items-center">
					<div className="shrink-0">
						<img
							className="h-12 w-12 rounded-lg"
							src={attendance.member.user.avatar_url}
							alt={attendance.member.user.name}
						/>
					</div>
					<div className="shrink-0">
						<p className="text-sm font-medium text-gray-900">{attendance.member.user.name}</p>
					</div>
				</div>
				{isEditing ? (
					<div className="shrink-0 flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2 items:stretch sm:items-end">
						<div className="text-sm text-gray-700 mr-2 mb-2">Mark as:</div>
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
											singer: attendance.member.id,
											tenant: pageProps.tenant,
										})}
										method="put"
										data={{
											response: response,
											absent_reason: absentReason,
										}}
										preserveScroll
										size="sm"
										variant={variant}
										key={response}
									>
										<Icon icon={icon} />
										{label}
									</Button>
								)
						)}

						{!['absent', 'absent_apology'].includes(attendance.response) && (
							<>
								<div className="flex flex-col items-stretch gap-0.5 sm:gap-2">
									<Label
										label="Reason for absence (Optional)"
										forInput={`absent_reason_${attendance.member.id}`}
									/>
									<TextInput
										name="absent_reason"
										id={`absent_reason_${attendance.member.id}`}
										value={absentReason}
										updateFn={value => setAbsentReason(value)}
										wrapperClasses="grow"
									/>
								</div>

								<Button
									href={route('events.attendances.update', {
										event,
										singer: attendance.member.id,
										tenant: pageProps.tenant,
									})}
									method="put"
									data={{
										response: !!absentReason ? 'absent_apology' : 'absent',
										absent_reason: absentReason,
									}}
									preserveScroll
									size="sm"
									variant="danger-outline"
								>
									<Icon icon="times" />
									Absent
								</Button>
							</>
						)}
					</div>
				) : (
					<div className="flex flex-col">
						<div className="flex gap-2 items-center">
							<AttendanceTag label={attendance.label} icon={attendance.icon} colour={attendance.colour} />
							<Button variant="primary" size="xs" onClick={() => setIsEditing(true)}>
								<Icon icon="edit" />
								Change
							</Button>
						</div>
						{!!attendance.updated_at && (
							<DateTag
								icon="pencil"
								label="Updated"
								date={attendance.updated_at}
								format="DATETIME_SHORT"
								className="text-sm text-gray-400"
							/>
						)}
						{attendance.response.includes('absent') && attendance.absent_reason && (
							<div className="text-sm text-gray-500">Reason for absence: {attendance.absent_reason}</div>
						)}
					</div>
				)}
			</div>
		</li>
	);
};

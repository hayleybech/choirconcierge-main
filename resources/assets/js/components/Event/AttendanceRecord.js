import React, { useState } from 'react';
import Button from '../inputs/Button';
import AttendanceTag from './AttendanceTag';
import Icon from '../Icon';
import TextInput from '../inputs/TextInput';
import useRoute from '../../hooks/useRoute';
import { usePage } from '@inertiajs/react';

const AttendanceRecord = ({ attendance, singerId, event }) => {
	const [absentReason, setAbsentReason] = useState(attendance.absent_reason || '');
	const [isEditing, setIsEditing] = useState(attendance.response === 'unknown');

	const { props: pageProps } = usePage();
	const { route } = useRoute();

	return (
		<div className={`sm:min-w-[200px] ${isEditing ? 'w-full mt-2 lg:mt-0 lg:w-auto' : 'shrink-0'}`}>
			{isEditing ? (
				<div className="flex flex-col space-y-0.5">
					<div className="grid grid-cols-2 lg:flex lg:flex-wrap gap-1">
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
								className="text-xs"
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

export default AttendanceRecord;

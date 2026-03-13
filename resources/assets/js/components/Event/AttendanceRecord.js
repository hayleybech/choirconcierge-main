import React, { useState } from 'react';
import Button from '../inputs/Button';
import AttendanceTag from './AttendanceTag';
import Icon from '../Icon';
import TextInput from '../inputs/TextInput';
import RadioGroup from '../inputs/RadioGroup';
import useRoute from '../../hooks/useRoute';
import { usePage, router } from '@inertiajs/react';
import { useMediaQuery } from 'react-responsive';

const AttendanceRecord = ({ attendance, singerId, event }) => {
	const [absentReason, setAbsentReason] = useState(attendance.absent_reason || '');
	const [response, setResponse] = useState(attendance.response);
	const [isEditing, setIsEditing] = useState(attendance.response === 'unknown');

	const { props: pageProps } = usePage();
	const { route } = useRoute();

	const isDesktop = useMediaQuery({ query: '(min-width: 1024px)' });

	const options = [
		{
			id: 'present',
			name: 'On Time',
			icon: 'check',
			colour: 'text-green-600',
		},
		{
			id: 'late',
			name: 'Late',
			icon: 'alarm-exclamation',
			colour: 'text-amber-500',
		},
		{
			id: 'late_deemed_absent',
			name: 'Late (Deemed Absent)',
			icon: 'times',
			colour: 'text-red-500',
		},
		{
			id: 'absent',
			name: 'Absent',
			icon: 'times',
			colour: 'text-red-500',
		},
	];

	const handleSubmit = () => {
		router.put(
			route('events.attendances.update', {
				event,
				singer: singerId,
				tenant: pageProps.tenant,
			}),
			{
				response: response === 'absent' && !!absentReason ? 'absent_apology' : response,
				absent_reason: absentReason,
			},
			{
				preserveScroll: true,
				onSuccess: () => setIsEditing(false),
			}
		);
	};

	return (
		<div className={`sm:min-w-[200px] ${isEditing ? 'w-full mt-2 lg:mt-0 lg:w-auto' : 'shrink-0'}`}>
			{isEditing ? (
				<div className="flex flex-col space-y-1.5">
					<RadioGroup
						options={options}
						selected={response === 'absent_apology' ? 'absent' : response}
						setSelected={setResponse}
						vertical={!isDesktop}
						contentVertical={false}
						size="xs"
					/>

					{(response.includes('absent') || response === 'late') && (
						<div className="flex flex-col gap-1">
							<TextInput
								name="absent_reason"
								id={`absent_reason_${singerId}`}
								value={absentReason}
								updateFn={value => setAbsentReason(value)}
								placeholder="Reason for absence (Optional)"
								className="text-xs"
								size="sm"
							/>
						</div>
					)}

					<div className="flex gap-1.5">
						<Button size="xs" onClick={handleSubmit}>
							Submit
						</Button>
						{attendance.response !== 'unknown' && (
							<Button variant="secondary" size="xs" onClick={() => setIsEditing(false)}>
								Cancel
							</Button>
						)}
					</div>
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

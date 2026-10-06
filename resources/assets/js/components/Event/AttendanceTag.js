import React from 'react';
import Icon from '../Icon';

const attendanceStatuses = {
	present: { label: 'On Time', colour: 'emerald', icon: 'check', type: 'solid' },
	late: { label: 'Late', colour: 'amber', icon: 'alarm-snooze', type: 'solid' },
	late_deemed_absent: { label: 'Late (Deemed Absent)', colour: 'red', icon: 'alarm-exclamation', type: 'solid' },
	absent: { label: 'Absent', colour: 'red', icon: 'times', type: 'solid' },
	unknown: { label: 'Not recorded', colour: 'gray', icon: 'circle', type: 'regular' },
};

const AttendanceTag = ({ status, size = 'sm', className = '', hideLabel = false }) => {
	const { label, colour, icon, type } = attendanceStatuses[status];

	return (
		<span className={`text-${size} text-${colour}-500 ${className}`} title={label}>
			<Icon icon={icon} mr={!!label} type={type} />
			{!hideLabel && label}
		</span>
	);
};

export default AttendanceTag;

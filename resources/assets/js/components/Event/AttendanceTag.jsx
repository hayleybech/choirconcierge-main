import React from 'react';
import Icon from '../Icon';
import Tooltip from '../Tooltip';
 
const attendanceStatuses = {
	present: { label: 'On Time', colour: 'emerald', icon: 'check', type: 'solid' },
	late: { label: 'Late', colour: 'yellow', icon: 'alarm-snooze', type: 'solid' },
	late_deemed_absent: { label: 'Late (Deemed Absent)', colour: 'orange', icon: 'alarm-exclamation', type: 'solid' },
	absent: { label: 'Absent', colour: 'red', icon: 'times', type: 'solid' },
	unknown: { label: 'Not recorded', colour: 'gray', icon: 'circle', type: 'regular' },
};

const AttendanceTag = ({ status, size = 'sm', className = '', hideLabel = false }) => {
	const { label, colour, icon, type } = attendanceStatuses[status];

	return (
		<Tooltip content={hideLabel ? label : null}>
			<span className={`text-${size} text-${colour}-500 ${className}`} tabIndex={hideLabel ? 0 : undefined}>
				<Icon icon={icon} mr={!!label} type={type} />
				{!hideLabel && label}
			</span>
		</Tooltip>
	);
};

export default AttendanceTag;

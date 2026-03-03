import React from 'react';
import SingerAttendanceSummary from '../../../components/Attendance/SingerAttendanceSummary';

export const AttendanceSection = ({ attendanceSummary }) => {
	return <SingerAttendanceSummary attendanceSummary={attendanceSummary} />;
};

export default AttendanceSection;

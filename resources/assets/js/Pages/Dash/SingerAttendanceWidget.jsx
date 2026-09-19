import Panel, { PanelTitle } from '../../components/Panel';
import React from 'react';
import SingerAttendanceSummary from '../../components/Attendance/SingerAttendanceSummary';
import SingerRsvpSummary from '../../components/Attendance/SingerRsvpSummary';

export const SingerAttendanceWidget = ({ attendanceSummary, rsvpSummary, performanceTypeId }) => {
	return (
		<Panel header={<PanelTitle>Attendance</PanelTitle>} noPadding>
			<div className="py-3 px-4 lg:px-6">
			<SingerAttendanceSummary attendanceSummary={attendanceSummary} />
			</div>
			<hr className="border-gray-200" />
			<div className="py-3 px-4 lg:px-6">
			<SingerRsvpSummary rsvpSummary={rsvpSummary} performanceTypeId={performanceTypeId} />
			</div>
		</Panel>
	);
};

export default SingerAttendanceWidget;

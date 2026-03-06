import Panel, { PanelTitle } from '../../components/Panel';
import React from 'react';
import Icon from '../../components/Icon';
import ButtonLink from '../../components/inputs/ButtonLink';
import SingerAttendanceSummary from '../../components/Attendance/SingerAttendanceSummary';
import useRoute from '../../hooks/useRoute';
import { usePage } from '@inertiajs/react';
import SingerRsvpSummary from '../../components/Attendance/SingerRsvpSummary';

export const SingerAttendanceWidget = ({ attendanceSummary, rsvpSummary, performanceTypeId }) => {
	return (
		<Panel header={<PanelTitle>Attendance</PanelTitle>} noPadding>
			<SingerAttendanceSummary attendanceSummary={attendanceSummary} />
			<hr className="border-gray-200" />
			<SingerRsvpSummary rsvpSummary={rsvpSummary} performanceTypeId={performanceTypeId} />
		</Panel>
	);
};

export default SingerAttendanceWidget;

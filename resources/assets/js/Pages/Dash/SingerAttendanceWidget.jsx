import Panel, { PanelTitle } from '../../components/Panel';
import React from 'react';
import Icon from '../../components/Icon';
import ButtonLink from '../../components/inputs/ButtonLink';
import SingerAttendanceSummary from '../../components/Attendance/SingerAttendanceSummary';
import useRoute from '../../hooks/useRoute';
import { usePage } from '@inertiajs/react';

export const SingerAttendanceWidget = ({ attendanceSummary }) => {
	const { route } = useRoute();

	const { user: authUser } = usePage().props;

	return (
		<Panel
			header={
				<div className="flex justify-between items-center">
					<PanelTitle>Attendance</PanelTitle>

					<ButtonLink variant="secondary" size="xs" href={route('singers.attendance', { singer: authUser.membership })}>
						<Icon icon="clipboard-list" />
						View All
					</ButtonLink>
				</div>
			}
			noPadding
		>
			<SingerAttendanceSummary attendanceSummary={attendanceSummary} />
		</Panel>
	);
}

export default SingerAttendanceWidget;
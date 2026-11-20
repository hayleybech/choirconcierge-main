import React from 'react';
import Table, { TableCell, THead, TBody, TableHeading, TItemRow } from '../../../components/Table';
import EventTypeBadge from '../../../components/EventTypeBadge';
import AttendanceTag from "../../../components/Event/AttendanceTag";
import Pagination from "../../../components/Pagination";
import useRoute from "../../../hooks/useRoute";
import { Link } from '@inertiajs/react';
import DateTag from '../../../components/DateTag';
 
const AttendanceTableDesktop = ({ attendances, pagination }) => {
	const { route } = useRoute();

	return (
		<Table pagination={<Pagination details={pagination} />}>
			<THead>
				<tr>
					<TableHeading>Event</TableHeading>
					<TableHeading>Type</TableHeading>
					<TableHeading>Date</TableHeading>
					<TableHeading>Attendance</TableHeading>
				</tr>
			</THead>
			<TBody>
				{attendances.filter(attendance => !!attendance.event).map(attendance => (
					<TItemRow key={attendance.id} bulkEdit={{ isAllowed: false, selectedIds: [] }}>
						<TableCell>
							<Link
								href={route('events.show', { event: attendance.event.id })}
								className="text-sm font-medium text-purple-800"
							>
								{attendance.event.title}
							</Link>
						</TableCell>
						<TableCell>
							<EventTypeBadge eventTypeName={attendance.event.type.title} />
						</TableCell>
						<TableCell>
							<DateTag date={attendance.event.start_date} />
						</TableCell>
						<TableCell>
							<AttendanceTag status={attendance.response} />
						</TableCell>
					</TItemRow>
				))}
			</TBody>
		</Table>
	);
};

export default AttendanceTableDesktop;

import React from 'react';
import Table, { TableCell, THead, TBody, TableHeading, TItemRow } from '../../../components/Table';
import { DateTime } from 'luxon';
import Badge from "../../../components/Badge";
import EventType from "../../../EventType";
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
							<Badge colour={new EventType(attendance.event.type.title).badgeColour}>
								{attendance.event.type.title}
							</Badge>
						</TableCell>
						<TableCell>
							<DateTag date={attendance.event.start_date} />
						</TableCell>
						<TableCell>
							<AttendanceTag
								icon={attendance.icon}
								colour={attendance.colour}
								label={attendance.label}
							/>
						</TableCell>
					</TItemRow>
				))}
			</TBody>
		</Table>
	);
};

export default AttendanceTableDesktop;

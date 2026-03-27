import React from 'react';
import TableMobile, {
	TableMobileHeader,
	TableMobileListItem,
	TableMobileLink,
} from '../../../components/TableMobile';
import Icon from "../../../components/Icon";
import useRoute from "../../../hooks/useRoute";
import Pagination from '../../../components/Pagination';
import Button from '../../../components/inputs/Button';
import { DateTime } from 'luxon';
import Badge from "../../../components/Badge";
import EventType from "../../../EventType";
import AttendanceTag from "../../../components/Event/AttendanceTag";
import DateTag from '../../../components/DateTag';

const AttendanceTableMobile = ({ attendances, pagination, hasNonDefaultFilters, setShowFilters }) => {
	const { route } = useRoute();

	return (
		<div>
			<TableMobileHeader bulkEdit={{ isAllowed: false, isActiveMobile: false, noun: 'Attendance Record' }}>
				<Button
					variant={hasNonDefaultFilters ? 'success-outline' : 'clear-v2'}
					size="xs"
					onClick={() => setShowFilters(prev => !prev)}
				>
					<Icon icon="filter" mr />
					Filter/Sort
				</Button>
			</TableMobileHeader>
			<TableMobile pagination={<Pagination details={pagination} />}>
				{attendances.filter(attendance => !!attendance.event).map(attendance => (
					<TableMobileListItem key={attendance.id}>
						<TableMobileLink
							url={route('events.show', { event: attendance.event.id })}
						>
							<div className="min-w-0 flex-1 pr-2 lg:grid lg:grid-cols-2 lg:gap-4">
								<div>
									<div className="flex items-center justify-between">
										<p className="flex items-center min-w-0 mr-1.5 truncate text-sm font-medium text-purple-600">
											{attendance.event.title}
										</p>
										<Badge colour={new EventType(attendance.event.type.title).badgeColour}>
											{attendance.event.type.title}
										</Badge>
									</div>
									<div className="flex items-center justify-between mt-2">
										<div className="flex items-center text-sm text-gray-500 min-w-0">
											<DateTag date={attendance.event.start_date} />
										</div>
										<AttendanceTag
											icon={attendance.icon}
											colour={attendance.colour}
											label={attendance.label}
										/>
									</div>
								</div>
							</div>
						</TableMobileLink>
					</TableMobileListItem>
				))}
			</TableMobile>
		</div>
	);
}

export default AttendanceTableMobile;

import React from 'react';
import TableMobile, {
	TableMobileListItem,
	TableMobileSelect,
	TableMobileSelectableLink,
} from "../../components/TableMobile";
import Badge from "../../components/Badge";
import {DateTime} from "luxon";
import Icon from "../../components/Icon";
import DateTag from "../../components/DateTag";
import EventType from "../../EventType";
import useRoute from "../../hooks/useRoute";
import Pagination from '../../components/Pagination';
import BulkEditBarMobile from '../../components/BulkEditBarMobile';

const EventTableMobile = ({ events, userEnsemblesCount, bulkEdit }) => {
	const { route } = useRoute();

	return (
		<div>
			<BulkEditBarMobile totalItems={events.data.length} bulkEdit={bulkEdit} />
			<TableMobile pagination={<Pagination details={events} />}>
				{events.data.map(event => (
					<TableMobileListItem key={event.id}>
						<div className="flex items-center">
							<TableMobileSelect bulkEdit={bulkEdit} value={event.id} />
							<div className="flex-1 min-w-0">
								<TableMobileSelectableLink
									url={route('events.show', { event })}
									bulkEdit={bulkEdit}
									value={event.id}
								>
									<div className="min-w-0 flex-1 px-4 lg:grid lg:grid-cols-2 lg:gap-4">
										<div className="flex items-center justify-between">
											<p className="flex items-center min-w-0 mr-1.5">
												<span className="text-sm font-medium text-purple-600 truncate">
													{event.title}
													{event.is_repeating && (
														<Icon
															icon={event.is_repeat_parent ? 'repeat-1' : 'repeat'}
															className="ml-1.5"
														/>
													)}
												</span>
											</p>
											<div className="text-xs text-gray-500">
												{DateTime.fromJSDate(new Date(event.call_time)) < DateTime.now() ? (
													<p>{event.present_count}&nbsp;present</p>
												) : (
													<p>{event.going_count}&nbsp;going</p>
												)}
											</div>
										</div>
										<div className="flex items-center justify-between">
											<p className="mt-1.5 flex items-center text-xs text-gray-500 min-w-0">
												<DateTag date={event.call_time} format="DATETIME_MED_WITH_WEEKDAY" />
											</p>

											<p className="mt-2 flex items-center text-sm text-gray-500 min-w-0">
												<Badge colour={new EventType(event.type.title).badgeColour}>
													{event.type.title}
												</Badge>
											</p>
										</div>
									</div>
								</TableMobileSelectableLink>
								{userEnsemblesCount > 1 && event.ensembles.length > 0 && (
									<div className="mt-2 flex gap-1 flex-wrap pl-4 mb-3">
										{event.ensembles.map(ensemble => (
											<Badge key={ensemble.id} colour="bg-purple-100 text-purple-800 truncate" display="">
												{ensemble.name}
											</Badge>
										))}
									</div>
								)}
							</div>
						</div>
					</TableMobileListItem>
				))}
			</TableMobile>
		</div>
	);
};

export default EventTableMobile;
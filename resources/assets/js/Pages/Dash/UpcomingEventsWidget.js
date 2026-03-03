import React, { useState } from 'react';
import Panel, { PanelTitle } from '../../components/Panel';
import TableMobile, { TableMobileLink, TableMobileListItem } from '../../components/TableMobile';
import DateTag from '../../components/DateTag';
import { DateTime } from 'luxon';
import GoogleMap from '../../components/GoogleMap';
import ButtonLink from '../../components/inputs/ButtonLink';
import Icon from '../../components/Icon';
import { usePage } from '@inertiajs/react';
import useRoute from '../../hooks/useRoute';
import Button from '../../components/inputs/Button';
import RsvpDropdown from '../../components/Event/RsvpDropdown';
import Dialog from '../../components/Dialog';
import Label from '../../components/inputs/Label';
import CheckboxGroup from '../../components/inputs/CheckboxGroup';
import SingerRsvpSummary from '../../components/Attendance/SingerRsvpSummary';

const UpcomingEventsWidget = ({ events, eventCategories, rsvpSummary }) => {
	const { can, tenant } = usePage().props;
	const { route } = useRoute();

	const [dialogIsOpen, setDialogIsOpen] = useState(false);

	return (
		<Panel
			header={
				<div className="flex justify-between items-center">
					<PanelTitle>Upcoming Events</PanelTitle>

					<div className="flex gap-1 items-center">
						{can['create_event'] && (
							<>
								<Button variant="secondary" size="xs" onClick={() => setDialogIsOpen(true)}>
									<Icon icon="filter" style={{ lineHeight: '1rem' }} />
									<span className="hidden lg:inline">Filter</span>
								</Button>

								<CustomiseEventsWidgetDialog
									isOpen={dialogIsOpen}
									setIsOpen={setDialogIsOpen}
									allCategories={eventCategories}
									initialCategories={tenant.widgets_upcoming_events_categories ?? []}
								/>
							</>
						)}
						<Button href={route('events.index')} variant="secondary" size="xs">
							<Icon icon="list" style={{ lineHeight: '1rem' }} />
							<span className="hidden sm:inline">View All</span>
						</Button>
					</div>
				</div>
			}
			noPadding
		>
			<>
				<SingerRsvpSummary rsvpSummary={rsvpSummary} />

				{events.length > 0 ? (
					<TableMobile>
						{events.map(event => (
							<TableMobileListItem key={event.id}>
								{isToday(event) && (
									<div className="flex items-center justify-between mt-4 -mb-2 px-4 sm:px-6 z-10">
										<div className="text-md font-bold mr-2">Today</div>

										{can['create_attendance'] && (
											<ButtonLink
												href={route('events.attendances.index', { event })}
												variant="primary"
												size="xs"
												className="mt-2"
											>
												<Icon icon="edit" />
												Record Attendance
											</ButtonLink>
										)}
									</div>
								)}

								<TableMobileLink url={route('events.show', { event })}>
									<div className="flex-1 flex flex-col mr-2 sm:mr-4">
										<div className="flex items-center justify-between gap-1">
											<div className="text-sm font-medium text-purple-800">{event.title}</div>
											<div className="text-sm hidden xl:block shrink-0">
												<DateTag
													date={event.call_time}
													format={isToday(event) ? 'TIME_24_SIMPLE' : 'DATE_MED'}
												/>
											</div>
											<div className="text-sm xl:hidden shrink-0">
												<DateTag
													date={event.call_time}
													format={isToday(event) ? 'TIME_24_SIMPLE' : 'DATE_SHORT'}
												/>
											</div>
										</div>
										{isToday(event) && (
											<div className="mt-2">
												<p className="text-sm text-gray-500 font-bold">{event.location_name}</p>
												<p className="text-sm text-gray-500">{event.location_address}</p>
												{event.location_place_id && <GoogleMap placeId={event.location_place_id} />}
											</div>
										)}
									</div>
								</TableMobileLink>

								{!isToday(event) && (
									<div className="-mt-2 mb-4 self-stretch md:self-start px-4 sm:px-6">
										<RsvpDropdown event={event} size="xs" />
									</div>
								)}
							</TableMobileListItem>
						))}
					</TableMobile>
				) : (
					<p className="px-4 py-4 sm:px-6">No events this month.</p>
				)}
			</>
		</Panel>
	);
};

export default UpcomingEventsWidget;

function isToday(event) {
	return DateTime.fromISO(event.call_time).hasSame(DateTime.now(), 'day');
}

const CustomiseEventsWidgetDialog = ({ isOpen, setIsOpen, allCategories, initialCategories }) => {
	const [selectedEventCategories, setSelectedEventCategories] = useState(initialCategories);

	const { route } = useRoute();

	return (
		<Dialog
			title="Filter Events Widget"
			okLabel="Save"
			okUrl={route('widgets.upcoming-events')}
			okVariant="primary"
			okMethod="put"
			data={{
				event_categories: selectedEventCategories,
			}}
			isOpen={isOpen}
			setIsOpen={setIsOpen}
		>
			<p className="mb-2">Select which event categories your singers should see in this widget.</p>
			<fieldset className="mb-2 px-1">
				{/*<legend className="text-base font-medium text-gray-900">Roles</legend>*/}
				<Label forInput="event_categories">Categories</Label>
				<CheckboxGroup
					name="event_categories"
					options={allCategories.map(category => ({ id: category.id, name: category.title }))}
					value={selectedEventCategories}
					updateFn={value => setSelectedEventCategories(value)}
				/>
				{/*{errors.event_categories && <Error>{errors.event_categories}</Error>}*/}
			</fieldset>
		</Dialog>
	);
};

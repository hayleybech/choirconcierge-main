import React, { useState } from 'react';
import { useMediaQuery } from 'react-responsive';
import EventScheduleDesktop from './EventScheduleDesktop';
import EventScheduleMobile from './EventScheduleMobile';
import EmptyState from '../EmptyState';
import ScheduleItemForm from '../ScheduleItemForm';
import Button from '../inputs/Button';
import Icon from '../Icon';

const EventSchedule = ({ event }) => {
	const isDesktop = useMediaQuery({ query: '(min-width: 1024px)' });
	const [addActivityDialogIsOpen, setAddActivityDialogIsOpen] = useState(false);

	const openAddActivityDialog = () => setAddActivityDialogIsOpen(true);

	return (
		<div>
			{event.activities.length > 0 ? (
				isDesktop ? (
					<EventScheduleDesktop event={event} onAddActivity={openAddActivityDialog} />
				) : (
					<EventScheduleMobile event={event} onAddActivity={openAddActivityDialog} />
				)
			) : (
				<EmptyState
					title="Empty schedule"
					description={
						<>
							Looks like this event doesn't have a schedule. This tool is great for rehearsals and
							performances. <br />
							Schedules can assign songs and estimate the event duration.
						</>
					}
					actionDescription={event.can['update_event'] ? 'To get started, use the form below.' : null}
					icon="stream"
				/>
			)}

			{event.can.update_event && event.activities.length === 0 && (
				<ul role="list" className="relative z-0 divide-y divide-gray-200">
					<li className="bg-white hover:bg-purple-100">
						<Button
							variant="clear"
							size="sm"
							className="w-full justify-start px-6 py-5 text-purple-600"
							onClick={openAddActivityDialog}
						>
							<Icon icon="plus" />
							Add activity
						</Button>
					</li>
				</ul>
			)}

			{event.can.update_event && (
				<ScheduleItemForm
					event={event}
					isOpen={addActivityDialogIsOpen}
					setIsOpen={setAddActivityDialogIsOpen}
				/>
			)}
		</div>
	);
};

export default EventSchedule;

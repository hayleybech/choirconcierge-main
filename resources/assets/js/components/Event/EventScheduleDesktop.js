import React, { useState } from 'react';
import Table, { TableCell, THead, TBody, TableHeading } from '../Table';
import ButtonV2 from '../inputs/ButtonV2';
import Icon from '../Icon';
import DeleteDialog from '../DeleteDialog';
import { Link } from '@inertiajs/react';
import EditScheduleItemDialog from '../EditScheduleItemDialog';
import useRoute from '../../hooks/useRoute';

const EventScheduleDesktop = ({ event, onAddActivity }) => {
	const { route } = useRoute();

	const [deleteDialogIsOpen, setDeleteDialogIsOpen] = useState(false);
	const [deletingActivityId, setDeletingActivityId] = useState(0);

	const [editDialogIsOpen, setEditDialogIsOpen] = useState(false);
	const [editingActivity, setEditingActivity] = useState(0);

	return (
		<>
			<Table>
				<THead>
					<tr>
						<TableHeading>Title</TableHeading>
						<TableHeading>Duration</TableHeading>
						{event.can.update_event && <TableHeading>Actions</TableHeading>}
					</tr>
				</THead>
				<TBody>
					{event.activities.map((activity, index) => (
						<tr key={activity.id}>
							<TableCell>
								{activity.song && (
									<Link
										href={route('songs.show', { song: activity.song })}
										className="text-sm font-medium text-purple-800"
									>
										{activity.song.title}
									</Link>
								)}
								<div>{activity.description}</div>
							</TableCell>
							<TableCell>{activity.duration} min</TableCell>
							{event.can.update_event && (
								<TableCell>
									<ButtonV2
										intent="secondary"
										weight="clear"
										size="md"
										href={route('events.activities.move', { event, activity })}
										method="post"
										data={{ direction: 'up' }}
										className={index > 0 ? '' : 'invisible'}
									>
										<Icon icon="arrow-up" />
									</ButtonV2>
									<ButtonV2
										intent="secondary"
										weight="clear"
										size="md"
										href={route('events.activities.move', { event, activity })}
										method="post"
										data={{ direction: 'down' }}
										className={index < event.activities.length - 1 ? '' : 'invisible'}
									>
										<Icon icon="arrow-down" />
									</ButtonV2>

									<ButtonV2
										intent="secondary"
										weight="clear"
										size="md"
										onClick={() => {
											setEditingActivity(activity);
											setEditDialogIsOpen(true);
										}}
									>
										<Icon icon="edit" />
									</ButtonV2>
									<ButtonV2
										intent="danger"
										weight="clear"
										size="md"
										onClick={() => {
											setDeletingActivityId(activity.id);
											setDeleteDialogIsOpen(true);
										}}
									>
										<Icon icon="trash" />
									</ButtonV2>
								</TableCell>
							)}
						</tr>
					))}
					{event.can.update_event && (
						<tr className="bg-white hover:bg-purple-100 cursor-pointer" onClick={onAddActivity}>
							<TableCell colSpan={3}>
								<div className="flex w-full justify-center items-center px-6 text-purple-600 gap-2">
									<Icon icon="plus" />
									Add activity
								</div>
							</TableCell>
						</tr>
					)}
					<tr className="bg-gray-50">
						<TableCell>
							<div className="font-bold text-gray-500">Total</div>
						</TableCell>
						<TableCell>
							<div className="font-bold text-gray-500">
								{event.activities.reduce((prevValue, item) => prevValue + item.duration, 0)} min
							</div>
						</TableCell>
						{event.can.update_event && <TableCell />}
					</tr>
				</TBody>
			</Table>
			<DeleteDialog
				title="Delete Activity"
				url={
					deletingActivityId
						? route('events.activities.destroy', { event, activity: deletingActivityId })
						: '#'
				}
				isOpen={deleteDialogIsOpen}
				setIsOpen={setDeleteDialogIsOpen}
			>
				Are you sure you want to delete this activity? It will be permanently removed from our servers forever.
				This action cannot be undone.
			</DeleteDialog>

			<EditScheduleItemDialog
				isOpen={editDialogIsOpen}
				setIsOpen={setEditDialogIsOpen}
				event={event}
				activity={editingActivity}
			/>
		</>
	);
};

export default EventScheduleDesktop;

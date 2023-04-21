import React, {useState} from 'react';
import Table, {TableCell} from "../Table";
import Button from "../inputs/Button";
import Icon from "../Icon";
import DeleteDialog from "../DeleteDialog";
import {Link} from "@inertiajs/react";
import collect from "collect.js";
import EditScheduleItemDialog from "../EditScheduleItemDialog";
import useRoute from "../../hooks/useRoute";

const EventScheduleDesktop = ({ event }) => {
    const { route } = useRoute();

    const [deleteDialogIsOpen, setDeleteDialogIsOpen] = useState(false);
    const [deletingActivityId, setDeletingActivityId] = useState(0);

    const [editDialogIsOpen, setEditDialogIsOpen] = useState(false);
    const [editingActivity, setEditingActivity] = useState(0);

    const headings = collect({
        title: 'Title',
        duration: 'Duration',
        actions: 'Actions',
    }).filter((item, key) => key !== 'actions' || event.can.update_event);

    return (
        <>
            <Table
                headings={headings}
                body={<>
                    {event.activities.map((activity, index) => (
                        <tr key={activity.id}>
                            <TableCell>
                                {activity.song && (
                                    <Link href={route('songs.show', {song: activity.song})} className="text-sm font-medium text-purple-800">{activity.song.title}</Link>
                                )}
                                <div>
                                    {activity.description}
                                </div>
                            </TableCell>
                            <TableCell>
                                {activity.duration} min
                            </TableCell>
                            {event.can.update_event && (
                            <TableCell>
                                <Button
                                    variant="clear"
                                    size="sm"
                                    href={route('events.activities.move', {event, activity})}
                                    method="post"
                                    data={{direction: 'up'}}
                                    className={index > 0 ? '' : 'invisible'}
                                >
                                    <Icon icon="arrow-up"/>
                                </Button>
                                <Button
                                    variant="clear"
                                    size="sm"
                                    href={route('events.activities.move', {event, activity})}
                                    method="post"
                                    data={{direction: 'down'}}
                                    className={index < event.activities.length - 1 ? '' : 'invisible'}
                                >
                                    <Icon icon="arrow-down"/>
                                </Button>

                                <Button
                                    variant="clear"
                                    size="sm"
                                    onClick={() => {setEditingActivity(activity);
                                        setEditDialogIsOpen(true)}}
                                >
                                    <Icon icon="edit" />
                                </Button>
                                <Button
                                    variant="danger-clear"
                                    size="sm"
                                    onClick={() => {setDeletingActivityId(activity.id);
                                        setDeleteDialogIsOpen(true)}}
                                >
                                    <Icon icon="trash" />
                                </Button>
                            </TableCell>
                            )}
                        </tr>
                    ))}
                    <tr className="bg-gray-50">
                        <TableCell>
                            <div className="font-bold text-gray-500">
                                Total
                            </div>
                        </TableCell>
                        <TableCell>
                            <div className="font-bold text-gray-500">
                                {event.activities.reduce((prevValue, item) => prevValue + item.duration, 0)} min
                            </div>
                        </TableCell>
                        {event.can.update_event && <TableCell />}
                    </tr>
                </>}
            />
            <DeleteDialog
                title="Delete Activity"
                url={route('events.activities.destroy', {event, activity: deletingActivityId})}
                isOpen={deleteDialogIsOpen}
                setIsOpen={setDeleteDialogIsOpen}
            >
                Are you sure you want to delete this activity?
                It will be permanently removed from our servers forever.
                This action cannot be undone.
            </DeleteDialog>

            <EditScheduleItemDialog isOpen={editDialogIsOpen} setIsOpen={setEditDialogIsOpen} event={event} activity={editingActivity} />
        </>
    );
}

export default EventScheduleDesktop;
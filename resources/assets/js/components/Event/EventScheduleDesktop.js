import React, {useState} from 'react';
import Table, {TableCell} from "../Table";
import Button from "../inputs/Button";
import Icon from "../Icon";
import DeleteDialog from "../DeleteDialog";

const EventScheduleDesktop = ({ event }) => {
    const [deleteDialogIsOpen, setDeleteDialogIsOpen] = useState(false);
    const [deletingActivityId, setDeletingActivityId] = useState(0);

    return (
        <>
            <Table
                headings={event.can.update_event ? ['Description', 'Duration', 'Move', 'Delete'] : ['Description', 'Duration']}
                body={<>
                    {event.activities.map((activity, index) => (
                        <tr key={activity.id}>
                            <TableCell>
                                {activity.description}
                            </TableCell>
                            <TableCell>
                                {activity.duration} min
                            </TableCell>
                            {event.can.update_event && (
                            <TableCell>
                                <Button
                                    variant="clear"
                                    size="sm"
                                    href={route('events.activities.move', [event, activity])}
                                    method="post"
                                    data={{direction: 'up'}}
                                    className={index > 0 ? '' : 'invisible'}
                                >
                                    <Icon icon="arrow-up"/>
                                </Button>
                                {index < event.activities.length - 1 &&
                                <Button
                                    variant="clear"
                                    size="sm"
                                    href={route('events.activities.move', [event, activity])}
                                    method="post"
                                    data={{direction: 'down'}}
                                >
                                    <Icon icon="arrow-down"/>
                                </Button>
                                }
                            </TableCell>
                            )}
                            {event.can.update_event && (
                            <TableCell>
                                    <Button
                                        variant="danger-clear"
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
                        {event.can.update_event && (<>
                            <TableCell />
                            <TableCell />
                        </>)}
                    </tr>
                </>}
            />
            <DeleteDialog
                title="Delete Activity"
                url={route('events.activities.destroy', [event, deletingActivityId])}
                isOpen={deleteDialogIsOpen}
                setIsOpen={setDeleteDialogIsOpen}
            >
                Are you sure you want to delete this activity?
                It will be permanently removed from our servers forever.
                This action cannot be undone.
            </DeleteDialog>
        </>
    );
}

export default EventScheduleDesktop;
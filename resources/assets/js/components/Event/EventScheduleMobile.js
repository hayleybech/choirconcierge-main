import React, {useState} from 'react';
import Button from "../inputs/Button";
import Icon from "../Icon";
import DeleteDialog from "../DeleteDialog";

const EventScheduleMobile = ({ event }) => {
    const [deleteDialogIsOpen, setDeleteDialogIsOpen] = useState(false);
    const [deletingActivityId, setDeletingActivityId] = useState(0);

    return (
        <>
            <ul role="list" className="relative z-0 divide-y divide-gray-200">
                {event.activities.map((activity) => (
                    <li key={activity.id} className="bg-white">
                        <div className="px-6 py-5 flex justify-between items-center space-x-3 hover:bg-gray-50">
                            <div>
                                {activity.description}
                            </div>

                            <div className="flex items-center justify-between shrink-0">
                                <div className="text-gray-500 shrink-0">
                                    {activity.duration} min
                                </div>

                                {event.can.update_event && (
                                    <Button
                                        variant="danger-clear"
                                        onClick={() => {setDeletingActivityId(activity.id);
                                        setDeleteDialogIsOpen(true)}}
                                    >
                                        <Icon icon="trash" />
                                    </Button>
                                )}

                            </div>
                        </div>
                    </li>
                ))}
                <li className="bg-gray-50 font-bold">
                    <div className="px-6 py-4 flex justify-between items-center space-x-3">
                        <div className="text-gray-500">
                            Total:
                        </div>

                        <div className="text-gray-500 shrink-0">
                            {event.activities.reduce((prevValue, item) => prevValue + item.duration, 0)} min
                        </div>
                    </div>
                </li>
            </ul>

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

export default EventScheduleMobile;
import React, {useState} from 'react';
import Dialog from "../Dialog";
import RadioGroup from "../inputs/RadioGroup";
import useRoute from "../../hooks/useRoute";

const EditRepeatingEventDialog = ({ isOpen, setIsOpen, event }) => {
    const { route } = useRoute();

    const [selectedMode, setSelectedMode] = useState('single');

    const editModes = [
        {
            id: 'single',
            name: 'Only this event',
            icon: 'calendar-day',
            description: 'All other events in the series will remain the same.',
            disabled: false,
        },
        {
            id: 'following',
            name: 'Following events',
            icon: 'calendar-week',
            description: <>
                <p className="mb-2">
                    This and all the following events will be changed.<br />
                    <strong>Any changes to future events will be lost, including RSVPs.</strong>
                </p>
                {!event.in_future && (
                    <p className="mb-2 text-red-500">
                        This option affects events in the past. To protect attendance data, you cannot bulk edit past events. Please edit individually
                        instead.
                    </p>
                )}
            </>,
            disabled: !event.in_future,
        },
        {
            id: 'all',
            name: 'All events',
            icon: 'calendar-alt',
            description: <>
                <p className="mb-2">
                    All events in the series will be changed.<br/>
                    <strong>Any changes to other events will be lost, including RSVPs and attendance records.</strong>
                </p>
                {(! event.id === event.repeat_parent_id && ! event.parent_in_past) && (
                    <p className="mb-2 text-orange-500">You will be redirected to the first event in the series to make these changes.</p>
                )}
                {event.parent_in_past && (
                    <p className="mb-2 text-red-500">
                        This option affects events in the past. To protect attendance data, you cannot bulk edit past events. Please edit individually
                        instead.
                    </p>
                )}
            </>,
            disabled: event.parent_in_past,
        }
    ];

    return (
        <Dialog
            title="Edit Repeating Event"
            okLabel="Start"
            okUrl={route('events.recurring.edit', {event, mode: selectedMode})}
            okVariant="primary"
            okMethod="get"
            isOpen={isOpen}
            setIsOpen={setIsOpen}
        >
            <p className="mb-2">
                This is a repeating event. How would you like to edit it?
            </p>
            <RadioGroup
                label="Select edit mode"
                options={editModes}
                selected={selectedMode}
                setSelected={setSelectedMode}
                vertical
            />
        </Dialog>
    );
}

export default EditRepeatingEventDialog;
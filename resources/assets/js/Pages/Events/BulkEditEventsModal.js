import React, {useMemo} from 'react';
import Dialog from "../../components/Dialog";
import Label from "../../components/inputs/Label";
import CheckboxGroup from "../../components/inputs/CheckboxGroup";
import {useForm} from "@inertiajs/react";
import useRoute from "../../hooks/useRoute";
import Help from '../../components/inputs/Help';
import RadioGroup from '../../components/inputs/RadioGroup';
import Error from '../../components/inputs/Error';
import EventType from '../../EventType';
import WarningAlert from '../../components/WarningAlert';

const BulkEditEventsModal = ({ isOpen, setIsOpen, selectedEvents, eventTypes, ensembles, userEnsemblesCount, onSuccess }) => {
    const { route } = useRoute();

    const selectedEventIds = useMemo(() => selectedEvents.map(e => e.id), [selectedEvents]);
    const hasRecurringEvents = useMemo(() => selectedEvents.some(e => e.is_repeating || e.repeat_parent_id), [selectedEvents]);

    const { data, setData, post, processing, reset, errors } = useForm({
        event_ids: selectedEventIds,
        event_type_id: eventTypes[0]?.id,
        ensemble_ids: [],
    });

    const handleOk = (e) => {
        e.preventDefault();
        post(route('events.bulk-update'), {
            preserveScroll: true,
            onSuccess: () => {
                setIsOpen(false);
                reset();
                if (onSuccess) onSuccess();
            },
        });
    };

    return (
		<Dialog
			title={`Edit ${selectedEvents.length} Events`}
			isOpen={isOpen}
			setIsOpen={setIsOpen}
			okLabel="Update Events"
			onOk={handleOk}
			processing={processing}
			icon="pencil"
			okVariant="primary"
		>
			<div className="space-y-6 text-left">
				{hasRecurringEvents && (
					<WarningAlert title="Recurring events selected">
						One or more selected events are part of a recurring series. Bulk editing will convert these into single, independent events.
					</WarningAlert>
				)}

				<div className="sm:col-span-6">
					<RadioGroup
						label={<Label label="Event Type" />}
						options={eventTypes.map(type => ({
							id: type.id,
							name: type.title,
							icon: 'tag',
							textColour: (new EventType(type.title)).iconColour,
						}))}
						selected={data.event_type_id}
						setSelected={value => setData('event_type_id', value)}
						vertical
						size="sm"
					/>
					{errors.event_type_id && <Error>{errors.event_type_id}</Error>}
				</div>

				{userEnsemblesCount > 1 && (
					<div className="sm:col-span-6">
						<Label label="Ensembles" forInput="ensembles" />
						<Help>Sub-groups that may view and access these events.</Help>
						<CheckboxGroup
							name="ensembles"
							options={ensembles.map(e => ({ id: e.id, name: e.name }))}
							value={data.ensemble_ids}
							updateFn={val => setData('ensemble_ids', val)}
						/>
					</div>
				)}
			</div>
		</Dialog>
	);
};

export default BulkEditEventsModal;

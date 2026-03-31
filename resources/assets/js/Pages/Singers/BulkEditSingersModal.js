import React from 'react';
import Dialog from "../../components/Dialog";
import Label from "../../components/inputs/Label";
import {useForm} from "@inertiajs/react";
import useRoute from "../../hooks/useRoute";
import RadioGroup from '../../components/inputs/RadioGroup';
import SingerStatus from '../../SingerStatus';
import Error from '../../components/inputs/Error';

const BulkEditSingersModal = ({ isOpen, setIsOpen, selectedSingerIds, statuses, onSuccess }) => {
    const { route } = useRoute();

    const { data, setData, post, processing, reset, errors } = useForm({
        singer_ids: selectedSingerIds,
        singer_status_id: statuses[0]?.id,
    });

    const handleOk = (e) => {
        e.preventDefault();
        post(route('singers.bulk-update'), {
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
			title={`Edit ${selectedSingerIds.length} Singers`}
			isOpen={isOpen}
			setIsOpen={setIsOpen}
			okLabel="Update Singers"
			onOk={handleOk}
			processing={processing}
			icon="pencil"
			okVariant="primary"
		>
			<div className="space-y-6 text-left">
				<div className="sm:col-span-6">
					<RadioGroup
						label={<Label label="Status" />}
						options={statuses.map(status => ({
							id: status.id,
							name: status.name,
							textColour: (new SingerStatus(status.slug)).textColour,
							colour: (new SingerStatus(status.slug)).textColour,
							icon: (new SingerStatus(status.slug)).icon,
						}))}
						selected={data.singer_status_id}
						setSelected={value => setData('singer_status_id', value)}
						vertical
						size="sm"
					/>
					{errors.singer_status_id && <Error>{errors.singer_status_id}</Error>}
				</div>
			</div>
		</Dialog>
	);
};

export default BulkEditSingersModal;

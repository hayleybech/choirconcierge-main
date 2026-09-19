import React from 'react';
import Dialog from "../../components/Dialog";
import Label from "../../components/inputs/Label";
import {useForm} from "@inertiajs/react";
import useRoute from "../../hooks/useRoute";
import Error from '../../components/inputs/Error';
import CheckboxGroup from "../../components/inputs/CheckboxGroup";

const BulkEditRiserStacksModal = ({ isOpen, setIsOpen, selectedStackIds, ensembles, onSuccess }) => {
    const { route } = useRoute();

    const { data, setData, post, processing, reset, errors } = useForm({
        stack_ids: selectedStackIds,
        ensemble_ids: [],
    });

    const handleOk = (e) => {
        e.preventDefault();
        post(route('stacks.bulk-update'), {
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
			title={`Edit ${selectedStackIds.length} Riser Stacks`}
			isOpen={isOpen}
			setIsOpen={setIsOpen}
			okLabel="Update Riser Stacks"
			onOk={handleOk}
			processing={processing}
			icon="pencil"
			okVariant="primary"
		>
			<div className="space-y-6 text-left">
				<div className="sm:col-span-6">
                    <fieldset>
                        <legend className="text-sm font-medium text-gray-700 mb-2">
                            <Label label="Ensembles" />
                        </legend>
                        <CheckboxGroup
                            name="ensemble_ids"
                            options={ensembles.map((ensemble) => ({ id: ensemble.id, name: ensemble.name }))}
                            value={data.ensemble_ids}
                            updateFn={value => setData('ensemble_ids', value)}
                        />
                    </fieldset>
					{errors.ensemble_ids && <Error>{errors.ensemble_ids}</Error>}
				</div>
			</div>
		</Dialog>
	);
};

export default BulkEditRiserStacksModal;

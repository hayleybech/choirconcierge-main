import React from 'react';
import Dialog from "../../components/Dialog";
import Label from "../../components/inputs/Label";
import CheckboxGroup from "../../components/inputs/CheckboxGroup";
import {useForm} from "@inertiajs/react";
import useRoute from "../../hooks/useRoute";
import Help from '../../components/inputs/Help';
import RadioGroup from '../../components/inputs/RadioGroup';
import SongStatus from '../../SongStatus';
import Error from '../../components/inputs/Error';

const BulkEditSongsModal = ({ isOpen, setIsOpen, selectedSongIds, statuses, categories, ensembles, userEnsemblesCount, onSuccess }) => {
    const { route } = useRoute();

    const { data, setData, post, processing, reset, errors } = useForm({
        song_ids: selectedSongIds,
        status_id: statuses[0]?.id,
        category_ids: [],
        ensemble_ids: [],
    });

    // Update song_ids whenever selectedSongIds change
    React.useEffect(() => {
        setData('song_ids', selectedSongIds);
    }, [selectedSongIds]);

    const handleOk = (e) => {
        e.preventDefault();
        post(route('songs.bulk-update'), {
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
			title={`Bulk Edit ${selectedSongIds.length} Songs`}
			isOpen={isOpen}
			setIsOpen={setIsOpen}
			okLabel="Update Songs"
			onOk={handleOk}
			processing={processing}
			icon="pencil"
			okVariant="primary"
		>
			<div className="space-y-6 text-left">

				<div className="sm:col-span-6">
					<RadioGroup
						label={<Label label="Song Status" />}
						options={statuses.map(status => ({
							id: status.id,
							name: SongStatus.statuses[status.slug].title,
							textColour: SongStatus.statuses[status.slug].textColour,
							colour: SongStatus.statuses[status.slug].textColour,
							icon: SongStatus.statuses[status.slug].icon,
						}))}
						selected={data.status_id}
						setSelected={value => setData('status_id', value)}
						vertical
						size="sm"
					/>
					<Help>Songs are hidden from general members when they are "Pending".</Help>
					{errors.status && <Error>{errors.status}</Error>}
				</div>

				<div className="sm:col-span-6">
					<Label label="Category" />
					<CheckboxGroup
						name="categories"
						options={categories.map(c => ({ id: c.id, name: c.title }))}
						value={data.category_ids}
						updateFn={val => setData('category_ids', val)}
					/>
				</div>

				{/* @todo check that userEnsembles is the correct value */}
				{userEnsemblesCount > 1 && (
					<div className="sm:col-span-6">
						<Label label="Ensembles" forInput="ensembles" />
						<Help>Sub-groups that may view and access this song.</Help>
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

export default BulkEditSongsModal;

import React, { useState } from 'react';
import VoicePartTag from '../../../components/VoicePartTag';
import DateTag from '../../../components/DateTag';
import Button from '../../../components/inputs/Button';
import Icon from '../../../components/Icon';
import useRoute from '../../../hooks/useRoute';
import Dialog from '../../../components/Dialog';
import Label from '../../../components/inputs/Label';
import Select from '../../../components/inputs/Select';
import RadioGroup from '../../../components/inputs/RadioGroup';
import { CollapsePanelWithoutPadding } from '../../../components/CollapseGroup';
import { useMediaQuery } from 'react-responsive';

export const EnrolmentDetailsSection = ({ singer, voiceParts, ensembles }) => {
	const [creatingEnrolment, setCreatingEnrolment] = useState(false);
	const [editingEnrolment, setEditingEnrolment] = useState(null);
	const [deletingEnrolment, setDeletingEnrolment] = useState(null);

	const isXl = useMediaQuery({ query: '(min-width: 1280px)' });

	return (
		<>
			<CollapsePanelWithoutPadding>
				<ul className="divide-y divide-gray-200">
					{singer.enrolments.map(enrolment => (
						<li
							key={enrolment.id}
							className="flex gap-2 justify-between items-center py-3 px-4 sm:px-6 lg:px-8"
						>
							<div className="flex items-center gap-2">
								<strong>{enrolment.ensemble.name}</strong>
								{enrolment.voice_part && (
									<VoicePartTag
										title={enrolment.voice_part.title}
										colour={enrolment.voice_part.colour}
									/>
								)}
							</div>
							<div className="flex items-center gap-2">
								<span className="text-sm text-gray-500 italic hidden md:inline">
									<DateTag
										icon="pencil"
										date={enrolment.updated_at}
										label={isXl ? 'Updated' : ''}
										format={isXl ? 'DATE_MED' : 'DATE_SHORT'}
										mr={false}
										className="flex items-center gap-x-1 xl:gap-x-1.5"
									/>
								</span>
								{/* @todo add a more specific ability check */}
								{singer.can['update_singer'] && (
									<Button
										variant="primary"
										size="xs"
										onClick={() => setEditingEnrolment(enrolment.id)}
									>
										<Icon icon="edit" />
										<div className="hidden md:inline">Edit</div>
									</Button>
								)}

								{/* @todo add a more specific ability check */}
								{singer.can['update_singer'] && (
									<Button
										variant="danger-outline"
										size="xs"
										onClick={() => setDeletingEnrolment(enrolment.id)}
									>
										<Icon icon="trash" />
										<div className="hidden md:inline">Delete</div>
									</Button>
								)}
							</div>
						</li>
					))}
					{/* @todo add a more specific ability check */}
					{singer.can['update_singer'] && ensembles.length > 0 && (
						<li className="flex justify-center items-center gap-2 py-3 px-4 sm:px-6 lg:px-8">
							<div className="text-gray-700 text-sm">Add a new enrolment</div>
							<Button variant="primary" size="xs" onClick={() => setCreatingEnrolment(true)}>
								<Icon icon="plus" />
								Create
							</Button>
						</li>
					)}
				</ul>
			</CollapsePanelWithoutPadding>

			{singer.can['update_singer'] && (
				<>
					{ensembles.length > 0 && (
						<CreateEnrolmentDialog
							isOpen={!!creatingEnrolment}
							setIsOpen={setCreatingEnrolment}
							singer={singer}
							voiceParts={voiceParts}
							ensembles={ensembles}
						/>
					)}
					<EditEnrolmentDialog
						key={editingEnrolment}
						isOpen={!!editingEnrolment}
						setIsOpen={setEditingEnrolment}
						singer={singer}
						enrolment={singer.enrolments.find(item => item.id === editingEnrolment)}
						voiceParts={voiceParts}
					/>
					<DeleteEnrolmentDialog
						isOpen={!!deletingEnrolment}
						setIsOpen={setDeletingEnrolment}
						singer={singer}
						enrolment={deletingEnrolment}
						voiceParts={voiceParts}
					/>
				</>
			)}
		</>
	);
};

const CreateEnrolmentDialog = ({ singer, isOpen, setIsOpen, voiceParts, ensembles }) => {
	const [selectedEnsemble, setSelectedEnsemble] = useState(ensembles[0].id);
	const [selectedVoicePart, setSelectedVoicePart] = useState(0);

	const { route } = useRoute();

	return (
		<Dialog
			title="Create Enrolment"
			okLabel="Save"
			okUrl={route('singers.enrolments.store', { singer })}
			okVariant="primary"
			okMethod="post"
			data={{
				voice_part_id: selectedVoicePart,
				ensemble_id: selectedEnsemble,
			}}
			isOpen={isOpen}
			setIsOpen={setIsOpen}
		>
			<p className="mb-2">
				Here you can enrol a singer to an ensemble and assign them a voice part for that ensemble.
			</p>
			<div className="mb-2">
				<Label label="Ensemble" forInput="ensemble_id" />
				<Select
					name="ensemble_id"
					options={ensembles.map(ensemble => ({ key: ensemble.id, label: ensemble.name }))}
					value={selectedEnsemble}
					updateFn={value => setSelectedEnsemble(value)}
				/>
			</div>
			<RadioGroup
				label="Select a new voice part"
				options={voiceParts.map(part => ({
					id: part.id,
					name: part.title,
					colour: `text-${part.colour}-500`,
					icon: 'circle',
				}))}
				selected={selectedVoicePart}
				setSelected={setSelectedVoicePart}
				vertical
			/>
		</Dialog>
	);
};

const EditEnrolmentDialog = ({ singer, enrolment, isOpen, setIsOpen, voiceParts }) => {
	const [selectedVoicePart, setSelectedVoicePart] = useState(enrolment?.voice_part?.id ?? 0);

	const { route } = useRoute();

	return (
		<Dialog
			title="Edit Enrolment"
			okLabel="Save"
			okUrl={enrolment ? route('singers.enrolments.update', { singer, enrolment }) : '#'}
			okVariant="primary"
			okMethod="put"
			data={{ voice_part_id: selectedVoicePart }}
			isOpen={isOpen}
			setIsOpen={setIsOpen}
		>
			<p className="mb-2">
				Here you can update the singer's enrolment to an ensemble. Currently, this is limited to their voice
				part.
			</p>
			<RadioGroup
				label="Select a new voice part"
				options={voiceParts.map(part => ({
					id: part.id,
					name: part.title,
					colour: `text-{part.colour}-500`,
					icon: 'circle',
				}))}
				selected={selectedVoicePart}
				setSelected={setSelectedVoicePart}
				vertical
			/>
		</Dialog>
	);
};

const DeleteEnrolmentDialog = ({ singer, enrolment, isOpen, setIsOpen }) => {
	const { route } = useRoute();

	return (
		<Dialog
			title="Delete Enrolment"
			okLabel="Remove"
			okUrl={enrolment ? route('singers.enrolments.destroy', { singer, enrolment }) : '#'}
			okVariant="danger-solid"
			okMethod="delete"
			isOpen={isOpen}
			setIsOpen={setIsOpen}
		>
			<p className="mb-2">Are you sure you want to remove the singer from this ensemble?</p>
		</Dialog>
	);
};

import React, { useState } from 'react';
import ButtonV2 from '../inputs/ButtonV2';
import Dialog from '../Dialog';
import Icon from '../Icon';
import TextInput from '../inputs/TextInput';

export const rsvpOptions = [
	{ id: 'yes', name: 'Going', icon: 'check', colour: 'text-emerald-500' },
	{ id: 'maybe', name: 'Maybe', icon: 'question', colour: 'text-amber-500' },
	{ id: 'no', name: 'Not Going', icon: 'times', colour: 'text-red-500' },
];

const RsvpResponseDialog = ({ response, details, title, onSave, children }) => {
	const [isOpen, setIsOpen] = useState(false);
	const [selectedResponse, setSelectedResponse] = useState(response);
	const [responseDetails, setResponseDetails] = useState(details || '');
	const [processing, setProcessing] = useState(false);

	const openDialog = () => {
		setSelectedResponse(rsvpOptions.some(option => option.id === response) ? response : null);
		setResponseDetails(details || '');
		setIsOpen(true);
	};

	const handleSave = () => {
		if (!selectedResponse || processing) {
			return;
		}

		setProcessing(true);
		onSave(
			selectedResponse,
			['maybe', 'no'].includes(selectedResponse) ? responseDetails : null,
			{
				onSuccess: () => setIsOpen(false),
				onFinish: () => setProcessing(false),
			}
		);
	};

	const handleDialogClose = open => {
		if (open || !processing) {
			setIsOpen(open);
		}
	};

	const detailsLabel = selectedResponse === 'maybe' ? 'Details' : 'Reason';

	return (
		<>
			{children(openDialog)}
			<Dialog
				title={title}
				okLabel="Save RSVP"
				onOk={handleSave}
				isOpen={isOpen}
				setIsOpen={handleDialogClose}
				processing={processing}
				okDisabled={!selectedResponse}
				icon={null}
			>
				<div className="space-y-4">
					<div role="group" aria-label="RSVP response" className="grid gap-2 sm:grid-cols-3">
						{rsvpOptions.map(option => (
							<ButtonV2
								key={option.id}
								intent={selectedResponse === option.id ? 'selected' : 'secondary'}
								weight="outline"
								disabled={processing}
								onClick={() => {
									setSelectedResponse(option.id);
									if (option.id !== selectedResponse) {
										setResponseDetails('');
									}
								}}
								aria-pressed={selectedResponse === option.id}
								className="w-full"
							>
								<Icon icon={option.icon} className={option.colour} />
								{option.name}
							</ButtonV2>
						))}
					</div>
					{['maybe', 'no'].includes(selectedResponse) && (
						<div>
							<label htmlFor="rsvp-details" className="mb-1 block text-sm font-medium text-gray-700">
								{detailsLabel}
							</label>
							<TextInput
								name="rsvp-details"
								value={responseDetails}
								updateFn={setResponseDetails}
								placeholder={detailsLabel}
								aria-label={selectedResponse === 'maybe' ? 'RSVP details' : 'Reason for not going'}
								maxLength={50}
								disabled={processing}
								autoFocus
							/>
						</div>
					)}
				</div>
			</Dialog>
		</>
	);
};

export default RsvpResponseDialog;
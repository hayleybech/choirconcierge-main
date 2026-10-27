import React from 'react';
import Dialog from '../Dialog';
import ButtonV2 from '../inputs/ButtonV2';
import Icon from '../Icon';
 
const CalendarSyncDialog = ({ calendarSyncUrl, isOpen, setIsOpen }) => {
	const [hasCopiedSyncUrl, setHasCopiedSyncUrl] = React.useState(false);

	const closeDialog = open => {
		setIsOpen(open);

		if (!open) {
			setHasCopiedSyncUrl(false);
		}
	};

	return (
		<Dialog title="Sync to Calendar App" isOpen={isOpen} setIsOpen={closeDialog} icon={null}>
			<p className="mb-2">Copy this URL into your calendar application to subscribe to the events calendar.</p>
			<p className="font-bold mb-3">Note: This URL is unique to you and shows any private events that you have access to. </p>
			<div className="flex gap-2 items-center">
				<code className="block break-all rounded bg-gray-100 p-3 text-left text-xs text-gray-700 grow">
					{calendarSyncUrl}
				</code>
				<ButtonV2
					size="md"
					onClick={async () => {
						await navigator.clipboard.writeText(calendarSyncUrl);
						setHasCopiedSyncUrl(true);
					}}
				>
					<Icon icon={hasCopiedSyncUrl ? 'check' : 'clipboard'} mr />
					{hasCopiedSyncUrl ? 'Copied' : 'Copy'}
				</ButtonV2>
			</div>
		</Dialog>
	);
};

export default CalendarSyncDialog;

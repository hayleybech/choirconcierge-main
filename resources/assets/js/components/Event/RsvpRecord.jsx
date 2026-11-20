import React from 'react';
import { router, usePage } from '@inertiajs/react';
import ButtonV2 from '../inputs/ButtonV2';
import Icon from '../Icon';
import useRoute from '../../hooks/useRoute';
import RsvpResponseDialog, { rsvpOptions } from './RsvpResponseDialog';

const RsvpRecord = ({ rsvp, singerId, event }) => {
	const { route } = useRoute();
	const { props: pageProps } = usePage();
	const currentOption = rsvpOptions.find(option => option.id === rsvp.response);

	const updateResponse = (response, details, callbacks) => {
		router.put(
			route('events.rsvps.updateSinger', {
				event,
				singer: singerId,
				tenant: pageProps.tenant,
			}),
			{ response, details },
			{
				preserveScroll: true,
				...callbacks,
			}
		);
	};

	return (
		<RsvpResponseDialog
			response={rsvp.response}
			details={rsvp.details}
			title="Update RSVP"
			onSave={updateResponse}
		>
			{openDialog => (
				<ButtonV2 intent="secondary" weight="outline" size="sm" onClick={openDialog}>
					<Icon
						icon={currentOption?.icon ?? 'circle'}
						className={currentOption?.colour ?? 'text-gray-500'}
						mr
					/>
					{currentOption?.name ?? rsvp.label}
					<Icon icon="chevron-down" ml className="text-gray-400" />
				</ButtonV2>
			)}
		</RsvpResponseDialog>
	);
};

export default RsvpRecord;
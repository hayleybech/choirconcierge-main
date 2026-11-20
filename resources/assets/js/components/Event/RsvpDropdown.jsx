import React from 'react';
import { router } from '@inertiajs/react';
import ButtonV2 from '../inputs/ButtonV2';
import Icon from '../Icon';
import useRoute from '../../hooks/useRoute';
import RsvpResponseDialog, { rsvpOptions } from './RsvpResponseDialog';

const RsvpDropdown = ({ event, size = 'sm' }) => {
	const { route } = useRoute();
	const saveResponse = (response, details, callbacks) => {
		router.visit(
			event.my_rsvp.id
				? route('events.rsvps.update', { tenant: event.tenant_id, event, rsvp: event.my_rsvp })
				: route('events.rsvps.store', { tenant: event.tenant_id, event }),
			{
				method: event.my_rsvp.id ? 'put' : 'post',
				data: { rsvp_response: response, details },
				only: ['event', 'events'],
				preserveScroll: true,
				...callbacks,
			}
		);
	};

	const currentOption = rsvpOptions.find(option => option.id === event.my_rsvp.response);

	return (
		<div className="flex w-full flex-wrap items-center gap-1.5">
			<RsvpResponseDialog
				response={event.my_rsvp.response}
				details={event.my_rsvp.details}
				title="Your RSVP"
				onSave={(response, details, callbacks) => saveResponse(response, details, callbacks)}
			>
				{openDialog => (
					<ButtonV2
						intent="secondary"
						weight="outline"
						size={size}
						className="relative z-0 flex! w-full"
						onClick={openDialog}
					>
						<Icon
							icon={event.my_rsvp.icon}
							type={event.my_rsvp.icon === 'circle' ? 'regular' : 'solid'}
							className={currentOption?.colour ?? 'text-gray-500'}
						/>
						<span className={currentOption?.colour ?? 'text-gray-500'}>{event.my_rsvp.label}</span>
						<Icon icon="chevron-down" className="text-gray-500" />
					</ButtonV2>
				)}
			</RsvpResponseDialog>
		</div>
	);
};

export default RsvpDropdown;

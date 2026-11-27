import React, { Fragment, useState } from 'react';
import { router, usePage } from '@inertiajs/react';
import { Menu, Transition } from '@headlessui/react';
import ButtonV2 from '../inputs/ButtonV2';
import Icon from '../Icon';
import TextInput from '../inputs/TextInput';
import useRoute from '../../hooks/useRoute';
import RsvpResponseDialog, { rsvpOptions } from './RsvpResponseDialog';
import classNames from '../../classNames';
import menuItemStyles from '../ActionMenu/menuItemStyles';
import Tooltip from '../Tooltip';

const RsvpRecord = ({ rsvp, singerId, event, isMobile = false }) => {
	const [isEditing, setIsEditing] = useState(false);
	const [responseDetails, setResponseDetails] = useState(rsvp.details || '');
	const [processing, setProcessing] = useState(false);
	const { route } = useRoute();
	const { props: pageProps } = usePage();
	const currentOption = rsvpOptions.find(option => option.id === rsvp.response);
	const detailsLabel = rsvp.response === 'maybe' ? 'Details' : 'Reason';
	const responseAllowsDetails = ['maybe', 'no'].includes(rsvp.response);

	const updateResponse = (response, details, callbacks = {}) => {
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

	const updateInlineResponse = (response, details) => {
		setProcessing(true);
		updateResponse(response, details, { onFinish: () => setProcessing(false) });
	};

	const startEditing = () => {
		setResponseDetails(rsvp.details || '');
		setIsEditing(true);
	};

	const cancelEditing = () => {
		setResponseDetails(rsvp.details || '');
		setIsEditing(false);
	};

	const applyDetails = () => {
		if (processing) {
			return;
		}

		setProcessing(true);
		updateResponse(rsvp.response, responseDetails, {
			onSuccess: () => setIsEditing(false),
			onFinish: () => setProcessing(false),
		});
	};

	if (isMobile) {
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
							className={currentOption?.iconColour ?? 'text-gray-500'}
						/>
						{currentOption?.name ?? rsvp.label}
						<Icon icon="chevron-down" className="text-gray-400" />
					</ButtonV2>
				)}
			</RsvpResponseDialog>
		);
	}

	return (
		<div className="shrink-0">
			<div className="flex items-center gap-2">
				<Menu as="div" className="relative inline-block text-left">
					<Menu.Button as={ButtonV2} intent="secondary" weight="outline" size="sm" disabled={processing}>
						<Icon
							icon={currentOption?.icon ?? 'circle'}
							className={currentOption?.iconColour ?? 'text-gray-500'}
							mr
						/>
						{currentOption?.name ?? rsvp.label}
						<Icon icon="chevron-down" ml className="text-gray-400" />
					</Menu.Button>
					<Transition
						as={Fragment}
						enter="transition ease-out duration-100"
						enterFrom="transform opacity-0 scale-95"
						enterTo="transform opacity-100 scale-100"
						leave="transition ease-in duration-75"
						leaveFrom="transform opacity-100 scale-100"
						leaveTo="transform opacity-0 scale-95"
					>
						<Menu.Items className="absolute left-0 z-10 mt-2 w-56 origin-top-left divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none">
							<div className="px-1 py-1">
								{rsvpOptions.map(option => (
									<Menu.Item
										key={option.id}
										as="button"
										type="button"
										disabled={processing}
										onClick={() => {
											setIsEditing(false);
											updateInlineResponse(
												option.id,
												option.id === rsvp.response && ['maybe', 'no'].includes(option.id)
													? rsvp.details
													: null
											);
										}}
										className={({ active }) =>
											classNames(
												menuItemStyles('secondary', active, 'w-full text-left', 'xs'),
												option.id === rsvp.response ? 'bg-gray-50 font-bold' : ''
											)
										}
									>
										<Icon icon={option.icon} className={option.iconColour} mr />
										{option.name}
									</Menu.Item>
								))}
							</div>
						</Menu.Items>
					</Transition>
				</Menu>
				{!isEditing && responseAllowsDetails && !rsvp.details && (
					<ButtonV2 intent="secondary" weight="outline" size="sm" onClick={startEditing}>
						<Icon icon="plus" mr /> Add Reason
					</ButtonV2>
				)}
			</div>

			{responseAllowsDetails && (
				<div className="mt-1 flex items-center gap-1.5">
					{rsvp.details && !isEditing && (
						<Tooltip content={rsvp.details}>
							<span className="block max-w-[200px] truncate text-[11px] italic text-gray-500">
								{detailsLabel}: {rsvp.details}
							</span>
						</Tooltip>
					)}
					{rsvp.details && !isEditing && (
						<ButtonV2 intent="secondary" weight="outline" size="sm" onClick={startEditing}>
							<Icon icon="pencil" mr /> Edit
						</ButtonV2>
					)}
					{isEditing && (
						<>
							<TextInput
								name={`rsvp_details_${singerId}`}
								id={`rsvp_details_${singerId}`}
								value={responseDetails}
								updateFn={setResponseDetails}
								placeholder={detailsLabel}
								aria-label={detailsLabel}
								size="xs"
								maxLength={50}
								autoFocus
								disabled={processing}
								onKeyDown={e => {
									if (e.key === 'Enter') {
										applyDetails();
									}
									if (e.key === 'Escape') {
										cancelEditing();
									}
								}}
							/>
							<div className="flex gap-1">
								<ButtonV2
									size="sm"
									onClick={applyDetails}
									type="button"
									loading={processing}
									aria-label="Apply reason"
								>
									<Icon icon="check" />
								</ButtonV2>
								<ButtonV2
									intent="secondary"
									weight="outline"
									size="sm"
									onClick={cancelEditing}
									disabled={processing}
									aria-label="Cancel reason"
								>
									<Icon icon="times" />
								</ButtonV2>
							</div>
						</>
					)}
				</div>
			)}
		</div>
	);
};

export default RsvpRecord;
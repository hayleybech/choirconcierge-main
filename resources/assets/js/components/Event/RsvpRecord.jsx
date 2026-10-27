import React, { Fragment, useState } from 'react';
import { Menu, Transition } from '@headlessui/react';
import { router, usePage } from '@inertiajs/react';
import ButtonV2 from '../inputs/ButtonV2';
import Icon from '../Icon';
import menuItemStyles from '../ActionMenu/menuItemStyles';
import useRoute from '../../hooks/useRoute';
 
const options = [
	{ id: 'yes', name: 'Going', icon: 'check', colour: 'text-emerald-500' },
	{ id: 'maybe', name: 'Maybe', icon: 'question', colour: 'text-amber-500' },
	{ id: 'no', name: 'Not Going', icon: 'times', colour: 'text-red-500' },
];

const RsvpRecord = ({ rsvp, singerId, event }) => {
	const { route } = useRoute();
	const { props: pageProps } = usePage();
	const [processing, setProcessing] = useState(false);
	const currentOption = options.find(option => option.id === rsvp.response);

	const updateResponse = response => {
		setProcessing(true);
		router.put(
			route('events.rsvps.updateSinger', {
				event,
				singer: singerId,
				tenant: pageProps.tenant,
			}),
			{ response },
			{
				preserveScroll: true,
				onFinish: () => setProcessing(false),
			}
		);
	};

	return (
		<Menu as="div" className="relative inline-block text-left">
			<Menu.Button as={ButtonV2} intent="secondary" weight="outline" size="sm" disabled={processing}>
				<Icon icon={currentOption?.icon ?? 'circle'} className={currentOption?.colour ?? 'text-gray-500'} mr />
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
				<Menu.Items className="absolute left-0 z-20 mt-2 w-48 origin-top-left divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none">
					<div className="px-1 py-1">
						{options.map(option => (
							<Menu.Item key={option.id}>
								{({ active }) => (
									<button
										type="button"
										onClick={() => updateResponse(option.id)}
										className={menuItemStyles('secondary', active, 'text-left', 'xs')}
									>
										<Icon icon={option.icon} className={option.colour} mr />
										{option.name}
										{option.id === rsvp.response && <Icon icon="check" className="ml-2" />}
									</button>
								)}
							</Menu.Item>
						))}
					</div>
				</Menu.Items>
			</Transition>
		</Menu>
	);
};

export default RsvpRecord;
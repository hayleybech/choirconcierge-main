import React, { useState, Fragment } from 'react';
import Button from '../inputs/Button';
import Icon from '../Icon';
import TextInput from '../inputs/TextInput';
import { Menu, Transition } from '@headlessui/react';
import menuItemStyles from '../ActionMenu/menuItemStyles';
import buttonStyles from '../inputs/buttonStyles';
import useRoute from '../../hooks/useRoute';
import { usePage, useForm, router } from '@inertiajs/react';
import classNames from '../../classNames';

const AttendanceRecord = ({ attendance, singerId, event, onToggleEditing }) => {
	const [isEditing, setIsEditing] = useState(false);

	const reasonIsAllowed = attendance.response === 'absent' || attendance.response === 'absent_apology';

	const toggleIsEditing = (value) => {
		setIsEditing(value);
		if (onToggleEditing) {
			onToggleEditing(value);
		}
	};

	const { props: pageProps } = usePage();
	const { route } = useRoute();

	const { data, put, transform, processing, setData } = useForm({
		response: attendance.response,
		absent_reason: attendance.absent_reason || '',
	});

	transform(data => ({
		...data,
		response: data.response === 'absent' && (!!data.absent_reason || attendance.absent_reason) ? 'absent_apology' : data.response,
	}));

	const options = [
		{
			id: 'present',
			name: 'Present',
			icon: 'check',
			colour: 'text-green-600',
		},
		{
			id: 'late',
			name: 'Late',
			icon: 'alarm-exclamation',
			colour: 'text-amber-500',
		},
		{
			id: 'late_deemed_absent',
			name: 'Late (Deemed Absent)',
			icon: 'times',
			colour: 'text-red-500',
		},
		{
			id: 'absent',
			name: 'Absent',
			icon: 'times',
			colour: 'text-red-500',
		},
	];

	const handleSubmit = () => {
		put(
			route('events.attendances.update', {
				event,
				singer: singerId,
				tenant: pageProps.tenant,
			}),
			{
				preserveScroll: true,
				onSuccess: () => {
					toggleIsEditing(false);
				},
			}
		);
	};

	const currentOption = options.find(o => o.id === (data.response === 'absent_apology' ? 'absent' : data.response));

	return (
		<div className="sm:min-w-[200px] 'shrink-0">
			<div className="flex flex-col gap-y-1.5">
				<div className="flex items-center gap-2">
					<Menu as="div" className="relative inline-block text-left">
						<Menu.Button className={buttonStyles('secondary', 'xs')}>
							{currentOption ? (
								<>
									<Icon icon={currentOption.icon} className={currentOption.colour} mr />
									{currentOption.name}
								</>
							) : (
								'Select status'
							)}
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
							<Menu.Items className="absolute left-0 mt-2 w-56 origin-top-left divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-10">
								<div className="px-1 py-1">
									{options.map(option => (
										<Menu.Item
											key={option.id}
											as="button"
											type="button"
											onClick={e => {
												setData('response', option.id);
												router.put(
													route('events.attendances.update', {
														event,
														singer: singerId,
														tenant: pageProps.tenant,
													}),
													{
														response: option.id,
														absent_reason: data.absent_reason,
													},
													{
														preserveScroll: true,
														onSuccess: () => {
															toggleIsEditing(false);
														},
													}
												);
											}}
											className={({ active }) =>
												classNames(
													menuItemStyles('secondary', active, 'w-full text-left', 'xs'),
													option.id === data.response ? 'bg-gray-50 font-bold' : ''
												)
											}
										>
											<Icon icon={option.icon} className={option.colour} mr />
											{option.name}
										</Menu.Item>
									))}
								</div>
							</Menu.Items>
						</Transition>
					</Menu>

					{!isEditing && !attendance.absent_reason && reasonIsAllowed && (
						<Button variant="secondary" size="xs" onClick={e => toggleIsEditing(true)}>
							<Icon icon="plus" mr /> Add Reason
						</Button>
					)}
				</div>

				{reasonIsAllowed && (
					<div className="flex gap-1.5 items-center">
						{attendance.absent_reason && !isEditing && (
							<div
								className="text-[11px] text-gray-500 italic max-w-[200px] truncate"
								title={attendance.absent_reason}
							>
								Reason: {attendance.absent_reason}
							</div>
						)}

						{!isEditing && !!attendance.absent_reason && (
							<Button variant="secondary" size="xs" onClick={e => toggleIsEditing(true)}>
								<Icon icon="pencil" mr /> Edit
							</Button>
						)}

						{isEditing && (
							<>
								<TextInput
									name="absent_reason"
									id={`absent_reason_${singerId}`}
									value={data.absent_reason}
									updateFn={value => setData('absent_reason', value)}
									placeholder="Reason for absence"
									size="xs"
									autoFocus
									onKeyDown={e => {
										if (e.key === 'Enter') {
											handleSubmit();
										}
										if (e.key === 'Escape') {
											toggleIsEditing(false);
										}
									}}
								/>
								<div className="flex gap-1">
									<Button size="xs" onClick={e => handleSubmit()} type="button" loading={processing}>
										<Icon icon="check" />
									</Button>
									<Button
										variant="secondary"
										size="xs"
										onClick={e => toggleIsEditing(false)}
										disabled={processing}
									>
										<Icon icon="times" />
									</Button>
								</div>
							</>
						)}
					</div>
				)}
			</div>
		</div>
	);
};

export default AttendanceRecord;

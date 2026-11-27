import React, { Fragment, useState } from 'react';
import ButtonV2 from '../inputs/ButtonV2';
import Icon from '../Icon';
import TextInput from '../inputs/TextInput';
import { Menu, Transition } from '@headlessui/react';
import menuItemStyles from '../ActionMenu/menuItemStyles';
import useRoute from '../../hooks/useRoute';
import { router, useForm, usePage } from '@inertiajs/react';
import classNames from '../../classNames';
import Dialog from '../Dialog';
import Tooltip from '../Tooltip';

const AttendanceRecord = ({ attendance, singerId, event, onToggleEditing, isMobile = false }) => {
	const [isEditing, setIsEditing] = useState(false);
	const [isMobileDialogOpen, setIsMobileDialogOpen] = useState(false);

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
		response: data.response,
	}));

	const options = [
		{
			id: 'present',
			name: 'Present',
			icon: 'check',
			iconColour: 'text-emerald-500!',
		},
		{
			id: 'late',
			name: 'Late',
			icon: 'alarm-snooze',
			iconColour: 'text-yellow-500!',
		},
		{
			id: 'late_deemed_absent',
			shortName: 'Very Late',
			name: 'Very Late (Deemed Absent)',
			icon: 'alarm-exclamation',
			iconColour: 'text-orange-500!',
		},
		{
			id: 'absent',
			name: 'Absent',
			icon: 'times',
			iconColour: 'text-red-500!',
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
					if (isMobile) {
						setIsMobileDialogOpen(false);
					}
				},
			}
		);
	};

	const openMobileDialog = () => {
		setData('response', attendance.response);
		setData('absent_reason', attendance.absent_reason || '');
		setIsMobileDialogOpen(true);
	};

	const handleMobileDialogClose = open => {
		if (open || !processing) {
			setIsMobileDialogOpen(open);
			if (!open) {
				setData('response', attendance.response);
				setData('absent_reason', attendance.absent_reason || '');
			}
		}
	};

	const currentOption = options.find(o => o.id === data.response);

	return (
		<div className="sm:min-w-[200px] shrink-0">
			<div className="flex flex-col gap-y-1.5">
				<div className="flex items-center gap-1">
					{isMobile && attendance.response === 'absent' && attendance.absent_reason && (
						<Tooltip
							content={attendance.absent_reason}
							placement="bottom-end"
							triggerProps={{
								'aria-label': 'Show attendance reason',
								className:
									'h-8 w-8 rounded-full text-gray-400 hover:text-purple-600 focus:outline-none focus:ring-1 focus:ring-purple-500',
							}}
						>
							<Icon icon="comment-dots" />
						</Tooltip>
					)}
					{isMobile && (
						<ButtonV2
							intent="secondary"
							weight="outline"
							size="sm"
							className="max-w-[150px]"
							onClick={openMobileDialog}
						>
							{currentOption ? (
								<>
									<Icon icon={currentOption.icon} className={currentOption.iconColour} />
									<span className="truncate">{currentOption.shortName ?? currentOption.name}</span>
								</>
							) : (
								'Select status'
							)}
							<Icon icon="chevron-down" className="text-gray-400" />
						</ButtonV2>
					)}
					{!isMobile && <Menu as="div" className="relative inline-block text-left">
						<Menu.Button as={ButtonV2} intent="secondary" weight="outline" size="sm">
							{currentOption ? (
								<>
									<Icon icon={currentOption.icon} className={currentOption.iconColour} mr />
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
							<Menu.Items className="absolute left-0 mt-2 w-56 origin-top-left divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none z-10">
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
											<Icon icon={option.icon} className={option.iconColour} mr />
											{option.name}
										</Menu.Item>
									))}
								</div>
							</Menu.Items>
						</Transition>
					</Menu>}

					{!isMobile && !isEditing && !attendance.absent_reason && attendance.response === 'absent' && (
						<ButtonV2 intent="secondary" weight="outline" size="sm" onClick={e => toggleIsEditing(true)}>
							<Icon icon="plus" mr /> Add Reason
						</ButtonV2>
					)}
				</div>

				{attendance.response === 'absent' && (
					<div className="flex gap-1.5 items-center">
						{attendance.absent_reason && !isEditing && !isMobile && (
							<Tooltip content={attendance.absent_reason}>
								<span className="block text-[11px] text-gray-500 italic max-w-[200px] truncate">
									Reason: {attendance.absent_reason}
								</span>
							</Tooltip>
						)}

						{!isMobile && !isEditing && !!attendance.absent_reason && (
							<ButtonV2 intent="secondary" weight="outline" size="sm" onClick={e => toggleIsEditing(true)}>
								<Icon icon="pencil" mr /> Edit
							</ButtonV2>
						)}

						{isEditing && !isMobile && (
							<>
								<TextInput
									name="absent_reason"
									id={`absent_reason_${singerId}`}
									value={data.absent_reason}
									updateFn={value => setData('absent_reason', value)}
									placeholder="Reason for absence"
									size="xs"
									maxLength={50}
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
									<ButtonV2 size="sm" onClick={e => handleSubmit()} type="button" loading={processing}>
										<Icon icon="check" />
									</ButtonV2>
									<ButtonV2
										intent="secondary"
										weight="outline"
										size="sm"
										onClick={e => toggleIsEditing(false)}
										disabled={processing}
									>
										<Icon icon="times" />
									</ButtonV2>
								</div>
							</>
						)}
					</div>
				)}
			</div>
			{isMobile && (
				<Dialog
					title="Update Attendance"
					okLabel="Save"
					onOk={handleSubmit}
					isOpen={isMobileDialogOpen}
					setIsOpen={handleMobileDialogClose}
					processing={processing}
					okDisabled={!data.response}
					icon={null}
				>
					<div className="space-y-4">
						<div role="group" aria-label="Attendance status" className="relative z-0 grid grid-cols-1">
							{options.map((option, index) => (
								<ButtonV2
									key={option.id}
									intent={data.response === option.id ? 'selected' : 'secondary'}
									weight="outline"
									disabled={processing}
									onClick={() => setData('response', option.id)}
									aria-pressed={data.response === option.id}
									className={classNames(
										'relative w-full min-w-0 rounded-none px-2 text-sm justify-start',
										index > 0 ? '-mt-px' : '',
										index === 0 ? 'rounded-t-md' : '',
										index === options.length - 1 ? 'rounded-b-md' : '',
										data.response === option.id ? 'z-10' : ''
									)}
								>
									<Icon icon={option.icon} className={option.iconColour} mr />
									{option.name}
								</ButtonV2>
							))}
						</div>
						{data.response === 'absent' && (
							<div>
								<label
									htmlFor={`absent_reason_${singerId}`}
									className="mb-1 block text-sm font-medium text-gray-700"
								>
									Reason for absence
								</label>
								<TextInput
									name="absent_reason"
									id={`absent_reason_${singerId}`}
									value={data.absent_reason}
									updateFn={value => setData('absent_reason', value)}
									placeholder="Reason for absence"
									aria-label="Reason for absence"
									maxLength={50}
									disabled={processing}
									autoFocus
								/>
							</div>
						)}
					</div>
				</Dialog>
			)}
		</div>
	);
};

export default AttendanceRecord;

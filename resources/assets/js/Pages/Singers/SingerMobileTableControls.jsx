import React, { Fragment } from 'react';
import { Menu, Transition } from '@headlessui/react';
import ActionMenuItem from '../../components/ActionMenu/ActionMenuItem';
import Icon from '../../components/Icon';
import Button from '../../components/inputs/ButtonV2';
import CheckboxInput from '../../components/inputs/CheckboxInput';
import SingerSortDropdown from './SingerSortDropdown';

const SingerMobileTableControls = ({ bulkEdit, onToggleFilters, sorts, form, tableActions, hasChangedFilters = false }) => (
	<div className="flex items-center gap-2 border-b border-gray-200 bg-white px-4 py-2 sm:px-6 md:px-8">
		{bulkEdit.isActiveMobile && (
			<CheckboxInput
				ref={bulkEdit.refSelectAll}
				checked={bulkEdit.selectedIds.length === bulkEdit.totalItems && bulkEdit.totalItems > 0}
				onChange={bulkEdit.toggleAll}
				aria-label="Select all singers"
				className="shrink-0"
			/>
		)}
		{bulkEdit.action && (
			<Button
				size="sm"
				intent={bulkEdit.action.label === 'Cancel Selection' ? 'selected' : 'secondary'}
				weight="outline"
				className="h-8"
				onClick={bulkEdit.action.onClick}
				aria-label={bulkEdit.action.label}
			>
				<Icon icon={bulkEdit.action.icon} />
				{bulkEdit.action.label}
			</Button>
		)}
		<div className="ml-auto flex items-center gap-2">
			<Button
				size="sm"
				weight="outline"
				intent={hasChangedFilters ? 'selected' : 'secondary'}
				className="h-8"
				onClick={onToggleFilters}
				aria-label="Filter singers"
			>
				<Icon icon="filter" type="regular" />
				Filter
			</Button>
			<SingerSortDropdown sorts={sorts} form={form} autoSubmit />
			<Menu as="div" className="relative">
				<Menu.Button as={Button} size="sm" weight="outline" className="h-8" aria-label="Singer table actions">
					<Icon icon="cog" />
				</Menu.Button>
				<Transition
					as={Fragment}
					enter="transition ease-out duration-100"
					enterFrom="opacity-0 scale-95"
					enterTo="opacity-100 scale-100"
					leave="transition ease-in duration-75"
					leaveFrom="opacity-100 scale-100"
					leaveTo="opacity-0 scale-95"
				>
					<Menu.Items className="absolute right-0 z-50 mt-2 w-52 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black/5 focus:outline-none">
						{tableActions.map(action => (
							<ActionMenuItem
								key={action.label}
								url={action.url}
								onClick={action.onClick}
								download={action.download}
							>
								<Icon icon={action.icon} mr />
								{action.label}
							</ActionMenuItem>
						))}
					</Menu.Items>
				</Transition>
			</Menu>
		</div>
	</div>
);

export default SingerMobileTableControls;
import React from 'react';
import { Link, router } from '@inertiajs/react';
import Icon from './Icon';
import CheckboxInput from './inputs/CheckboxInput';
import useLongPress from '../hooks/useLongPress';
import { plural } from '../util';

export const TableMobileItem = ({ url, children }) => (
	<TableMobileListItem>
		<TableMobileLink url={url}>{children}</TableMobileLink>
	</TableMobileListItem>
);

export const TableMobileListItem = ({ children }) => <li className="flex flex-col relative">{children}</li>;

export const TableMobileLink = ({ url, onClick, active, padding = 'pl-4', children, ...props }) => (
	<Link
		href={url}
		onClick={onClick}
		className={`block flex-grow min-w-0 ${active ? 'bg-purple-50' : 'hover:bg-gray-50'} ${padding}`}
		{...props}
	>
		<div className="flex items-center pr-4 py-4 sm:px-6">
			<div className="flex-1 flex items-center justify-between min-w-0 w-full">{children}</div>
			<div>
				<Icon icon="chevron-right" className="text-gray-400" />
			</div>
		</div>
	</Link>
);

export const TableMobileSelectableLink = ({ url, bulkEdit, value, children }) => {
	const onLongPress = () => {
		if (bulkEdit.isAllowed && !bulkEdit.isActiveMobile) {
			bulkEdit.toggleSelection(value);
		}
	};

	const handleClick = e => {
		if (bulkEdit.isActiveMobile) {

			bulkEdit.toggleSelection(value);
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			return false;
		}

		return true;
	};

	const longPressProps = useLongPress(onLongPress, handleClick);

	return (
		<TableMobileLink
			url={bulkEdit.isActiveMobile ? '' : url}
			active={bulkEdit.selectedIds.includes(value)}
			padding={bulkEdit.isActiveMobile ? 'pl-11 transition-[padding]' : 'pl-4 transition-[padding]'}
			{...longPressProps}
		>
			{children}
		</TableMobileLink>
	);
};

export const TableMobileSelect = ({ bulkEdit, value }) => {
	if (!bulkEdit.isAllowed || !bulkEdit.isActiveMobile) {
		return null;
	}

	return (
		<div className="flex absolute top-0 bottom-0 px-4 items-center z-10">
			<CheckboxInput
				checked={bulkEdit.selectedIds.includes(value)}
				onChange={() => bulkEdit.toggleSelection(value)}
			/>
		</div>
	);
};
export const TableMobileHeader = ({ bulkEdit, children }) => (
	<div className="px-4 py-1 border-b border-gray-200 flex gap-1 items-center justify-between bg-gray-50 relative">
		<div className="flex items-center">
			{bulkEdit.isActiveMobile && (
				<div className="pr-2 absolute left-4 flex">
					<CheckboxInput
						ref={bulkEdit.refSelectAll}
						checked={bulkEdit.selectedIds.length === bulkEdit.totalItems && bulkEdit.totalItems > 0}
						onChange={() => bulkEdit.toggleAll()}
					/>
				</div>
			)}
			<div className={`text-gray-500 text-sm transition-[padding] ${bulkEdit.isActiveMobile? 'pl-7' : 'pl-0'}`}>
				{plural(bulkEdit.noun)}
			</div>
		</div>

		<div>
			{children}
		</div>
	</div>
);

const TableMobile = ({ children, pagination }) => (
	<div>
		<ul className="divide-y divide-gray-200">{children}</ul>
		{pagination}
	</div>
);

export default TableMobile;

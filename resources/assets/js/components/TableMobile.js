import React from 'react';
import { Link, router } from '@inertiajs/react';
import Icon from './Icon';
import CheckboxInput from './inputs/CheckboxInput';
import useLongPress from '../hooks/useLongPress';

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

export const TableMobileSelectableLink = ({ url, onClick, bulkEdit, value, children }) => {
	const onLongPress = () => {
		if (bulkEdit.canUpdate && !bulkEdit.isSelectionModeMobile) {
			bulkEdit.setIsSelectionModeMobile(true);
			bulkEdit.toggleSelection(value);
		}
	};

	const handleClick = e => {
		if (bulkEdit.isSelectionModeMobile) {
			bulkEdit.toggleSelection(value);
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			return false;
		}

		if (onClick) {
			onClick(e);
		} else if (url && (!e || !e.defaultPrevented)) {
			router.visit(url);
		}
	};

	const longPressProps = useLongPress(onLongPress, handleClick);

	return (
		<TableMobileLink
			url={bulkEdit.isSelectionModeMobile ? '' : url}
			active={bulkEdit.selectedIds.includes(value)}
			padding={bulkEdit.isSelectionModeMobile ? 'pl-11' : 'pl-4'}
			{...longPressProps}
		>
			{children}
		</TableMobileLink>
	);
};

export const TableMobileSelect = ({ bulkEdit, value }) => {
	if (!bulkEdit.canUpdate || !bulkEdit.isSelectionModeMobile) {
		return null;
	}

	return (
		<div className="flex absolute top-0 bottom-0 px-4 items-center">
			<CheckboxInput
				checked={bulkEdit.selectedIds.includes(value)}
				onChange={() => bulkEdit.toggleSelection(value)}
			/>
		</div>
	);
};
const TableMobile = ({ children, pagination }) => (
	<div>
		<ul className="divide-y divide-gray-200">{children}</ul>
		{pagination}
	</div>
);

export default TableMobile;

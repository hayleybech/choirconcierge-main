import React from 'react';
import { Link } from '@inertiajs/react';
import Icon from './Icon';
import CheckboxInput from './inputs/CheckboxInput';

export const TableMobileItem = ({ url, children }) => (
	<TableMobileListItem>
		<TableMobileLink url={url}>{children}</TableMobileLink>
	</TableMobileListItem>
);

export const TableMobileListItem = ({ children }) => <li className="flex flex-col relative">{children}</li>;

export const TableMobileLink = ({ url, onClick, children }) => (
	<Link href={url} onClick={onClick} className="block hover:bg-gray-50 flex-grow min-w-0">
		<div className="flex items-center px-4 py-4 sm:px-6">
			<div className="flex-1 flex items-center justify-between min-w-0 w-full">{children}</div>
			<div>
				<Icon icon="chevron-right" className="text-gray-400" />
			</div>
		</div>
	</Link>
);

export const TableMobileSelectableLink = ({ url, onClick, bulkEdit, value, children }) => (
	<TableMobileLink
		href={bulkEdit.isSelectionModeMobile ? null : url}
		onClick={
			bulkEdit.isSelectionModeMobile
				? e => {
						bulkEdit.toggleSelection(value);
						e.preventDefault();
						return false;
				  }
				: onClick
		}
	>
		{children}
	</TableMobileLink>
);

export const TableMobileSelect = ({ bulkEdit, value }) => {
	if (!bulkEdit.isSelectionModeMobile) {
		return null;
	}

	return (
		<div className="pl-4 flex">
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

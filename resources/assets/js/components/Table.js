import React from 'react';
import CheckboxInput from './inputs/CheckboxInput';

export const TableHeading = ({ colSpan, children, className }) => (
	<th
		colSpan={colSpan}
		scope="col"
		className={'px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider ' + className}
	>
		{children}
	</th>
);

export const TableCell = ({ colSpan, children, className }) => (
	<td colSpan={colSpan} className={'px-6 py-4 whitespace-nowrap text-sm text-gray-500 ' + className}>
		{children}
	</td>
);

export const TItemRow = ({ bulkEdit, value, children }) => {
	const props = {};

	if (bulkEdit.isAllowed && bulkEdit.handleRowClick) {
		props.onClick = event => {
			if (event.target.closest('a, button, input')) {
				return;
			}
			bulkEdit.handleRowClick(value, event);
		};
		props.onMouseDown = event => {
			if (event.shiftKey) {
				event.preventDefault();
			}
		};
		props.className = 'cursor-pointer ';
	}

	props.className = (props.className || '') + (bulkEdit.selectedIds.includes(value) ? 'bg-purple-50' : '');

	return <tr {...props}>{children}</tr>;
};

export const THead = ({ children }) => <thead className="bg-gray-50">{children}</thead>;

export const TBody = ({ children }) => <tbody className="bg-white divide-y divide-gray-200">{children}</tbody>;

export const TableSelectAll = ({ bulkEdit, totalItems }) => {
	if (!bulkEdit.isAllowed) {
		return null;
	}

	return (
		<TableHeading className="w-4 pr-0">
			<CheckboxInput
				ref={bulkEdit.refSelectAll}
				checked={bulkEdit.selectedIds.length === totalItems && totalItems > 0}
				onChange={bulkEdit.toggleAll}
			/>
		</TableHeading>
	);
};

export const TableCellSelect = ({ bulkEdit, value }) => {
	if (!bulkEdit.isAllowed) {
		return null;
	}

	return (
		<TableCell className="w-4 pr-0">
			<CheckboxInput
				checked={bulkEdit.selectedIds.includes(value)}
				onChange={() => bulkEdit.toggleSelection(value)}
				onClick={event => event.stopPropagation()}
			/>
		</TableCell>
	);
};

const Table = ({ children, pagination }) => (
	<div className="-my-2 overflow-x-auto">
		<div className="py-2 align-middle inline-block min-w-full">
			<div className="shadow overflow-hidden border-b border-gray-200">
				<table className="min-w-full divide-y divide-gray-200">{children}</table>
				{pagination}
			</div>
		</div>
	</div>
);

export default Table;

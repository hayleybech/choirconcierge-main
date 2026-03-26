import React from 'react';
import CheckboxInput from './inputs/CheckboxInput';
import Button from './inputs/Button';
import Icon from './Icon';

const BulkEditBarMobile = ({ totalItems, bulkEdit }) => {
	if (!bulkEdit.isSelectionModeMobile) {
		return null;
	}

	return (
		<div className="px-4 py-2 border-b border-gray-200 flex gap-1 bg-gray-50">
			<div className="items-center flex mr-3">
				<CheckboxInput
					ref={bulkEdit.refSelectAll}
					checked={bulkEdit.selectedIds.length === totalItems && totalItems > 0}
					onChange={() => bulkEdit.toggleAll()}
				/>
			</div>
			<Button
				size="xs"
				variant={bulkEdit.isSelectionModeMobile ? 'primary' : 'secondary'}
				onClick={
					bulkEdit.isSelectionModeMobile
						? () => {
								bulkEdit.clearSelections();
								bulkEdit.setIsSelectionModeMobile(false);
						  }
						: () => bulkEdit.setIsSelectionModeMobile(true)
				}
				className="gap-1"
			>
				<Icon icon={bulkEdit.isSelectionModeMobile ? 'times' : 'check-square'} />
				{bulkEdit.isSelectionModeMobile ? 'Cancel' : 'Select Multiple'}
			</Button>
			{bulkEdit.selectedIds.length > 0 && (
				<Button
					size="xs"
					variant="secondary"
					onClick={() => bulkEdit.setShowModal(true)}
					disabled={bulkEdit.selectedIds.length === 0}
					className="gap-1"
				>
					<Icon icon="pencil" />
					Edit {bulkEdit.selectedIds.length > 0 ? `(${bulkEdit.selectedIds.length})` : ''}
				</Button>
			)}
		</div>
	);
};

export default BulkEditBarMobile;

import React from 'react';
import Button from './inputs/Button';
import Icon from './Icon';

const BulkEditBar = ({ bulkEdit }) => {
	if (!bulkEdit.isAllowed || bulkEdit.selectedIds.length === 0) {
		return null;
	}

	return (
		<div className="px-2 py-2 border-b border-gray-200 flex gap-0,5 bg-gray-700 text-white fixed z-10 bottom-4 lg:bottom-8 left-1/2 transform -translate-x-1/2 rounded-xl shadow-xl">
			<Button
				size="xs"
				variant="clear-inverse"
				onClick={() => {
					bulkEdit.clearSelections();
					bulkEdit.setIsForcedMobile(false);
				}}
				className="gap-1"
			>
				<Icon icon="times" />
				{bulkEdit.selectedIds.length}&nbsp;{bulkEdit.noun}
				{bulkEdit.selectedIds.length === 1 ? '' : 's'}
			</Button>
			{bulkEdit.selectedIds.length > 0 && (
				<>
					{bulkEdit.canUpdate && (
						<Button
							size="xs"
							variant="clear-inverse"
							onClick={() => bulkEdit.setShowEditModal(true)}
							disabled={bulkEdit.selectedIds.length === 0}
							className="gap-1"
						>
							<Icon icon="pencil" />
							Edit
						</Button>
					)}
					{bulkEdit.canDelete && (
						<Button
							size="xs"
							variant="clear-inverse"
							onClick={() => bulkEdit.setShowDeleteModal(true)}
							disabled={bulkEdit.selectedIds.length === 0}
							className="gap-1"
						>
							<Icon icon="trash" />
							Delete
						</Button>
					)}
				</>
			)}
		</div>
	);
};

export default BulkEditBar;

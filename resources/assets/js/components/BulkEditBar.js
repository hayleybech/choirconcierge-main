import React, { Fragment } from 'react';
import { Transition } from '@headlessui/react';
import Button from './inputs/Button';
import Icon from './Icon';
import { plural } from '../util';

const BulkEditBar = ({ bulkEdit, actions }) => {
	const show = bulkEdit.isAllowed && bulkEdit.selectedIds.length > 0;

	return (
		<Transition
			show={show}
			as={Fragment}
			enter="transition ease-out-back duration-300 transform"
			enterFrom="translate-y-20 opacity-0"
			enterTo="translate-y-0 opacity-100"
			leave="transition ease-in-back duration-200 transform"
			leaveFrom="translate-y-0 opacity-100"
			leaveTo="translate-y-20 opacity-0"
		>
			<div className="px-2 py-2 border-b border-gray-200 flex gap-0.5 bg-gray-700 text-white fixed z-50 bottom-4 lg:bottom-8 left-1/2 transform -translate-x-1/2 rounded-xl shadow-xl">
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
				{bulkEdit.selectedIds.length}&nbsp;{plural(bulkEdit.noun, bulkEdit.selectedIds.length)}
			</Button>
			{bulkEdit.selectedIds.length > 0 && (
				<>
					{actions}
					{bulkEdit.canUpdate && !bulkEdit.hideEdit && (
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
		</Transition>
	);
};

export default BulkEditBar;

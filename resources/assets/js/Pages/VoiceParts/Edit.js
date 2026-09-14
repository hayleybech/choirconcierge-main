import React, { useState } from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import {
	PageHeader,
	PageHeaderActions,
	PageHeaderBreadcrumbs,
	PageHeaderContent,
	PageHeaderTitle,
} from '../../components/PageHeader/PageHeader';
import PageTopBar, { PageActionsMenu, PageTopNavigation } from '../../components/PageTopBar';
import ActionMenuItem from '../../components/ActionMenu/ActionMenuItem';
import Icon from '../../components/Icon';
import Button from '../../components/inputs/Button';
import AppHead from '../../components/AppHead';
import VoicePartForm from './VoicePartForm';
import useRoute from '../../hooks/useRoute';
import DeleteDialog from '../../components/DeleteDialog';

const Edit = ({ voice_part: voicePart, setSidebarOpen }) => {
	const { route } = useRoute();

	const [deleteDialogIsOpen, setDeleteDialogIsOpen] = useState(false);

	const breadcrumbs = [
		{ name: 'Singers', url: route('singers.index') },
		{ name: 'Voice Parts', url: route('voice-parts.index') },
		{ name: voicePart.title, url: '#' },
		{ name: 'Edit Voice Part', url: route('voice-parts.edit', { voice_part: voicePart }) },
	];

	return (
		<>
			<AppHead title={`Edit - ${voicePart.title}`} />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
					<PageTopNavigation breadcrumbs={breadcrumbs}></PageTopNavigation>
					{voicePart.can.delete_voice_part && (
						<PageActionsMenu>
							<ActionMenuItem onClick={() => setDeleteDialogIsOpen(true)} variant="danger-outline">
								<Icon icon="trash" mr />
								Delete
							</ActionMenuItem>
						</PageActionsMenu>
					)}
			</PageTopBar>
			<PageHeader>
				<PageHeaderContent>
					<PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
					<PageHeaderTitle>
						<span className="flex items-center">
							<Icon icon="users-class" type="solid" className="mr-2" />
							Edit Voice Part
						</span>
					</PageHeaderTitle>
				</PageHeaderContent>
				<PageHeaderActions>
					{voicePart.can.delete_voice_part && (
						<Button onClick={() => setDeleteDialogIsOpen(true)} size="sm" variant="danger-outline">
							<Icon icon="trash" mr />
							Delete
						</Button>
					)}
				</PageHeaderActions>
			</PageHeader>

			<VoicePartForm voicePart={voicePart} />

			<DeleteDialog
				title="Delete Voice Part"
				url={route('voice-parts.destroy', { voice_part: voicePart })}
				isOpen={deleteDialogIsOpen}
				setIsOpen={setDeleteDialogIsOpen}
			>
				Are you sure you want to delete this voice part? All of its singers will have no voice part. This action
				cannot be undone.
			</DeleteDialog>
		</>
	);
};

Edit.layout = page => <TenantLayout children={page} />;

export default Edit;

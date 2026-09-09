import React, { useState } from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import { PageHeader2 } from '../../components/PageHeader/PageHeader';
import Breadcrumbs from '../../components/PageHeader/Breadcrumbs';
import { PageHeading } from '../../components/PageHeader/PageHeading';
import PageTopBar, { PageActionsMenu, PageTopBarTitle, PageTopNavigation } from '../../components/PageTopBar';
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

	return (
		<>
			<AppHead title={`Edit - ${voicePart.title}`} />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<div className="flex justify-between grow">
					<PageTopNavigation
						backUrl={route('voice-parts.index')}
						breadcrumbs={[
							{ name: 'Singers', url: route('singers.index') },
							{ name: 'Voice Parts', url: route('voice-parts.index') },
							{ name: voicePart.title, url: '#' },
						]}
					>
						<PageTopBarTitle title="Edit Voice Part" />
					</PageTopNavigation>
					{voicePart.can.delete_voice_part && (
						<PageActionsMenu>
							<ActionMenuItem onClick={() => setDeleteDialogIsOpen(true)} variant="danger-outline">
								<Icon icon="trash" mr />
								Delete
							</ActionMenuItem>
						</PageActionsMenu>
					)}
				</div>
			</PageTopBar>
			<PageHeader2>
				<div className="lg:flex lg:items-center lg:justify-between">
					<div className="flex-1 min-w-0">
						<div className="hidden lg:block">
							<Breadcrumbs
								breadcrumbs={[
									{ name: 'Singers', url: route('singers.index') },
									{ name: 'Voice Parts', url: route('voice-parts.index') },
									{ name: voicePart.title, url: '#' },
									{ name: 'Edit', url: route('voice-parts.edit', { voice_part: voicePart }) },
								]}
								showLastChevron={false}
							/>
						</div>
						<PageHeading>
							<span className="flex items-center">
								<Icon icon="users-class" type="solid" className="mr-2" />
								Edit Voice Part
							</span>
						</PageHeading>
					</div>
					<div className="hidden lg:flex mt-0 lg:ml-4 gap-3">
						{voicePart.can.delete_voice_part && (
							<Button onClick={() => setDeleteDialogIsOpen(true)} size="sm" variant="danger-outline">
								<Icon icon="trash" mr />
								Delete
							</Button>
						)}
					</div>
				</div>
			</PageHeader2>

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

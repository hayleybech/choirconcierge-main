import React from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import {
	PageHeader,
	PageHeaderBreadcrumbs,
	PageHeaderContent,
	PageHeaderTitle,
} from '../../components/PageHeader/PageHeader';
import PageTopBar, { PageTopNavigation } from '../../components/PageTopBar';
import Icon from '../../components/Icon';
import AppHead from '../../components/AppHead';
import FolderForm from './FolderForm';
import useRoute from '../../hooks/useRoute';

const Edit = ({ folder, ensembles, roles, voiceParts, singerStatuses, setSidebarOpen }) => {
	const { route } = useRoute();

	const breadcrumbs = [
		{ name: 'Documents', url: route('folders.index') },
		{ name: folder.title, url: route('folders.edit', { folder }) },
		{ name: 'Edit Folder', url: route('folders.edit', { folder }) },
	];

	return (
		<>
			<AppHead title="Edit Folder" />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation breadcrumbs={breadcrumbs}></PageTopNavigation>
			</PageTopBar>
			<PageHeader>
				<PageHeaderContent>
					<PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
					<PageHeaderTitle>
						<Icon icon="folder-edit" type="solid" className="mr-2" />
						Edit Folder
					</PageHeaderTitle>
				</PageHeaderContent>
			</PageHeader>

			<FolderForm
				folder={folder}
				ensembles={ensembles}
				roles={roles}
				voiceParts={voiceParts}
				singerStatuses={singerStatuses}
			/>
		</>
	);
};

Edit.layout = page => <TenantLayout children={page} />;

export default Edit;

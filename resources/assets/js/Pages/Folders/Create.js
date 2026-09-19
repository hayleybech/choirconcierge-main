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

const Create = ({ ensembles, roles, voiceParts, singerStatuses, setSidebarOpen }) => {
	const { route } = useRoute();

	const breadcrumbs = [
		{ name: 'Documents', url: route('folders.index') },
		{ name: 'Create Folder', url: route('folders.create') },
	];

	return (
		<>
			<AppHead title="Create Folder" />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation breadcrumbs={breadcrumbs}></PageTopNavigation>
			</PageTopBar>
			<PageHeader>
				<PageHeaderContent>
					<PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
					<PageHeaderTitle>
						<Icon icon="folder-plus" type="solid" className="mr-2" />
						Create Folder
					</PageHeaderTitle>
				</PageHeaderContent>
			</PageHeader>

			<FolderForm ensembles={ensembles} roles={roles} voiceParts={voiceParts} singerStatuses={singerStatuses} />
		</>
	);
};

Create.layout = page => <TenantLayout children={page} />;

export default Create;

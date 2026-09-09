import React from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import { PageHeader2 } from '../../components/PageHeader/PageHeader';
import Breadcrumbs from '../../components/PageHeader/Breadcrumbs';
import { PageHeading } from '../../components/PageHeader/PageHeading';
import PageTopBar, { PageTopBarTitle, PageTopNavigation } from '../../components/PageTopBar';
import Icon from '../../components/Icon';
import AppHead from '../../components/AppHead';
import FolderForm from './FolderForm';
import useRoute from '../../hooks/useRoute';

const Edit = ({ folder, ensembles, roles, voiceParts, singerStatuses, setSidebarOpen }) => {
	const { route } = useRoute();

	return (
		<>
			<AppHead title="Edit Folder" />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation
					backUrl={route('folders.index')}
					breadcrumbs={[{ name: 'Documents', url: route('folders.index') }]}
				>
					<PageTopBarTitle title="Edit Folder" />
				</PageTopNavigation>
			</PageTopBar>
			<PageHeader2>
				<div className="lg:flex lg:items-center lg:justify-between">
					<div className="flex-1 min-w-0">
						<div className="hidden lg:block">
							<Breadcrumbs
								breadcrumbs={[
									{ name: 'Documents', url: route('folders.index') },
									{ name: folder.title, url: route('folders.edit', { folder }) },
									{ name: 'Edit', url: route('folders.edit', { folder }) },
								]}
								showLastChevron={false}
							/>
						</div>
						<PageHeading>
							<Icon icon="folder-edit" type="solid" className="mr-2" />
							Edit Folder
						</PageHeading>
					</div>
				</div>
			</PageHeader2>

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

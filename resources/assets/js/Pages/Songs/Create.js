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
import SongForm from './SongForm';
import AppHead from '../../components/AppHead';
import useRoute from '../../hooks/useRoute';

const Create = ({ categories, statuses, pitches, ensembles, setSidebarOpen }) => {
	const { route } = useRoute();

	const breadcrumbs = [
		{ name: 'Songs', url: route('songs.index') },
		{ name: 'Create Song', url: route('songs.create') },
	];

	return (
		<>
			<AppHead title="Create Song" />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation breadcrumbs={breadcrumbs}></PageTopNavigation>
			</PageTopBar>
			<PageHeader>
				<PageHeaderContent>
					<PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
					<PageHeaderTitle>
						<Icon icon="list-music" type="solid" className="mr-2" />
						Create Song
					</PageHeaderTitle>
				</PageHeaderContent>
			</PageHeader>

			<SongForm categories={categories} statuses={statuses} pitches={pitches} ensembles={ensembles} />
		</>
	);
};

Create.layout = page => <TenantLayout children={page} />;

export default Create;

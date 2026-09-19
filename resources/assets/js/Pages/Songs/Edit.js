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

const Edit = ({ categories, statuses, pitches, song, ensembles, setSidebarOpen }) => {
	const { route } = useRoute();

	const breadcrumbs = [
		{ name: 'Songs', url: route('songs.index') },
		{ name: song.title, url: route('songs.show', { song }) },
		{ name: 'Edit Song', url: route('songs.edit', { song }) },
	];

	return (
		<>
			<AppHead title={`Edit - ${song.title}`} />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation breadcrumbs={breadcrumbs}></PageTopNavigation>
			</PageTopBar>
			<PageHeader>
				<PageHeaderContent>
					<PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
					<PageHeaderTitle>
						<Icon icon="list-music" type="solid" className="mr-2" />
						Edit Song
					</PageHeaderTitle>
				</PageHeaderContent>
			</PageHeader>

			<SongForm categories={categories} statuses={statuses} pitches={pitches} song={song} ensembles={ensembles} />
		</>
	);
};

Edit.layout = page => <TenantLayout children={page} />;

export default Edit;

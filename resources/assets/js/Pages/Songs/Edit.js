import React from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import { PageHeader2 } from '../../components/PageHeader/PageHeader';
import Breadcrumbs from '../../components/PageHeader/Breadcrumbs';
import { PageHeading } from '../../components/PageHeader/PageHeading';
import PageTopBar, { PageTopBarTitle, PageTopNavigation } from '../../components/PageTopBar';
import Icon from '../../components/Icon';
import SongForm from './SongForm';
import AppHead from '../../components/AppHead';
import useRoute from '../../hooks/useRoute';

const Edit = ({ categories, statuses, pitches, song, ensembles, setSidebarOpen }) => {
	const { route } = useRoute();

	return (
		<>
			<AppHead title={`Edit - ${song.title}`} />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation
					backUrl={route('songs.show', { song })}
					breadcrumbs={[
						{ name: 'Songs', url: route('songs.index') },
						{ name: song.title, url: route('songs.show', { song }) },
					]}
				>
					<PageTopBarTitle title="Edit Song" />
				</PageTopNavigation>
			</PageTopBar>
			<PageHeader2>
				<div className="lg:flex lg:items-center lg:justify-between">
					<div className="flex-1 min-w-0">
						<div className="hidden lg:block">
							<Breadcrumbs
								breadcrumbs={[
									{ name: 'Songs', url: route('songs.index') },
									{ name: song.title, url: route('songs.show', { song }) },
									{ name: 'Edit', url: route('songs.edit', { song }) },
								]}
								showLastChevron={false}
							/>
						</div>
						<PageHeading>
							<Icon icon="list-music" type="solid" className="mr-2" />
							Edit Song
						</PageHeading>
					</div>
				</div>
			</PageHeader2>

			<SongForm categories={categories} statuses={statuses} pitches={pitches} song={song} ensembles={ensembles} />
		</>
	);
};

Edit.layout = page => <TenantLayout children={page} />;

export default Edit;

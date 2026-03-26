import React from 'react';

import TenantLayout from '../../Layouts/TenantLayout';
import SongTableDesktop from './SongTableDesktop';
import SongTableMobile from './SongTableMobile';
import PageHeader from '../../components/PageHeader/PageHeader';
import AppHead from '../../components/AppHead';
import SongFilters from '../../components/Song/SongFilters';
import IndexContainer from '../../components/IndexContainer';
import useFilterPane from '../../hooks/useFilterPane';
import FilterSortPane from '../../components/FilterSortPane';
import Sorts from '../../components/Sorts';
import useSortFilterForm from '../../hooks/useSortFilterForm';
import EmptyState from '../../components/EmptyState';
import useRoute from '../../hooks/useRoute';
import BulkEditSongsModal from './BulkEditSongsModal';
import useBulkEdit from '../../hooks/useBulkEdit';

const Index = ({
	songs,
	statuses,
	defaultStatuses,
	categories,
	showForProspectsDefault,
	userEnsemblesCount,
	ensembles,

	can,
}) => {
	const [showFilters, setShowFilters, filterAction, hasNonDefaultFilters] = useFilterPane();
	const { route } = useRoute();

	const bulkEdit = useBulkEdit(songs.data, can.update_song);

	const sorts = [
		{ id: 'title', name: 'Title', default: true },
		{ id: 'created_at', name: 'Date Created' },
		{ id: 'status-title', name: 'Status' },
	];

	const filters = [
		{ name: 'title', defaultValue: '' },
		{ name: 'status.id', multiple: true, defaultValue: defaultStatuses },
		{ name: 'categories.id', multiple: true },
		{ name: 'ensembles.id', multiple: true },
		{ name: 'show_for_prospects', multiple: true, multipleBool: true, defaultValue: showForProspectsDefault },
	];

	const sortFilterForm = useSortFilterForm('songs.index', filters, sorts);

	const actions = [
		{ label: 'Add New', icon: 'plus', url: route('songs.create'), variant: 'primary', can: 'create_song' },
		{ label: 'Categories', icon: 'tags', url: route('song-categories.index'), can: 'list_songs' },
		bulkEdit.action,
		filterAction,
	]
		.filter(action => !!action)
		.filter(action => (action.can ? can[action.can] : true));

	return (
		<>
			<AppHead title="Songs" />
			<PageHeader
				title="Songs"
				icon="list-music"
				breadcrumbs={[
					{ name: 'Dashboard', url: route('dash') },
					{ name: 'Songs', url: route('songs.index') },
				]}
				actions={actions}
				optionsVariant={hasNonDefaultFilters ? 'success-solid' : 'secondary'}
			/>

			<IndexContainer
				showFilters={showFilters}
				filterPane={
					<FilterSortPane
						sorts={<Sorts sorts={sorts} form={sortFilterForm} />}
						filters={
							<SongFilters
								statuses={statuses}
								categories={categories}
								ensembles={ensembles}
								userEnsemblesCount={userEnsemblesCount}
								showForProspectsDefault={showForProspectsDefault}
								form={sortFilterForm}
							/>
						}
						closeFn={() => setShowFilters(false)}
					/>
				}
				tableMobile={
					<SongTableMobile songs={songs} userEnsemblesCount={userEnsemblesCount} bulkEdit={bulkEdit} />
				}
				tableDesktop={
					<SongTableDesktop
						songs={songs}
						sortFilterForm={sortFilterForm}
						userEnsemblesCount={userEnsemblesCount}
						bulkEdit={bulkEdit}
					/>
				}
				emptyState={
					songs.data.length === 0 ? (
						<EmptyState
							title="No songs"
							description="You don't have any songs yet, or you need to expand your filters. "
							actionDescription={
								can['create_song']
									? 'To get started, add a song then upload some sheet music or audio files. '
									: null
							}
							icon="list-music"
							href={can['create_song'] ? route('songs.create') : null}
							actionLabel="Add Song"
							actionIcon="plus"
						/>
					) : null
				}
			/>

			<BulkEditSongsModal
				isOpen={bulkEdit.showModal}
				setIsOpen={bulkEdit.setShowModal}
				selectedSongIds={bulkEdit.selectedIds}
				onSuccess={() => bulkEdit.setSelectedIds([])}
				statuses={statuses}
				categories={categories}
				ensembles={ensembles}
				userEnsemblesCount={userEnsemblesCount}
				key={bulkEdit.selectedIds}
			/>
		</>
	);
};

Index.layout = page => <TenantLayout children={page} />;

export default Index;

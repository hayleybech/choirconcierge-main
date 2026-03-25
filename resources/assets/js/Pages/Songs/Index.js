import React, {useState} from 'react'
import TenantLayout from "../../Layouts/TenantLayout";
import SongTableDesktop from "./SongTableDesktop";
import SongTableMobile from "./SongTableMobile";
import PageHeader from "../../components/PageHeader/PageHeader";
import AppHead from "../../components/AppHead";
import {usePage} from "@inertiajs/react";
import SongFilters from "../../components/Song/SongFilters";
import IndexContainer from "../../components/IndexContainer";
import useFilterPane from "../../hooks/useFilterPane";
import FilterSortPane from "../../components/FilterSortPane";
import Sorts from "../../components/Sorts";
import useSortFilterForm from "../../hooks/useSortFilterForm";
import EmptyState from "../../components/EmptyState";
import useRoute from "../../hooks/useRoute";
import BulkEditSongsModal from "./BulkEditSongsModal";
import { useMediaQuery } from 'react-responsive';

const Index = ({ songs, statuses, defaultStatuses, categories, showForProspectsDefault, userEnsemblesCount, ensembles }) => {
    const [showFilters, setShowFilters, filterAction, hasNonDefaultFilters] = useFilterPane();
    const [selectedSongIds, setSelectedSongIds] = useState([]);
    const [showBulkEditModal, setShowBulkEditModal] = useState(false);

	const isDesktop = useMediaQuery({ query: '(min-width: 1024px)' });

    const { can } = usePage().props;
    const { route } = useRoute();

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

    const toggleSongSelection = (songId) => {
        setSelectedSongIds(prev =>
            prev.includes(songId)
                ? prev.filter(id => id !== songId)
                : [...prev, songId]
        );
    };

    const toggleAllSongs = () => {
        if (selectedSongIds.length === songs.data.length) {
            setSelectedSongIds([]);
        } else {
            setSelectedSongIds(songs.data.map(song => song.id));
        }
    };

	const clearSelections = () => {
		setSelectedSongIds([]);
	};


    const actions = [
        { label: 'Add New', icon: 'plus', url: route('songs.create'), variant: 'primary', can: 'create_song' },
		{
			label: `Bulk Edit ${selectedSongIds.length > 0 ? `(${selectedSongIds.length})` : ''}`,
			icon: 'pencil',
			onClick: () => setShowBulkEditModal(true),
			variant: 'secondary',
			disabled: selectedSongIds.length === 0,
			hideOnMobile: true,
		},
        { label: 'Categories', icon: 'tags', url: route('song-categories.index'), can: 'list_songs' },
        filterAction,
    ].filter(action => action.can ? can[action.can] : true)
	.filter(action => !action.hideOnMobile || isDesktop);
	// @todo replace this yucky mobile hack

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
					<SongTableMobile
						songs={songs}
						userEnsemblesCount={userEnsemblesCount}
						selectedSongIds={selectedSongIds}
						toggleSongSelection={toggleSongSelection}
						clearSelections={clearSelections}
						setShowBulkEditModal={setShowBulkEditModal}
						toggleAllSongs={toggleAllSongs}
					/>
				}
				tableDesktop={
					<SongTableDesktop
						songs={songs}
						sortFilterForm={sortFilterForm}
						userEnsemblesCount={userEnsemblesCount}
						selectedSongIds={selectedSongIds}
						toggleSongSelection={toggleSongSelection}
						toggleAllSongs={toggleAllSongs}
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
				isOpen={showBulkEditModal}
				setIsOpen={setShowBulkEditModal}
				selectedSongIds={selectedSongIds}
				statuses={statuses}
				categories={categories}
				ensembles={ensembles}
				userEnsemblesCount={userEnsemblesCount}
				onSuccess={() => setSelectedSongIds([])}
			/>
		</>
	);
}

Index.layout = page => <TenantLayout children={page} />

export default Index;

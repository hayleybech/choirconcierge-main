import React from 'react'
import Layout from "../../Layouts/Layout";
import SongTableDesktop from "./SongTableDesktop";
import SongTableMobile from "./SongTableMobile";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import {usePage} from "@inertiajs/inertia-react";
import SongFilters from "../../components/Song/SongFilters";
import IndexContainer from "../../components/IndexContainer";
import SongSorts from "../../components/Song/SongSorts";
import useFilterPane from "../../hooks/useFilterPane";
import FilterSortPane from "../../components/FilterSortPane";

const Index = ({ songs, statuses, defaultStatuses, categories }) => {
    const [showFilters, setShowFilters] = useFilterPane();

    const { can } = usePage().props;

    return (
        <>
            <AppHead title="Songs" />
            <PageHeader
                title="Songs"
                icon="list-music"
                breadcrumbs={[
                    { name: 'Dashboard', url: route('dash')},
                    { name: 'Songs', url: route('songs.index')},
                ]}
                actions={[
                    { label: 'Add New', icon: 'plus', url: route('songs.create'), variant: 'primary', can: 'create_song' },
                    { label: 'Filter/Sort', icon: 'filter', onClick: () => setShowFilters(! showFilters) },
                ].filter(action => action.can ? can[action.can] : true)}
            />

            <IndexContainer
                showFilters={showFilters}
                filterPane={
                    <FilterSortPane
                        sorts={<SongSorts />}
                        filters={<SongFilters statuses={statuses} defaultStatuses={defaultStatuses} categories={categories} />}
                        closeFn={() => setShowFilters(false)}
                    />
                }
                tableMobile={<SongTableMobile songs={songs} />}
                tableDesktop={<SongTableDesktop songs={songs} />}
            />
        </>
    );
}

Index.layout = page => <Layout children={page} />

export default Index;
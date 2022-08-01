import React from 'react'
import Layout from "../../Layouts/Layout";
import SongTableDesktop from "./SongTableDesktop";
import SongTableMobile from "./SongTableMobile";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import {usePage} from "@inertiajs/inertia-react";
import SongFilters from "../../components/Song/SongFilters";
import IndexContainer from "../../components/IndexContainer";
import useFilterPane from "../../hooks/useFilterPane";
import FilterSortPane from "../../components/FilterSortPane";
import Sorts from "../../components/Sorts";

const Index = ({ songs, statuses, defaultStatuses, categories, showForProspectsDefault }) => {
    const [showFilters, setShowFilters] = useFilterPane();

    const { can } = usePage().props;

    const sorts = [
        { id: 'title', name: 'Title', default: true },
        { id: 'created_at', name: 'Dated Created' },
        { id: 'status-title', name: 'Status' },
    ];

    const filters = [
        { name: 'title', defaultValue: '' },
        { name: 'status.id', multiple: true, defaultValue: defaultStatuses },
        { name: 'categories.id', multiple: true },
        { name: 'show_for_prospects', multiple: true, multipleBool: true, defaultValue: showForProspectsDefault },
    ];

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
                        sorts={<Sorts routeName="songs.index" sorts={sorts} filters={filters} />}
                        filters={<SongFilters
                            statuses={statuses}
                            defaultStatuses={defaultStatuses}
                            categories={categories}
                            showForProspectsDefault={showForProspectsDefault}
                            sorts={sorts}
                            filters={filters}
                        />}
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
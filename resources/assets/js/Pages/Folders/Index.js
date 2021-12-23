import React from 'react'
import Layout from "../../Layouts/Layout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import FolderTableDesktop from "./FolderTableDesktop";
import FolderTableMobile from "./FolderTableMobile";
import {usePage} from "@inertiajs/inertia-react";

const Index = ({ folders }) => {
    const { can } = usePage().props;

    return (
        <>
            <AppHead title="Documents" />
            <PageHeader
                title="Documents"
                icon="folders"
                breadcrumbs={[
                    { name: 'Dashboard', url: route('dash')},
                    { name: 'Documents', url: route('folders.index')},
                ]}
                actions={[
                    { label: 'Add Folder', icon: 'folder-plus', url: route('folders.create'), variant: 'primary', can: 'create_folder' },
                ].filter(action => action.can ? can[action.can] : true)}
            />

            <div className="hidden lg:flex flex-col">
                <FolderTableDesktop folders={folders} />
            </div>

            <div className="bg-white shadow block lg:hidden">
                <FolderTableMobile folders={folders} />
            </div>
        </>
    );
}

Index.layout = page => <Layout children={page} />

export default Index;
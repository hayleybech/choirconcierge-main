import React from 'react'
import Layout from "../../Layouts/Layout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import FolderTableDesktop from "./FolderTableDesktop";
import FolderTableMobile from "./FolderTableMobile";

const Index = ({ folders }) => (
    <>
        <AppHead title="Documents" />
        <PageHeader
            title="Documents"
            icon="fa-folders"
            breadcrumbs={[
                { name: 'Dashboard', url: route('dash')},
                { name: 'Documents', url: route('folders.index')},
            ]}
            actions={[
                { label: 'Add Folder', icon: 'folder-plus', url: route('folders.create'), variant: 'primary'},
                { label: 'Filter', icon: 'filter', url: '#'},
            ]}
        />

        <div className="hidden lg:flex flex-col">
            <FolderTableDesktop folders={folders} />
        </div>

        <div className="bg-white shadow overflow-hidden block lg:hidden">
            <FolderTableMobile folders={folders} />
        </div>
    </>
);

Index.layout = page => <Layout children={page} />

export default Index;
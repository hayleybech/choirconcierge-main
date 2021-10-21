import React from 'react'
import Layout from "../../Layouts/Layout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import RiserStackTableDesktop from "./RiserStackTableDesktop";
import RiserStackTableMobile from "./RiserStackTableMobile";

const Index = ({ stacks }) => (
    <>
        <AppHead title="Riser Stacks" />
        <PageHeader
            title="Riser Stacks"
            icon="people-arrows"
            breadcrumbs={[
                { name: 'Dashboard', url: route('dash')},
                { name: 'Riser Stacks', url: route('stacks.index')},
            ]}
            actions={[
                { label: 'Add New', icon: 'plus', url: route('stacks.create'), variant: 'primary'},
                { label: 'Filter', icon: 'filter', url: '#'},
            ]}
        />

        <div className="hidden lg:flex flex-col">
            <RiserStackTableDesktop stacks={stacks} />
        </div>

        <div className="bg-white shadow overflow-hidden block lg:hidden">
            <RiserStackTableMobile stacks={stacks} />
        </div>
    </>
);

Index.layout = page => <Layout children={page} />

export default Index;
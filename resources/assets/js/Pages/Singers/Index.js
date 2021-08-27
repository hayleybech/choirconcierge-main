import React from 'react'
import Layout from "../../Layouts/Layout";
import SingerPageHeader from "./SingerPageHeader";
import SingerTableDesktop from "./SingerTableDesktop";
import SingerTableMobile from "./SingerTableMobile";

const Index = ({all_singers}) => (
    <>
        <SingerPageHeader
            title="Singers"
            icon="fa-users"
            breadcrumbs={[
                { name: 'Dashboard', url: route('dash')},
                { name: 'Singers', url: route('singers.index')},
            ]}
            actions={[
                { label: 'Add New', icon: 'user-plus', route: 'singers.create'},
                { label: 'Voice Parts', icon: 'users-class', route: 'voice-parts.index'},
                { label: 'Filter', icon: 'filter', route: 'voice-parts.index'},
            ]}
        />

        {/* Desktop Table */}
        <div className="hidden lg:flex flex-col">
            <SingerTableDesktop singers={all_singers} />
        </div>

        {/* Mobile Table */}
        <div className="bg-white shadow overflow-hidden block lg:hidden">
            <SingerTableMobile singers={all_singers} />
        </div>
    </>
);

Index.layout = page => <Layout children={page} title="Singers" />

export default Index;
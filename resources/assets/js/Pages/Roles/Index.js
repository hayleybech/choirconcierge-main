import React from 'react'
import Layout from "../../Layouts/Layout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import RoleTableDesktop from "./RoleTableDesktop";
import RoleTableMobile from "./RoleTableMobile";

const Index = ({ roles }) => (
    <>
        <AppHead title="Roles" />
        <PageHeader
            title="Singer Roles"
            icon="user-tag"
            breadcrumbs={[
                { name: 'Dashboard', url: route('dash')},
                { name: 'Singers', url: route('singers.index')},
                { name: 'Roles', url: route('roles.index')},
            ]}
            actions={[
                { label: 'Add New', icon: 'plus', url: route('roles.create'), variant: 'primary'},
            ]}
        />

        {/* Desktop Table */}
        <div className="hidden lg:flex flex-col">
            <RoleTableDesktop roles={roles} />
        </div>

        {/* Mobile Table */}
        <div className="bg-white shadow block lg:hidden">
            <RoleTableMobile roles={roles} />
        </div>
    </>
);

Index.layout = page => <Layout children={page} />

export default Index;
import React from 'react'
import TenantLayout from "../../Layouts/TenantLayout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import RoleForm from "./RoleForm";

const Create = () => (
    <>
        <AppHead title="Create Role" />
        <PageHeader
            title="Create Role"
            icon="user-tag"
            breadcrumbs={[
                { name: 'Dashboard', url: route('dash')},
                { name: 'Singers', url: route('singers.index')},
                { name: 'Roles', url: route('roles.index')},
                { name: 'Create', url: route('roles.create')},
            ]}
        />

        <RoleForm />
    </>
);

Create.layout = page => <TenantLayout children={page} />

export default Create;
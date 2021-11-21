import React from 'react'
import Layout from "../../Layouts/Layout";
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
                { name: 'Roles', url: route('roles.index')},
                { name: 'Create', url: route('roles.create')},
            ]}
        />

        <RoleForm />
    </>
);

Create.layout = page => <Layout children={page} />

export default Create;
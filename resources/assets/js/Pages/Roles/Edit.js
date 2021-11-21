import React from 'react'
import Layout from "../../Layouts/Layout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import RoleForm from "./RoleForm";

const Edit = ({ role }) => (
    <>
        <AppHead title={`Edit - ${role.name}`} />
        <PageHeader
            title="Edit Singer Role"
            icon="user-tag"
            breadcrumbs={[
                { name: 'Dashboard', url: route('dash')},
                { name: 'Roles', url: route('roles.index')},
                { name: `Edit - ${role.name}`, url: route('roles.edit', role)},
            ]}
        />

        <RoleForm role={role} />
    </>
);

Edit.layout = page => <Layout children={page} />

export default Edit;
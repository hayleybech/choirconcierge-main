import React from 'react'
import Layout from "../../Layouts/Layout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import AccountForm from "./AccountForm";

const Edit = ({ }) => (
    <>
        <AppHead title="Edit Profile" />
        <PageHeader
            title="Edit Profile"
            icon="user-edit"
            breadcrumbs={[
                { name: 'Dashboard', url: route('dash')},
                { name: 'Edit Profile', url: route('accounts.edit')},
            ]}
        />

        <AccountForm />
    </>
);

Edit.layout = page => <Layout children={page} />

export default Edit;
import React from 'react'
import AccountForm from "./AccountForm";
import AppHead from "../../../components/AppHead";
import PageHeader from "../../../components/PageHeader";
import CentralLayout from "../../../Layouts/CentralLayout";

const Edit = ({ }) => (
    <>
        <AppHead title="Edit Profile" />
        <PageHeader
            title="Edit Profile"
            icon="user-edit"
            breadcrumbs={[
                { name: 'Dashboard', url: route('central.dash')},
                { name: 'Edit Profile', url: route('central.accounts.edit')},
            ]}
        />

        <AccountForm />
    </>
);

Edit.layout = page => <CentralLayout children={page} />

export default Edit;
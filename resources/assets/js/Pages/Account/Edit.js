import React from 'react'
import Layout from "../../Layouts/Layout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import AccountForm from "./AccountForm";
import {usePage} from "@inertiajs/inertia-react";

const Edit = ({ }) => {
    const { user: authUser } = usePage().props;

    return (
        <>
            <AppHead title="Edit Profile" />
            <PageHeader
                title="Edit Profile"
                icon="user-edit"
                breadcrumbs={[
                    { name: 'Dashboard', url: route('dash')},
                    { name: 'Singers', url: route('singers.index')},
                    { name: authUser.name, url: route('singers.show', authUser) },
                    { name: 'Edit Profile', url: route('accounts.edit')},
                ]}
            />

            <AccountForm />
        </>
    );
}

Edit.layout = page => <Layout children={page} />

export default Edit;
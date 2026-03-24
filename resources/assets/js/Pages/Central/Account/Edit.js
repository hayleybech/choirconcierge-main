import React from 'react'
import AppHead from "../../../components/AppHead";
import PageHeader from "../../../components/PageHeader/PageHeader";
import CentralLayout from "../../../Layouts/CentralLayout";
import useRoute from "../../../hooks/useRoute";
import AccountForm from "../../Account/AccountForm";

const Edit = ({ }) => {
    const { route } = useRoute();

    return (
        <>
            <AppHead title="Edit Profile" />
            <PageHeader
                title="Edit Profile"
                icon="user-edit"
                breadcrumbs={[
                    { name: 'Dashboard', url: route('central.dash')},
                    { name: 'Edit Profile', url: route('central.account.edit')},
                ]}
            />

            <AccountForm postUrl={route('central.account.update')} cancelUrl={route('central.dash')} />
        </>
    );
}

Edit.layout = page => <CentralLayout children={page} />

export default Edit;
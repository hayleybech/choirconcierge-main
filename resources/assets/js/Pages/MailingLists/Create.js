import React from 'react'
import TenantLayout from "../../Layouts/TenantLayout";
import PageHeader from "../../components/PageHeader/PageHeader";
import AppHead from "../../components/AppHead";
import MailingListForm from "./MailingListForm";
import useRoute from "../../hooks/useRoute";
import TrialAntiSpamNotice from "./TrialAntiSpamNotice";

const Create = ({ roles, voiceParts, singerCategories, ensembles }) => {
    const { route } = useRoute();

    return (
        <>
            <AppHead title="Create Mailing List" />

            <TrialAntiSpamNotice />

            <PageHeader
                title="Create Mailing List"
                icon="mail-bulk"
                breadcrumbs={[
                    { name: 'Dashboard', url: route('dash')},
                    { name: 'Mailing Lists', url: route('groups.index')},
                    { name: 'Create', url: route('groups.create')},
                ]}
            />

            <MailingListForm roles={roles} voiceParts={voiceParts} singerCategories={singerCategories} ensembles={ensembles} />
        </>
    );
}

Create.layout = page => <TenantLayout children={page} />

export default Create;
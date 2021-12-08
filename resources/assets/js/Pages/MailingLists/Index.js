import React from 'react'
import Layout from "../../Layouts/Layout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import MailingListTableDesktop from "./MailingListTableDesktop";
import MailingListTableMobile from "./MailingListTableMobile";
import {usePage} from "@inertiajs/inertia-react";

const Index = ({ lists }) => {
    const { can } = usePage().props;

    return (
        <>
            <AppHead title="Mailing Lists" />
            <PageHeader
                title="Mailing Lists"
                icon="mail-bulk"
                breadcrumbs={[
                    { name: 'Dashboard', url: route('dash')},
                    { name: 'Mailing Lists', url: route('groups.index')},
                ]}
                actions={[
                    { label: 'Add New', icon: 'plus', url: route('groups.create'), variant: 'primary', can: 'create_group' },
                    { label: 'Filter', icon: 'filter', url: '#' },
                ].filter(action => action.can ? can[action.can] : true)}
            />

            <div className="hidden lg:flex flex-col">
                <MailingListTableDesktop tasks={lists} />
            </div>

            <div className="bg-white shadow block lg:hidden">
                <MailingListTableMobile lists={lists} />
            </div>
        </>
    );
}

Index.layout = page => <Layout children={page} />

export default Index;
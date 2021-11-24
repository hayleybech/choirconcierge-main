import React from 'react'
import Layout from "../../Layouts/Layout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import MailingListTableDesktop from "./MailingListTableDesktop";
import MailingListTableMobile from "./MailingListTableMobile";

const Index = ({ lists }) => (
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
                { label: 'Add New', icon: 'plus', url: route('groups.create'), variant: 'primary'},
                { label: 'Filter', icon: 'filter', url: '#'},
            ]}
        />

        <div className="hidden lg:flex flex-col">
            <MailingListTableDesktop tasks={lists} />
        </div>

        <div className="bg-white shadow block lg:hidden">
            <MailingListTableMobile lists={lists} />
        </div>
    </>
);

Index.layout = page => <Layout children={page} />

export default Index;
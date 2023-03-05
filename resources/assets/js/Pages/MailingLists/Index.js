import React from 'react'
import TenantLayout from "../../Layouts/TenantLayout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import MailingListTableDesktop from "./MailingListTableDesktop";
import MailingListTableMobile from "./MailingListTableMobile";
import {usePage} from "@inertiajs/inertia-react";
import RiserStackTableDesktop from "../RiserStacks/RiserStackTableDesktop";
import RiserStackTableMobile from "../RiserStacks/RiserStackTableMobile";
import EmptyState from "../../components/EmptyState";
import IndexContainer from "../../components/IndexContainer";

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
                    { label: 'Send Broadcast', icon: 'inbox-out', url: route('groups.broadcasts.create'), variant: 'secondary', can: 'create_broadcast' },
                ].filter(action => action.can ? can[action.can] : true)}
            />

            <IndexContainer
                tableDesktop={<MailingListTableDesktop tasks={lists} />}
                tableMobile={<MailingListTableMobile lists={lists} />}
                emptyState={lists.length === 0
                    ? <EmptyState
                        title="No mailing lists"
                        description="Mailing lists allow you to assign an email address to a group of users, for chat or announcements. "
                        actionDescription={can['create_group']
                            ? "You don't have any yet. Get started by adding a mailing list."
                            : "You don't have any yet. Ask one of your admins to create one for you."
                        }
                        icon="mail-bulk"
                        href={can['create_group'] ? route('groups.create') : null}
                        actionLabel="Add Mailing List"
                        actionIcon="plus"
                    />
                    : null
                }
            />
        </>
    );
}

Index.layout = page => <TenantLayout children={page} />

export default Index;
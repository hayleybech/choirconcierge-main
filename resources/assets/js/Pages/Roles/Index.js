import React from 'react'
import TenantLayout from "../../Layouts/TenantLayout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import RoleTableDesktop from "./RoleTableDesktop";
import RoleTableMobile from "./RoleTableMobile";
import {usePage} from "@inertiajs/inertia-react";
import useRoute from "../../hooks/useRoute";

const Index = ({ roles }) => {
    const { can } = usePage().props;
    const { route } = useRoute();

    return (
        <>
            <AppHead title="Roles" />
            <PageHeader
                title="Singer Roles"
                icon="user-tag"
                breadcrumbs={[
                    { name: 'Dashboard', url: route('dash')},
                    { name: 'Singers', url: route('singers.index')},
                    { name: 'Roles', url: route('roles.index')},
                ]}
                actions={[
                    { label: 'Add New', icon: 'plus', url: route('roles.create'), variant: 'primary', can: 'create_role' },
                ].filter(action => action.can ? can[action.can] : true)}
            />

            {/* Desktop Table */}
            <div className="hidden lg:flex flex-col">
                <RoleTableDesktop roles={roles} />
            </div>

            {/* Mobile Table */}
            <div className="bg-white shadow block lg:hidden">
                <RoleTableMobile roles={roles} />
            </div>
        </>
    );
}

Index.layout = page => <TenantLayout children={page} />

export default Index;
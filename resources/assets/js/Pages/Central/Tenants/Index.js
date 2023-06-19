import React from 'react'
import PageHeader from "../../../components/PageHeader";
import AppHead from "../../../components/AppHead";
import IndexContainer from "../../../components/IndexContainer";
import useRoute from "../../../hooks/useRoute";
import TenantTableDesktop from "./TenantTableDesktop";
import CentralLayout from "../../../Layouts/CentralLayout";
import TenantTableMobile from "./TenantTableMobile";

const Index = ({ tenants }) => {
    const { route } = useRoute();

    return (
        <>
            <AppHead title="Tenants" />
            <PageHeader
                title="Tenants"
                icon="building"
                breadcrumbs={[
                    { name: 'Dashboard', url: route('central.dash')},
                    { name: 'Tenants', url: route('central.tenants.index')},
                ]}
            />

            <IndexContainer
                tableMobile={<TenantTableMobile tenants={tenants} />}
                tableDesktop={<TenantTableDesktop tenants={tenants} />}
            />
        </>
    );
}

Index.layout = page => <CentralLayout children={page} />

export default Index;

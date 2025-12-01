import React from 'react'
import PageHeader from "../../../components/PageHeader/PageHeader";
import AppHead from "../../../components/AppHead";
import IndexContainer from "../../../components/IndexContainer";
import useRoute from "../../../hooks/useRoute";
import TenantTableDesktop from "./TenantTableDesktop";
import CentralLayout from "../../../Layouts/CentralLayout";
import TenantTableMobile from "./TenantTableMobile";
import useSortFilterForm from "../../../hooks/useSortFilterForm";
import useFilterPane from "../../../hooks/useFilterPane";
import FilterSortPane from "../../../components/FilterSortPane";
import Sorts from "../../../components/Sorts";
import TenantFilters from "./TenantFilters";

const Index = ({ tenants, pagination }) => {
    const { route } = useRoute();

    const [showFilters, setShowFilters, filterAction, hasNonDefaultFilters] = useFilterPane();

    const sorts = [
        { id: 'id', name: 'Organisation Name', default: true },
        { id: 'created_at', name: 'Date Created' },
    ];

    const filters = [
        { name: 'id', defaultValue: '' },
        { name: 'billing_status', defaultValue: '' },
    ]

    const sortFilterForm = useSortFilterForm('central.tenants.index', filters, sorts);

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
                actions={[
                    filterAction,
                ]}
                optionsVariant={hasNonDefaultFilters ? 'success-solid' : 'secondary' }
            />

            <IndexContainer
                showFilters={showFilters}
                filterPane={
                    <FilterSortPane
                        sorts={<Sorts sorts={sorts} form={sortFilterForm} />}
                        filters={<TenantFilters form={sortFilterForm} />}
                        closeFn={() => setShowFilters(false)}
                    />
                }
                tableMobile={<TenantTableMobile tenants={tenants} pagination={pagination} />}
                tableDesktop={<TenantTableDesktop tenants={tenants} sortFilterForm={sortFilterForm} pagination={pagination} />}
            />
        </>
    );
}

Index.layout = page => <CentralLayout children={page} />

export default Index;

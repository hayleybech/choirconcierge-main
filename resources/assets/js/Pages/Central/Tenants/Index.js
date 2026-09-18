import React from 'react'

import {
	PageHeader,
	PageHeaderActions,
	PageHeaderContent,
	PageHeaderTitle,
} from '../../../components/PageHeader/PageHeader';
import PageTopBar, { PageActionsMenu, PageTopNavigation } from "../../../components/PageTopBar";
import ActionMenuItem from "../../../components/ActionMenu/ActionMenuItem";
import Icon from "../../../components/Icon";
import Button from "../../../components/inputs/Button";
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

const Index = ({ tenants, pagination, setSidebarOpen }) => {
    const { route } = useRoute();

    const [showFilters, setShowFilters, filterAction] = useFilterPane();

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
            <PageTopBar setSidebarOpen={setSidebarOpen}>
                <PageTopNavigation title="Tenants">
                    {filterAction && (
                        <PageActionsMenu>
                            <ActionMenuItem {...filterAction}>
                                <Icon icon={filterAction.icon} mr />
                                {filterAction.label}
                            </ActionMenuItem>
                        </PageActionsMenu>
                    )}
                </PageTopNavigation>
            </PageTopBar>
            <PageHeader>
                <PageHeaderContent>
                    <PageHeaderTitle>
                        <Icon icon="building" type="solid" className="mr-2" /> Tenants
                    </PageHeaderTitle>
                </PageHeaderContent>
                <PageHeaderActions>
                    {filterAction && (
                        <Button onClick={filterAction.onClick} size="sm" variant={filterAction.variant}>
                            <Icon icon={filterAction.icon} mr />
                            {filterAction.label}
                        </Button>
                    )}
                </PageHeaderActions>
            </PageHeader>

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

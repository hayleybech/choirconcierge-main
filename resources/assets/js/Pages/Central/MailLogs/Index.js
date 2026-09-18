import React from 'react'

import { PageHeader, PageHeaderContent, PageHeaderTitle } from '../../../components/PageHeader/PageHeader';
import PageTopBar, { PageTopNavigation } from "../../../components/PageTopBar";
import Icon from "../../../components/Icon";
import AppHead from "../../../components/AppHead";
import IndexContainer from "../../../components/IndexContainer";
import useRoute from "../../../hooks/useRoute";
import CentralLayout from "../../../Layouts/CentralLayout";
import MailLogTableMobile from './MailLogTableMobile';
import MailLogTableDesktop from './MailLogTableDesktop';

const Index = ({ logs, setSidebarOpen }) => {
    const { route } = useRoute();

    // const [showFilters, setShowFilters, filterAction, hasNonDefaultFilters] = useFilterPane();

    // const sorts = [
    //     { id: 'id', name: 'Organisation Name', default: true },
    //     { id: 'created_at', name: 'Date Created' },
    // ];
    //
    // const filters = [
    //     { name: 'id', defaultValue: '' },
    // ]
    //
    // const sortFilterForm = useSortFilterForm('central.tenants.index', filters, sorts);

    return (
        <>
            <AppHead title="Mail Logs" />
            <PageTopBar setSidebarOpen={setSidebarOpen}>
                <PageTopNavigation title="Mail Logs" />
            </PageTopBar>
            <PageHeader>
                <PageHeaderContent>
                    <PageHeaderTitle>
                        <Icon icon="history" type="solid" className="mr-2" /> Mail Logs
                    </PageHeaderTitle>
                </PageHeaderContent>
            </PageHeader>

            <IndexContainer
                // showFilters={showFilters}
                // filterPane={
                //     <FilterSortPane
                //         sorts={<Sorts sorts={sorts} form={sortFilterForm} />}
                //         filters={<TenantFilters form={sortFilterForm} />}
                //         closeFn={() => setShowFilters(false)}
                //     />
                // }
                tableMobile={<MailLogTableMobile logs={logs} />}
                tableDesktop={<MailLogTableDesktop logs={logs} />}
            />
        </>
    );
}

Index.layout = page => <CentralLayout children={page} />

export default Index;

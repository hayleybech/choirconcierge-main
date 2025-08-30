import React from 'react'
import PageHeader from "../../../components/PageHeader";
import AppHead from "../../../components/AppHead";
import IndexContainer from "../../../components/IndexContainer";
import useRoute from "../../../hooks/useRoute";
import CentralLayout from "../../../Layouts/CentralLayout";
import MailLogTableMobile from './MailLogTableMobile';
import MailLogTableDesktop from './MailLogTableDesktop';

const Index = ({ logs }) => {
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
            <AppHead title="Tenants" />
            <PageHeader
                title="Mail Logs"
                icon="history"
                breadcrumbs={[
                    { name: 'Dashboard', url: route('central.dash')},
                    { name: 'Mail Logs', url: route('central.mail-logs.index')},
                ]}
                // actions={[
                //     filterAction,
                // ]}
                // optionsVariant={hasNonDefaultFilters ? 'success-solid' : 'secondary' }
            />

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

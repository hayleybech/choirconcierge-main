import React from 'react'
import PageHeader from "../../../components/PageHeader/PageHeader";
import AppHead from "../../../components/AppHead";
import IndexContainer from "../../../components/IndexContainer";
import useRoute from "../../../hooks/useRoute";
import MailLogTableMobile from './MailLogTableMobile';
import MailLogTableDesktop from './MailLogTableDesktop';
import TenantLayout from '../../../Layouts/TenantLayout';
import EmptyState from '../../../components/EmptyState';
import TrialAntiSpamNotice from "../TrialAntiSpamNotice";

const Index = ({ logs, can }) => {
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

			<TrialAntiSpamNotice />

			<PageHeader
				title="Communications"
				icon="mail-bulk"
				breadcrumbs={[
					{ name: 'Dashboard', url: route('dash') },
					{ name: 'Communications', url: route('communications.index') },
				]}
				actions={[
					{
						label: 'Send Broadcast',
						icon: 'inbox-out',
						url: route('communications.create'),
						variant: 'primary',
						can: 'create_broadcast',
					},
					{
						label: 'Mailing lists',
						icon: 'at',
						url: route('groups.index'),
						variant: 'secondary',
						can: 'list_groups',
					},
				//     filterAction,
				].filter(action => (action?.can ? can[action.can] : !!action))}
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
				emptyState={
					logs.data.length === 0 && (
						<EmptyState
							title="No mail logs"
							description={
								<>
									This area tracks all emails sent through our mailing list system. <br />
									Either there are no emails yet, or you're not a member of any groups that have
									received emails.
								</>
							}
							icon="history"
						/>
					)
				}
			/>
		</>
	);
}

Index.layout = page => <TenantLayout children={page} />

export default Index;

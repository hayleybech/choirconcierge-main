import React from 'react';
import { PageHeader, PageHeaderActions, PageHeaderTitle } from '../../../components/PageHeader/PageHeader';
import PageTopBar, { PageActionsMenu, PageTopNavigation } from '../../../components/PageTopBar';
import ActionMenuItem from '../../../components/ActionMenu/ActionMenuItem';
import Button from '../../../components/inputs/Button';
import Icon from '../../../components/Icon';
import AppHead from '../../../components/AppHead';
import IndexContainer from '../../../components/IndexContainer';
import useRoute from '../../../hooks/useRoute';
import MailLogTableMobile from './MailLogTableMobile';
import MailLogTableDesktop from './MailLogTableDesktop';
import TenantLayout from '../../../Layouts/TenantLayout';
import EmptyState from '../../../components/EmptyState';
import TrialAntiSpamNotice from '../TrialAntiSpamNotice';
import useSortFilterForm from '../../../hooks/useSortFilterForm';
import useFilterPane from '../../../hooks/useFilterPane';
import FilterSortPane from '../../../components/FilterSortPane';
import Sorts from '../../../components/Sorts';
import MailLogFilters from './MailLogFilters';

const Index = ({ logs, mailingLists, can, setSidebarOpen }) => {
	const { route } = useRoute();
	const showAdminDetails = can.create_broadcast || can.update_group;
	const [showFilters, setShowFilters, filterAction, hasNonDefaultFilters] = useFilterPane();
	const sorts = [
		{ id: 'subject', name: 'Subject' },
		{ id: 'to', name: 'To' },
		{ id: 'from', name: 'From' },
		{ id: 'size', name: 'Size' },
		...(showAdminDetails ? [{ id: 'opens', name: 'Opens' }] : []),
		{ id: 'created_at', name: 'Date Created', default: true, defaultDirection: 'desc' },
	];
	const filters = [
		{ name: 'subject', defaultValue: '' },
		{ name: 'to', defaultValue: '' },
		{ name: 'from', defaultValue: '' },
	];
	const sortFilterForm = useSortFilterForm('communications.index', filters, sorts);

	const actions = [
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
		filterAction,
	].filter(action => (action?.can ? can[action.can] : !!action));

	return (
		<>
			<AppHead title="Communications" />

			{showAdminDetails && <TrialAntiSpamNotice />}

			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation title="Communications">
					<PageActionsMenu>
						{actions.map((action, key) => (
							<ActionMenuItem
								key={key}
								url={action.url}
								onClick={action.onClick}
								variant={action.variant}
							>
								<Icon icon={action.icon} mr />
								{action.label}
							</ActionMenuItem>
						))}
					</PageActionsMenu>
				</PageTopNavigation>
			</PageTopBar>
			<PageHeader>
				<PageHeaderTitle>
					<Icon icon="mail-bulk" type="solid" className="mr-2" /> Communications
				</PageHeaderTitle>
				<PageHeaderActions>
					{actions.map(action => (
						<Button
							key={action.label}
							href={action.url}
							onClick={action.onClick}
							size="sm"
							variant={action.variant}
						>
							<Icon icon={action.icon} mr />
							{action.label}
						</Button>
					))}
				</PageHeaderActions>
			</PageHeader>

			<IndexContainer
				showFilters={showFilters}
				filterPane={
					<FilterSortPane
						sorts={<Sorts sorts={sorts} form={sortFilterForm} />}
						filters={<MailLogFilters form={sortFilterForm} mailingLists={mailingLists} />}
						closeFn={() => setShowFilters(false)}
					/>
				}
				tableMobile={
					<MailLogTableMobile
						logs={logs}
						showAdminDetails={showAdminDetails}
						hasNonDefaultFilters={hasNonDefaultFilters}
						setShowFilters={setShowFilters}
					/>
				}
				tableDesktop={
					<MailLogTableDesktop
						logs={logs}
						showAdminDetails={showAdminDetails}
						sortFilterForm={sortFilterForm}
					/>
				}
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
};

Index.layout = page => <TenantLayout children={page} />;

export default Index;

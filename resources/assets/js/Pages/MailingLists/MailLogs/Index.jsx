import React from 'react';
import {
	PageHeader,
	PageHeaderActions,
	PageHeaderContent,
	PageHeaderLink,
	PageHeaderLinks,
	PageHeaderTitle,
	PageHeaderTitleRow,
} from '../../../components/PageHeader/PageHeader';
import PageTopBar, { PageActionsMenu, PageTopNavigation } from '../../../components/PageTopBar';
import ActionMenuItem from '../../../components/ActionMenu/ActionMenuItem';
import Button from '../../../components/inputs/ButtonV2';
import Icon from '../../../components/Icon';
import AppHead from '../../../components/AppHead';
import IndexContainer from '../../../components/IndexContainer';
import IndexTableControls from '../../../components/IndexTableControls';
import useRoute from '../../../hooks/useRoute';
import MailLogTableMobile from './MailLogTableMobile';
import MailLogTableDesktop from './MailLogTableDesktop';
import TenantLayout from '../../../Layouts/TenantLayout';
import EmptyState from '../../../components/EmptyState';
import TrialAntiSpamNotice from '../TrialAntiSpamNotice';
import useSortFilterForm from '../../../hooks/useSortFilterForm';
import useFilterPane from '../../../hooks/useFilterPane';
import FilterSortPane from '../../../components/FilterSortPane';
import MailLogFilters from './MailLogFilters';
 
const Index = ({ logs, mailingLists, mailTypes, defaultMailTypes, can, inboxEnabled, setSidebarOpen }) => {
	const { route } = useRoute();
	const showAdminDetails = can.create_broadcast || can.update_group;
	const [showFilters, setShowFilters] = useFilterPane();
	const sorts = [
		{ id: 'subject', name: 'Subject' },
		{ id: 'to', name: 'To' },
		{ id: 'from', name: 'From' },
		{ id: 'size', name: 'Size' },
		...(showAdminDetails ? [{ id: 'opens', name: 'Opens' }] : []),
		{ id: 'created_at', name: 'Date Created', default: true, defaultDirection: 'desc' },
	];
	const filters = [
		{ name: 'subject', label: 'Subject', type: 'text', defaultValue: '' },
		{
			name: 'to',
			label: 'To',
			multiple: true,
			defaultValue: '',
			options: mailingLists.map(({ title, email, type_icon }) => ({
				id: email,
				name: title,
				email,
				type_icon,
			})),
			renderOption: (option, compact) => (
				<span className="inline-flex min-w-0 items-center gap-1.5">
					<Icon icon={option.type_icon} size={compact ? 'text-xs' : 'text-sm'} />
					<span className="truncate">{option.name}</span>
					{!compact && <span className="text-xs text-gray-500">{option.email}</span>}
				</span>
			),
		},
		{ name: 'from', label: 'From', type: 'text', defaultValue: '' },
		{
			name: 'type',
			label: 'Mail Type',
			multiple: true,
			defaultValue: defaultMailTypes,
			options: mailTypes,
		},
		...(inboxEnabled
			? [
					{
						name: 'read',
						label: 'Read Status',
						defaultValue: '',
						options: [
							{ id: 'read', name: 'Read', icon: 'envelope-open-text' },
							{ id: 'unread', name: 'Unread', icon: 'envelope' },
						],
						renderOption: option => (
							<span className="inline-flex items-center gap-1.5">
								<Icon icon={option.icon} />
								{option.name}
							</span>
						),
					},
				]
			: []),
	];
	const sortFilterForm = useSortFilterForm('communications.index', filters, sorts);

	const actions = [
		{
			label: inboxEnabled ? 'Compose' : 'Send Broadcast',
			icon: 'inbox-out',
			url: route('communications.create'),
			intent: 'primary',
			weight: 'solid',
			can: 'create_broadcast',
		},
		{
			label: 'Mailing lists',
			icon: 'at',
			url: route('groups.index'),
			intent: 'secondary',
			weight: 'solid',
			can: 'list_groups',
		},
	].filter(action => (action?.can ? can[action.can] : !!action));
	const mailingListsAction = actions.find(action => action.label === 'Mailing lists');
	const pageHeaderActions = actions.filter(action => action !== mailingListsAction);

	return (
		<>
			<AppHead title={inboxEnabled ? 'Inbox' : 'Communications'} />

			{showAdminDetails && <TrialAntiSpamNotice />}

			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation title={inboxEnabled ? 'Inbox' : 'Communications'}>
					<PageActionsMenu>
						{actions.map((action, key) => (
							<ActionMenuItem
								key={key}
								url={action.url}
								onClick={action.onClick}
								intent={action.intent}
							>
								<Icon icon={action.icon} mr />
								{action.label}
							</ActionMenuItem>
						))}
					</PageActionsMenu>
				</PageTopNavigation>
			</PageTopBar>
			<PageHeader hideOnMobile>
				<PageHeaderContent>
					<PageHeaderTitleRow>
						<PageHeaderTitle>
							<Icon icon="mail-bulk" type="solid" className="mr-2" />{' '}
							{inboxEnabled ? 'Inbox' : 'Communications'}
						</PageHeaderTitle>
						<PageHeaderLinks>
							{mailingListsAction && (
								<PageHeaderLink href={mailingListsAction.url} icon={mailingListsAction.icon}>
									{mailingListsAction.label}
								</PageHeaderLink>
							)}
						</PageHeaderLinks>
					</PageHeaderTitleRow>
				</PageHeaderContent>
				<PageHeaderActions>
					{pageHeaderActions.map(action => (
						<Button
							key={action.label}
							href={action.url}
							onClick={action.onClick}
							size="md"
							intent={action.intent}
							weight={action.weight}
							className={action.className}
						>
							<Icon icon={action.icon} mr />
							{action.label}
						</Button>
					))}
				</PageHeaderActions>
			</PageHeader>

			<IndexContainer
				showFilters={showFilters}
				tableControls={
					<IndexTableControls
						filters={filters}
						sorts={sorts}
						form={sortFilterForm}
						filterLabel={inboxEnabled ? 'inbox' : 'communications'}
						onToggleFilters={() => setShowFilters(previousValue => !previousValue)}
					/>
				}
				filterPane={
					<FilterSortPane
						filters={
							<MailLogFilters
								form={sortFilterForm}
								mailingLists={mailingLists}
								mailTypes={mailTypes}
								inboxEnabled={inboxEnabled}
							/>
						}
						closeFn={() => setShowFilters(false)}
					/>
				}
				tableMobile={
					<MailLogTableMobile
						logs={logs}
						showAdminDetails={showAdminDetails}
						inboxEnabled={inboxEnabled}
					/>
				}
				tableDesktop={
					<MailLogTableDesktop
						logs={logs}
						showAdminDetails={showAdminDetails}
						inboxEnabled={inboxEnabled}
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

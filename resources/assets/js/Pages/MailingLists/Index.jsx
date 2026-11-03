import React from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import {
	PageHeader,
	PageHeaderActions,
	PageHeaderBreadcrumbs,
	PageHeaderContent,
	PageHeaderTitle,
} from '../../components/PageHeader/PageHeader';
import PageTopBar, { PageActionsMenu, PageTopNavigation } from '../../components/PageTopBar';
import ActionMenuItem from '../../components/ActionMenu/ActionMenuItem';
import Button from '../../components/inputs/ButtonV2';
import Icon from '../../components/Icon';
import AppHead from '../../components/AppHead';
import MailingListTableDesktop from './MailingListTableDesktop';
import MailingListTableMobile from './MailingListTableMobile';
import EmptyState from '../../components/EmptyState';
import IndexContainer from '../../components/IndexContainer';
import IndexTableControls from '../../components/IndexTableControls';
import useRoute from '../../hooks/useRoute';
import TrialAntiSpamNotice from './TrialAntiSpamNotice';
import useBulkEdit from '../../hooks/useBulkEdit';
import Dialog from '../../components/Dialog';
import BulkEditBar from '../../components/BulkEditBar';
import useFilterPane from '../../hooks/useFilterPane';
import useSortFilterForm from '../../hooks/useSortFilterForm';
import FilterSortPane from '../../components/FilterSortPane';
import MailingListFilters from './MailingListFilters';
 
const Index = ({ lists, can, setSidebarOpen }) => {
	const { route } = useRoute();
	const [showFilters, setShowFilters] = useFilterPane();

	const sorts = [
		{ id: 'title', name: 'Title', default: true },
		{ id: 'list_type', name: 'Type' },
		{ id: 'address', name: 'Address' },
		{ id: 'created_at', name: 'Created' },
	];
	const filters = [
		{ name: 'search', label: 'Search', type: 'text', defaultValue: '' },
		{
			name: 'list_type',
			label: 'Type',
			multiple: true,
			options: [
				{ id: 'chat', name: 'Chat' },
				{ id: 'public', name: 'Public' },
				{ id: 'distribution', name: 'Mailout' },
			],
		},
	];
	const sortFilterForm = useSortFilterForm('groups.index', filters, sorts);

	const bulkEdit = useBulkEdit(lists.data, false, can.delete_group, 'List');
	const actions = [
		{
			label: 'Add New',
			icon: 'plus',
			href: route('groups.create'),
			intent: 'primary',
			weight: 'solid',
			can: 'create_group',
		},
		{
			label: 'Compose',
			icon: 'inbox-out',
			href: route('communications.create'),
			intent: 'secondary',
			can: 'create_broadcast',
		},
		bulkEdit.action,
	].filter(action => (action?.can ? can[action.can] : !!action));
	const pageMenuActions = actions.filter(action => action !== bulkEdit.action);
	const pageHeaderActions = actions.filter(action => action !== bulkEdit.action);

	const breadcrumbs = [
		{ name: 'Communications', url: route('communications.index') },
		{ name: 'Mailing Lists', url: route('groups.index') },
	];

	return (
		<>
			<AppHead title="Mailing Lists" />

			<TrialAntiSpamNotice />

			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation breadcrumbs={breadcrumbs}>
					<PageActionsMenu>
						{pageMenuActions.map(action => (
							<ActionMenuItem
								key={action.label}
								url={action.href}
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
					<PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
					<PageHeaderTitle>
						<Icon icon="mail-bulk" type="solid" className="mr-2" />
						Mailing Lists
					</PageHeaderTitle>
				</PageHeaderContent>

				<PageHeaderActions>
					{pageHeaderActions.map(action => (
						<Button
							key={action.label}
							{...action}
							size="md"
							weight={action.weight ?? 'clear'}
						>
							<Icon icon={action.icon} mr />
							{action.label}
						</Button>
					))}
				</PageHeaderActions>
			</PageHeader>

			<Dialog
				title={`Delete ${bulkEdit.selectedIds.length} Mailing Lists?`}
				isOpen={bulkEdit.showDeleteModal}
				setIsOpen={bulkEdit.setShowDeleteModal}
				okLabel="Delete"
				okColour="danger"
				okMethod="post"
				data={{ group_ids: bulkEdit.selectedIds }}
				okUrl={route('groups.bulk-destroy')}
				onOk={() => {
					bulkEdit.setSelectedIds([]);
					bulkEdit.setShowDeleteModal(false);
					bulkEdit.setIsForcedMobile(false);
				}}
			>
				Are you sure you want to delete the selected mailing lists? This action cannot be undone.
			</Dialog>

			<BulkEditBar bulkEdit={bulkEdit} />

			<IndexContainer
				showFilters={showFilters}
				tableControls={
					<IndexTableControls
						filters={filters}
						sorts={sorts}
						form={sortFilterForm}
						bulkEdit={bulkEdit}
						filterLabel="mailing lists"
						onToggleFilters={() => setShowFilters(previousValue => !previousValue)}
					/>
				}
				filterPane={
					<FilterSortPane
						filters={<MailingListFilters form={sortFilterForm} />}
						closeFn={() => setShowFilters(false)}
					/>
				}
				tableDesktop={<MailingListTableDesktop lists={lists} bulkEdit={bulkEdit} sortFilterForm={sortFilterForm} />}
				tableMobile={
					<MailingListTableMobile
						lists={lists}
						bulkEdit={bulkEdit}
					/>
				}
				emptyState={
					lists.data.length === 0 ? (
						<EmptyState
							title="No mailing lists"
							description="Mailing lists allow you to assign an email address to a group of users, for chat or announcements. "
							actionDescription={
								can['create_group']
									? "You don't have any yet. Get started by adding a mailing list."
									: "You don't have any yet. Ask one of your admins to create one for you."
							}
							icon="mail-bulk"
							href={can['create_group'] ? route('groups.create') : null}
							actionLabel="Add Mailing List"
							actionIcon="plus"
						/>
					) : null
				}
			/>
		</>
	);
};

Index.layout = page => <TenantLayout children={page} />;

export default Index;

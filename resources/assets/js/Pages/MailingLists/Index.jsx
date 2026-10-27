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
import useRoute from '../../hooks/useRoute';
import TrialAntiSpamNotice from './TrialAntiSpamNotice';
import useBulkEdit from '../../hooks/useBulkEdit';
import Dialog from '../../components/Dialog';
import BulkEditBar from '../../components/BulkEditBar';
import useFilterPane from '../../hooks/useFilterPane';
import useSortFilterForm from '../../hooks/useSortFilterForm';
import FilterSortPane from '../../components/FilterSortPane';
import Sorts from '../../components/Sorts';
import MailingListFilters from './MailingListFilters';
 
const Index = ({ lists, can, setSidebarOpen }) => {
	const { route } = useRoute();
	const [showFilters, setShowFilters, filterAction, hasNonDefaultFilters] = useFilterPane();

	const sorts = [
		{ id: 'title', name: 'Title', default: true },
		{ id: 'list_type', name: 'Type' },
		{ id: 'address', name: 'Address' },
		{ id: 'created_at', name: 'Created' },
	];
	const filters = [
		{ name: 'search', defaultValue: '' },
		{ name: 'list_type', multiple: true },
	];
	const sortFilterForm = useSortFilterForm('groups.index', filters, sorts);

	const bulkEdit = useBulkEdit(lists.data, false, can.delete_group, 'List');
	const actions = [
		{
			label: 'Add New',
			icon: 'plus',
			url: route('groups.create'),
			intent: 'primary',
			weight: 'solid',
			can: 'create_group',
		},
		{
			label: 'Compose',
			icon: 'inbox-out',
			url: route('communications.create'),
			intent: 'secondary',
			weight: 'solid',
			can: 'create_broadcast',
		},
		bulkEdit.action,
		filterAction,
	].filter(action => (action?.can ? can[action.can] : !!action));

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
						{actions.map(action => (
							<ActionMenuItem
								key={action.label}
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
			<PageHeader>
				<PageHeaderContent>
					<PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
					<PageHeaderTitle>
						<Icon icon="mail-bulk" type="solid" className="mr-2" />
						Mailing Lists
					</PageHeaderTitle>
				</PageHeaderContent>

				<PageHeaderActions>
					{actions.map(action => (
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
				filterPane={
					<FilterSortPane
						sorts={<Sorts sorts={sorts} form={sortFilterForm} />}
						filters={<MailingListFilters form={sortFilterForm} />}
						closeFn={() => setShowFilters(false)}
					/>
				}
				tableDesktop={<MailingListTableDesktop lists={lists} bulkEdit={bulkEdit} sortFilterForm={sortFilterForm} />}
				tableMobile={
					<MailingListTableMobile
						lists={lists}
						bulkEdit={bulkEdit}
						hasNonDefaultFilters={hasNonDefaultFilters}
						setShowFilters={setShowFilters}
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

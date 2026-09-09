import React from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import { PageHeader2 } from '../../components/PageHeader/PageHeader';
import { PageHeading } from '../../components/PageHeader/PageHeading';
import PageTopBar, { PageActionsMenu, PageTopBarTitle } from '../../components/PageTopBar';
import ActionMenuItem from '../../components/ActionMenu/ActionMenuItem';
import Icon from '../../components/Icon';
import Button from '../../components/inputs/Button';
import AppHead from '../../components/AppHead';
import RiserStackTableDesktop from './RiserStackTableDesktop';
import RiserStackTableMobile from './RiserStackTableMobile';
import { usePage } from '@inertiajs/react';
import IndexContainer from '../../components/IndexContainer';
import EmptyState from '../../components/EmptyState';
import useRoute from '../../hooks/useRoute';
import useFilterPane from '../../hooks/useFilterPane';
import useSortFilterForm from '../../hooks/useSortFilterForm';
import FilterSortPane from '../../components/FilterSortPane';
import RiserStackFilters from '../../components/RiserStack/RiserStackFilters';
import useBulkEdit from '../../hooks/useBulkEdit';
import Dialog from '../../components/Dialog';
import BulkEditRiserStacksModal from './BulkEditRiserStacksModal';
import BulkEditBar from '../../components/BulkEditBar';

const Index = ({ stacks, ensembles, userEnsemblesCount, setSidebarOpen }) => {
	const [showFilters, setShowFilters, filterAction, hasNonDefaultFilters] = useFilterPane();
	const { can } = usePage().props;
	const { route } = useRoute();

	const bulkEdit = useBulkEdit(stacks.data, can.update_stack && ensembles.length > 1, can.delete_stack, 'Stack');

	const filters = [{ name: 'ensembles.id', multiple: true }];

	const sortFilterForm = useSortFilterForm('stacks.index', filters, []);
	const actions = [
		{ label: 'Add New', icon: 'plus', url: route('stacks.create'), variant: 'primary', can: 'create_stack' },
		bulkEdit.action,
		filterAction,
	].filter(action => (action?.can ? can[action.can] : !!action));

	return (
		<>
			<AppHead title="Riser Stacks" />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<div className="flex justify-between grow">
					<PageTopBarTitle title="Riser Stacks" />
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
				</div>
			</PageTopBar>
			<PageHeader2>
				<div className="lg:flex lg:items-center lg:justify-between">
					<div className="flex-1 min-w-0">
						<PageHeading>
							<Icon icon="people-arrows" type="solid" className="mr-2" /> Riser Stacks
						</PageHeading>
					</div>
					<div className="hidden lg:flex mt-0 lg:ml-4 gap-3">
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
					</div>
				</div>
			</PageHeader2>

			<Dialog
				title={`Delete ${bulkEdit.selectedIds.length} Riser Stacks?`}
				isOpen={bulkEdit.showDeleteModal}
				setIsOpen={bulkEdit.setShowDeleteModal}
				okLabel="Delete"
				okVariant="danger-solid"
				okMethod="post"
				data={{ stack_ids: bulkEdit.selectedIds }}
				okUrl={route('stacks.bulk-destroy')}
				onOk={() => {
					bulkEdit.setSelectedIds([]);
					bulkEdit.setShowDeleteModal(false);
					bulkEdit.setIsForcedMobile(false);
				}}
			>
				Are you sure you want to delete the selected riser stacks? This action cannot be undone.
			</Dialog>

			<BulkEditBar bulkEdit={bulkEdit} />

			<IndexContainer
				showFilters={showFilters}
				filterPane={
					<FilterSortPane
						filters={
							<RiserStackFilters
								ensembles={ensembles}
								userEnsemblesCount={userEnsemblesCount}
								form={sortFilterForm}
							/>
						}
						closeFn={() => setShowFilters(false)}
					/>
				}
				tableDesktop={
					<RiserStackTableDesktop
						stacks={stacks}
						userEnsemblesCount={userEnsemblesCount}
						bulkEdit={bulkEdit}
					/>
				}
				tableMobile={
					<RiserStackTableMobile
						stacks={stacks}
						userEnsemblesCount={userEnsemblesCount}
						bulkEdit={bulkEdit}
						hasNonDefaultFilters={hasNonDefaultFilters}
						setShowFilters={setShowFilters}
					/>
				}
				emptyState={
					stacks.data.length === 0 ? (
						<EmptyState
							title="No riser stacks"
							description="Riser stacks allow you to track where your singers should be physically positioned. "
							actionDescription={
								can['create_stack']
									? "You haven't made any yet. Press the button and get started!"
									: "Your team haven't made any yet."
							}
							icon="people-arrows"
							href={can['create_stack'] ? route('stacks.create') : null}
							actionLabel="Add Riser Stack"
							actionIcon="plus"
						/>
					) : null
				}
			/>

			<BulkEditRiserStacksModal
				isOpen={bulkEdit.showEditModal}
				setIsOpen={bulkEdit.setShowEditModal}
				selectedStackIds={bulkEdit.selectedIds}
				key={bulkEdit.selectedIds}
				onSuccess={() => bulkEdit.setSelectedIds([])}
				ensembles={ensembles}
			/>
		</>
	);
};

Index.layout = page => <TenantLayout children={page} />;

export default Index;

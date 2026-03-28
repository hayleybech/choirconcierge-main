import React from 'react'
import TenantLayout from "../../Layouts/TenantLayout";
import PageHeader from "../../components/PageHeader/PageHeader";
import AppHead from "../../components/AppHead";
import RiserStackTableDesktop from "./RiserStackTableDesktop";
import RiserStackTableMobile from "./RiserStackTableMobile";
import {usePage} from "@inertiajs/react";
import IndexContainer from "../../components/IndexContainer";
import EmptyState from "../../components/EmptyState";
import useRoute from "../../hooks/useRoute";
import useFilterPane from "../../hooks/useFilterPane";
import useSortFilterForm from "../../hooks/useSortFilterForm";
import FilterSortPane from "../../components/FilterSortPane";
import RiserStackFilters from "../../components/RiserStack/RiserStackFilters";
import useBulkEdit from "../../hooks/useBulkEdit";
import Dialog from "../../components/Dialog";
import BulkEditRiserStacksModal from "./BulkEditRiserStacksModal";
import BulkEditBar from '../../components/BulkEditBar';

const Index = ({ stacks, ensembles, userEnsemblesCount }) => {
    const [showFilters, setShowFilters, filterAction, hasNonDefaultFilters] = useFilterPane();
    const { can } = usePage().props;
    const { route } = useRoute();

    const bulkEdit = useBulkEdit(stacks.data, can.update_stack && ensembles.length > 1, can.delete_stack, 'Stack');

    const filters = [
        { name: 'ensembles.id', multiple: true },
    ];

    const sortFilterForm = useSortFilterForm('stacks.index', filters, []);

    return (
		<>
			<AppHead title="Riser Stacks" />
			<PageHeader
				title="Riser Stacks"
				icon="people-arrows"
				breadcrumbs={[
					{ name: 'Dashboard', url: route('dash') },
					{ name: 'Riser Stacks', url: route('stacks.index') },
				]}
				actions={[
					{
						label: 'Add New',
						icon: 'plus',
						url: route('stacks.create'),
						variant: 'primary',
						can: 'create_stack',
					},
					bulkEdit.action,
					filterAction,
				].filter(action => (action?.can ? can[action.can] : !!action))}
				optionsVariant={hasNonDefaultFilters ? 'success-solid' : 'secondary'}
			/>

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
}

Index.layout = page => <TenantLayout children={page} />

export default Index;
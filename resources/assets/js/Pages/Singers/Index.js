import React, {useState} from 'react'
import TenantLayout from "../../Layouts/TenantLayout";
import PageHeader from "../../components/PageHeader/PageHeader";
import SingerTableDesktop from "./SingerTableDesktop";
import SingerTableMobile from "./SingerTableMobile";
import AppHead from "../../components/AppHead";
import {usePage} from "@inertiajs/react";
import IndexContainer from "../../components/IndexContainer";
import SingerFilters from "../../components/SingerFilters";
import useFilterPane from "../../hooks/useFilterPane";
import FilterSortPane from "../../components/FilterSortPane";
import Sorts from "../../components/Sorts";
import useSortFilterForm from "../../hooks/useSortFilterForm";
import EmptyState from "../../components/EmptyState";
import ImportSingersDialog from "../../components/ImportSingersDialog";
import useRoute from "../../hooks/useRoute";
import BulkEditSingersModal from './BulkEditSingersModal';
import useBulkEdit from '../../hooks/useBulkEdit';
import Dialog from '../../components/Dialog';
import BulkEditBar from '../../components/BulkEditBarMobile';

const Index = ({ allSingers, statuses, defaultStatus, voiceParts, roles, ensembles, pagination }) => {
    const [showFilters, setShowFilters, filterAction, hasNonDefaultFilters] = useFilterPane();
    const [showImportDialog, setShowImportDialog] = useState(false);
    const { can } = usePage().props;
    const { route } = useRoute();

    const bulkEdit = useBulkEdit(allSingers, can.update_singer, can.delete_singer, 'Singer');

    const sorts = [
        { id: 'full-name', name: 'First Name', default: true },
        { id: 'last-name-first', name: 'Last Name' },
        { id: 'status-title', name: 'Status' },
        // { id: 'part-title', name: 'Voice Part' },
        { id: 'paid_until', name: 'Paid Until' },
    ];

    const filters = [
        { name: 'user.name', defaultValue: '' },
        { name: 'category.id', multiple: true, defaultValue: [defaultStatus] },
        { name: 'enrolments.voice_part_id', multiple: true },
        { name: 'roles.id', multiple: true },
        { name: 'fee_status', defaultValue: '' },
        { name: 'enrolments.ensemble_id', multiple: true },
    ];

    const sortFilterForm = useSortFilterForm('singers.index', filters, sorts);

    return (
		<>
			<AppHead title="Singers" />
			<PageHeader
				title="Singers"
				icon="users"
				breadcrumbs={[
					{ name: 'Dashboard', url: route('dash') },
					{ name: 'Singers', url: route('singers.index') },
				]}
				actions={[
					{
						label: 'Add New',
						icon: 'user-plus',
						url: route('singers.create'),
						variant: 'primary',
						can: 'create_singer',
					},
					{
						label: 'Voice Parts',
						icon: 'users-class',
						url: route('voice-parts.index'),
						can: 'list_voice_parts',
					},
					{ label: 'User Roles', icon: 'user-tag', url: route('roles.index'), can: 'list_roles' },
					{
						label: 'Import Singers',
						icon: 'file-import',
						onClick: () => setShowImportDialog(true),
						can: 'import_singers',
					},
					{
						label: 'Export Singers',
						icon: 'file-export',
						url: route('singers.export'),
						download: true,
						can: 'export_singers',
					},
					bulkEdit.action,
					bulkEdit.deleteAction,
					filterAction,
				].filter(action => (action?.can ? can[action.can] : !!action))}
				optionsVariant={hasNonDefaultFilters ? 'success-solid' : 'secondary'}
			/>

			<Dialog
				title={`Delete ${bulkEdit.selectedIds.length} Singers?`}
				isOpen={bulkEdit.showDeleteModal}
				setIsOpen={bulkEdit.setShowDeleteModal}
				okLabel="Delete"
				okVariant="danger-solid"
				okMethod="post"
				data={{ singer_ids: bulkEdit.selectedIds }}
				okUrl={route('singers.bulk-destroy')}
				onOk={() => {
					bulkEdit.setSelectedIds([]);
					bulkEdit.setShowDeleteModal(false);
					bulkEdit.setIsForcedMobile(false);
				}}
			>
				Are you sure you want to delete the selected singers? This will also delete their user accounts if they
				are not members of any other choir. This action cannot be undone.
			</Dialog>

			<ImportSingersDialog isOpen={showImportDialog} setIsOpen={setShowImportDialog} />

			<BulkEditBar bulkEdit={bulkEdit} />

			<IndexContainer
				showFilters={showFilters}
				filterPane={
					<FilterSortPane
						sorts={<Sorts sorts={sorts} form={sortFilterForm} />}
						filters={
							<SingerFilters
								statuses={statuses}
								voiceParts={voiceParts}
								roles={roles}
								form={sortFilterForm}
								ensembles={ensembles}
							/>
						}
						closeFn={() => setShowFilters(false)}
					/>
				}
				tableMobile={
					<SingerTableMobile
						singers={allSingers}
						pagination={pagination}
						ensembles={ensembles}
						bulkEdit={bulkEdit}
						hasNonDefaultFilters={hasNonDefaultFilters}
						setShowFilters={setShowFilters}
					/>
				}
				tableDesktop={
					<SingerTableDesktop
						singers={allSingers}
						sortFilterForm={sortFilterForm}
						pagination={pagination}
						ensembles={ensembles}
						bulkEdit={bulkEdit}
					/>
				}
				emptyState={
					allSingers.length === 0 ? (
						<EmptyState
							title="No singers"
							actionDescription={
								can['create_singer']
									? 'Get started by adding a singer or try expanding your filtering options.'
									: 'Your team might not have added any singers yet or you may need to expand your filtering options.'
							}
							icon="users"
							href={can['create_singer'] ? route('singers.create') : null}
							actionLabel="Add Singer"
							actionIcon="user-plus"
						/>
					) : null
				}
			/>

			<BulkEditSingersModal
				isOpen={bulkEdit.showEditModal}
				setIsOpen={bulkEdit.setShowEditModal}
				selectedSingerIds={bulkEdit.selectedIds}
				key={bulkEdit.selectedIds}
				onSuccess={() => bulkEdit.setSelectedIds([])}
				statuses={statuses}
			/>
		</>
	);
}

Index.layout = page => <TenantLayout children={page} />

export default Index;
import React, { useEffect, useRef, useState } from 'react';
import { useMediaQuery } from 'react-responsive';
import TenantLayout from '../../Layouts/TenantLayout';
import { PageHeader, PageHeaderActions, PageHeaderTitle } from '../../components/PageHeader/PageHeader';
import SingerTableDesktop from './SingerTableDesktop';
import SingerTableMobile from './SingerTableMobile';
import AppHead from '../../components/AppHead';
import { usePage } from '@inertiajs/react';
import IndexContainer from '../../components/IndexContainer';
import SingerFilters from '../../components/SingerFilters';
import useFilterPane from '../../hooks/useFilterPane';
import FilterSortPane from '../../components/FilterSortPane';
import useSortFilterForm from '../../hooks/useSortFilterForm';
import EmptyState from '../../components/EmptyState';
import ImportSingersDialog from '../../components/ImportSingersDialog';
import useRoute from '../../hooks/useRoute';
import BulkEditSingersModal from './BulkEditSingersModal';
import useBulkEdit from '../../hooks/useBulkEdit';
import Dialog from '../../components/Dialog';
import BulkEditBar from '../../components/BulkEditBar';
import { usePhoneBreadcrumb } from '../../lib/reactNative';
import PageTopBar, { PageActionsMenu, PageTopNavigation } from '../../components/PageTopBar';
import ActionMenuItem from '../../components/ActionMenu/ActionMenuItem';
import Icon from '../../components/Icon';
import Button from '../../components/inputs/ButtonV2';
import RenewFeesDialog from './components/RenewFeesDialog';
import SingersFilterControlsPrototype from './SingersFilterControlsPrototype';
import SingerMobileTableControls from './SingerMobileTableControls';
import { areFilterValuesEqual } from '../../lib/filterValues';

const Index = ({ allSingers, statuses, defaultStatus, voiceParts, roles, ensembles, pagination, setSidebarOpen }) => {
	const [showFilters, setShowFilters] = useFilterPane();
	const isDesktop = useMediaQuery({ query: '(min-width: 1024px)' });
	const [showImportDialog, setShowImportDialog] = useState(false);
	const [showBulkRenewFeesDialog, setShowBulkRenewFeesDialog] = useState(false);
	const { can } = usePage().props;
	const { route } = useRoute();
	const bulkEdit = useBulkEdit(
		allSingers,
		can.update_singer || can.manage_finances,
		can.delete_singer,
		'Singer',
		!can.update_singer
	);

	const sorts = [
		{ id: 'full-name', name: 'First Name', default: true },
		{ id: 'last-name-first', name: 'Last Name' },
		{ id: 'status-title', name: 'Status' },
		// { id: 'part-title', name: 'Voice Part' },
		{ id: 'paid_until', name: 'Paid Until' },
	];

	const filters = [
		{ name: 'user.name', defaultValue: '' },
		{ name: 'status', multiple: true, defaultValue: [defaultStatus] },
		{ name: 'enrolments.voice_part_id', multiple: true, defaultValue: [] },
		{ name: 'roles.id', multiple: true, defaultValue: [] },
		{ name: 'fee_status', multiple: true, defaultValue: [] },
		{ name: 'enrolments.ensemble_id', multiple: true, defaultValue: [] },
	];

	const sortFilterForm = useSortFilterForm('singers.index', filters, sorts);
	const [draftFilterData, setDraftFilterData] = useState(() =>
		Object.fromEntries(filters.map(({ name }) => [name, sortFilterForm.data[name]]))
	);
	const appliedFilterDataRef = useRef(draftFilterData);
	const [applyDraftFilters, setApplyDraftFilters] = useState(false);
	const hasChangedFilters = filters.some(({ name, multiple, defaultValue }) =>
		!areFilterValuesEqual(sortFilterForm.data[name], defaultValue ?? (multiple ? [] : ''))
	);
	const draftSingerFilterForm = {
		data: draftFilterData,
		setData: (name, value) => setDraftFilterData(previousData => ({ ...previousData, [name]: value })),
		submit: event => event?.preventDefault(),
	};

	useEffect(() => {
		const appliedFilterData = Object.fromEntries(filters.map(({ name }) => [name, sortFilterForm.data[name]]));
		const filtersChanged = filters.some(({ name }) =>
			!areFilterValuesEqual(appliedFilterData[name], appliedFilterDataRef.current[name])
		);

		if (filtersChanged) {
			appliedFilterDataRef.current = appliedFilterData;
			setDraftFilterData(appliedFilterData);
		}
	}, [sortFilterForm.data]);

	useEffect(() => {
		if (!applyDraftFilters) {
			return;
		}

		sortFilterForm.submit(undefined);
		setApplyDraftFilters(false);
		setShowFilters(false);
	}, [applyDraftFilters, sortFilterForm.data]);

	const applyFilterChanges = () => {
		sortFilterForm.setData({ ...sortFilterForm.data, ...draftFilterData });
		setApplyDraftFilters(true);
	};
	const cancelFilterChanges = () => {
		const appliedFilterData = Object.fromEntries(filters.map(({ name }) => [name, sortFilterForm.data[name]]));
		appliedFilterDataRef.current = appliedFilterData;
		setDraftFilterData(appliedFilterData);
		setShowFilters(false);
	};

	const tableActions = [
		{
			label: 'Import',
			icon: 'file-import',
			onClick: () => setShowImportDialog(true),
			can: 'import_singers',
		},
		{
			label: 'Export',
			icon: 'file-export',
			url: route('singers.export'),
			download: true,
			can: 'export_singers',
		},
	].filter(action => can[action.can]);

	const actions = [
		{
			label: 'Add New',
			icon: 'user-plus',
			url: route('singers.create'),
			intent: 'primary',
			weight: 'solid',
			can: 'create_singer',
		},
		{
			label: 'Voice Parts',
			icon: 'users-class',
			url: route('voice-parts.index'),
			can: 'list_voice_parts',
		},
		{ label: 'User Roles', icon: 'user-tag', url: route('roles.index'), can: 'list_roles' },
	].filter(action => (action?.can ? can[action.can] : !!action));

	usePhoneBreadcrumb('Singers', []);

	return (
		<>
			<AppHead title="Singers" />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation title="Singers">
					<PageActionsMenu>
						{actions.map((action, key) => (
							<ActionMenuItem
								key={key}
								url={action.url}
								onClick={action.onClick}
								download={action.download}
								intent={action.intent}
								method={action.method}
								disabled={action.disabled}
							>
								<Icon icon={action.icon} mr />
								{action.label}
							</ActionMenuItem>
						))}
					</PageActionsMenu>
				</PageTopNavigation>
			</PageTopBar>

			<PageHeader className="xl:items-baseline">
				<PageHeaderTitle>
					<Icon icon="users" type="solid" className="mr-2" /> Singers
				</PageHeaderTitle>
				<PageHeaderActions>
					<div className="flex gap-3 justify-between items-baseline grow ml-4">
						<div className="flex gap-3 items-baseline">
							{actions.slice(1).map((action, key) => (
								<React.Fragment key={key}>
									{key !== 0 && (
										<div className="h-6 w-px bg-gray-200" />
									)}
									{action.label ? (
										<a
											href={action.url}
											onClick={action.onClick}
											className="text-sm flex gap-x-1.5 items-center text-purple-700 hover:text-sky-500"
										>
											<Icon icon={action.icon} />
											{action.label}
										</a>
									) : (
										action
									)}
								</React.Fragment>
							))}
						</div>
						<div className="flex gap-3 items-baseline">

							{actions.slice(0, 1).map((action, key) => (
								<React.Fragment key={key}>
									{action.label ? (
										<Button
											href={action.url}
											onClick={action.onClick}
											size="md"
											intent={action.intent}
											weight={action.weight ?? 'clear'}
											className={action.className}
											external={action.download}
											download={action.download}
											method={action.method}
											disabled={action.disabled}
										>
											<Icon icon={action.icon} />
											{action.label}
										</Button>
									) : (
										action
									)}
								</React.Fragment>
							))}
						</div>
					</div>
				</PageHeaderActions>
			</PageHeader>

			<Dialog
				title={`Delete ${bulkEdit.selectedIds.length} Singers?`}
				isOpen={bulkEdit.showDeleteModal}
				setIsOpen={bulkEdit.setShowDeleteModal}
				okLabel="Delete"
				okColour="danger"
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

			<BulkEditBar
				bulkEdit={bulkEdit}
				actions={
					can.manage_finances && (
						<Button
							size="sm"
							intent="secondary"
							weight="clear"
							theme="dark"
							onClick={() => setShowBulkRenewFeesDialog(true)}
							className="gap-1 hover:bg-gray-600 hover:!text-gray-100"
						>
							<Icon icon="calendar-check" />
							Renew Fees
						</Button>
					)
				}
			/>

			<IndexContainer
				showFilters={!isDesktop && showFilters}
				tableControls={
					isDesktop ? (
						<SingersFilterControlsPrototype
							filters={[
								{ name: 'user.name', label: 'Name', type: 'text', defaultValue: '' },
								{
									name: 'status',
									label: 'Status',
									multiple: true,
									defaultValue: [defaultStatus],
									options: statuses.map(status => ({ id: status.id, name: status.name })),
								},
								{
									name: 'enrolments.voice_part_id',
									label: 'Voice Part',
									multiple: true,
									defaultValue: [],
									options: voiceParts.map(part => ({
										id: part.id,
										name: part.title,
										colour: part.colour,
									})),
								},
								{
									name: 'roles.id',
									label: 'Role',
									multiple: true,
									defaultValue: [],
									options: roles.map(role => ({ id: role.id, name: role.name })),
								},
				{
					name: 'fee_status',
					label: 'Fee Status',
					multiple: true,
					defaultValue: [],
									options: [
										{ id: 'paid', name: 'Paid' },
										{ id: 'expires-soon', name: 'Expires Soon' },
										{ id: 'expired', name: 'Expired' },
										{ id: 'unknown', name: 'Unknown' },
									],
								},
								...(ensembles.length > 1
									? [
											{
												name: 'enrolments.ensemble_id',
												label: 'Ensemble',
												multiple: true,
												defaultValue: [],
												options: ensembles.map(ensemble => ({
													id: ensemble.id,
													name: ensemble.name,
												})),
											},
									  ]
									: []),
							]}
							sorts={sorts}
							form={sortFilterForm}
							tableActions={tableActions}
						/>
					) : (
						<SingerMobileTableControls
							bulkEdit={bulkEdit}
							onToggleFilters={() => setShowFilters(previousValue => !previousValue)}
							hasChangedFilters={hasChangedFilters}
							sorts={sorts}
							form={sortFilterForm}
							tableActions={tableActions}
						/>
					)
				}
				filterPane={
					<FilterSortPane
						sorts={null}
						filters={
							<SingerFilters
								statuses={statuses}
								defaultStatus={defaultStatus}
								voiceParts={voiceParts}
								roles={roles}
								form={draftSingerFilterForm}
								ensembles={ensembles}
							/>
						}
						closeFn={() => setShowFilters(false)}
						onApply={applyFilterChanges}
						onCancel={cancelFilterChanges}
					/>
				}
				tableMobile={
					<SingerTableMobile
						singers={allSingers}
						pagination={pagination}
						ensembles={ensembles}
						bulkEdit={bulkEdit}
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

			<RenewFeesDialog
				isOpen={showBulkRenewFeesDialog}
				setIsOpen={setShowBulkRenewFeesDialog}
				singerIds={bulkEdit.selectedIds}
				onSuccess={() => bulkEdit.setSelectedIds([])}
			/>
		</>
	);
};

Index.layout = page => <TenantLayout children={page} />;

export default Index;

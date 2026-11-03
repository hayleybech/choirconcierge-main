import React, { useEffect, useRef, useState } from 'react';
import { useMediaQuery } from 'react-responsive';
import TenantLayout from '../../Layouts/TenantLayout';
import {
	PageHeader,
	PageHeaderActions,
	PageHeaderContent,
	PageHeaderLink,
	PageHeaderLinks,
	PageHeaderTitle,
	PageHeaderTitleRow,
} from '../../components/PageHeader/PageHeader';
import SingerTableDesktop from './SingerTableDesktop';
import SingerTableMobile from './SingerTableMobile';
import AppHead from '../../components/AppHead';
import { usePage } from '@inertiajs/react';
import IndexContainer from '../../components/IndexContainer';
import SingerFilters from '../../components/SingerFilters';
import useFilterPane from '../../hooks/useFilterPane';
import FilterDrawer from '../../components/FilterDrawer';
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
import IndexTableControls from '../../components/IndexTableControls';
import { areFilterValuesEqual } from '../../lib/filterValues';
import Badge from '../../components/Badge';
import FeeStatus from '../../components/FeeStatus';
import SingerStatus from '../../SingerStatus';
import SingerStatusTag from '../../components/SingerStatusTag';
import VoicePartTag from '../../components/VoicePartTag';
import ButtonLink from '../../components/inputs/ButtonLinkV2';

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
		{ name: 'user.name', label: 'Name', type: 'text', defaultValue: '' },
		{
			name: 'status',
			label: 'Status',
			multiple: true,
			defaultValue: [defaultStatus],
			options: statuses.map(status => ({ id: status.id, name: status.name })),
			renderOption: (option, compact) => (
				<SingerStatusTag status={new SingerStatus(option.id)} withLabel compact={compact} />
			),
		},
		{
			name: 'enrolments.voice_part_id',
			label: 'Voice Part',
			multiple: true,
			defaultValue: [],
			options: voiceParts.map(part => ({ id: part.id, name: part.title, colour: part.colour })),
			renderOption: (option, compact) => (
				<VoicePartTag title={option.name} colour={option.colour} compact={compact} />
			),
		},
		{
			name: 'roles.id',
			label: 'Role',
			multiple: true,
			defaultValue: [],
			options: roles.map(role => ({ id: role.id, name: role.name })),
			renderOption: (option, compact) => (
				<Badge colour="bg-gray-100 text-gray-800" compact={compact}>
					{option.name}
				</Badge>
			),
		},
		{
			name: 'fee_status',
			label: 'Fee Status',
			multiple: true,
			defaultValue: [],
			allValuesLabel: 'All',
			options: [
				{ id: 'paid', name: 'Paid' },
				{ id: 'expires-soon', name: 'Expires Soon' },
				{ id: 'expired', name: 'Expired' },
				{ id: 'unknown', name: 'Unknown' },
			],
			renderOption: (option, compact) => <FeeStatus status={option.id} compact={compact} />,
		},
		...(ensembles.length > 1
			? [
					{
						name: 'enrolments.ensemble_id',
						label: 'Ensemble',
						multiple: true,
						defaultValue: [],
						options: ensembles.map(ensemble => ({ id: ensemble.id, name: ensemble.name })),
						renderOption: (option, compact) => (
							<Badge colour="bg-purple-100 text-purple-800" compact={compact}>
								{option.name}
							</Badge>
						),
					},
			  ]
			: []),
	];

	const sortFilterForm = useSortFilterForm('singers.index', filters, sorts);
	const [draftFilterData, setDraftFilterData] = useState(() =>
		Object.fromEntries(filters.map(({ name }) => [name, sortFilterForm.data[name]]))
	);
	const appliedFilterDataRef = useRef(draftFilterData);
	const [applyDraftFilters, setApplyDraftFilters] = useState(false);
	const draftSingerFilterForm = {
		data: draftFilterData,
		setData: (name, value) => setDraftFilterData(previousData => ({ ...previousData, [name]: value })),
		submit: event => event?.preventDefault(),
	};

	useEffect(() => {
		const appliedFilterData = Object.fromEntries(filters.map(({ name }) => [name, sortFilterForm.data[name]]));
		const filtersChanged = filters.some(
			({ name }) => !areFilterValuesEqual(appliedFilterData[name], appliedFilterDataRef.current[name])
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
			href: route('singers.export'),
			download: true,
			can: 'export_singers',
		},
	].filter(action => can[action.can]);

	const links = [
		{
			label: 'Voice Parts',
			icon: 'users-class',
			href: route('voice-parts.index'),
			can: 'list_voice_parts',
		},
		{ label: 'User Roles', icon: 'user-tag', href: route('roles.index'), can: 'list_roles' },
	].filter(action => (action?.can ? can[action.can] : !!action));

	const actions = [
		{
			label: 'Add New',
			icon: 'user-plus',
			href: route('singers.create'),
			intent: 'primary',
			weight: 'solid',
			can: 'create_singer',
		},
	].filter(action => (action?.can ? can[action.can] : !!action));

	usePhoneBreadcrumb('Singers', []);

	return (
		<>
			<AppHead title="Singers" />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation title="Singers">
					<PageActionsMenu>
						{[...actions, ...links].map((action, key) => (
							<ActionMenuItem key={key} url={action.href} {...action}>
								<Icon icon={action.icon} mr />
								{action.label}
							</ActionMenuItem>
						))}
					</PageActionsMenu>
				</PageTopNavigation>
			</PageTopBar>

			<PageHeader hideOnMobile className="xl:items-center">
				<PageHeaderContent>
					<PageHeaderTitleRow>
						<PageHeaderTitle>
							<Icon icon="users" type="solid" className="mr-2" /> Singers
						</PageHeaderTitle>
						{links.length > 0 && (
							<PageHeaderLinks>
								{links.map(action => (
									<PageHeaderLink key={action.label} {...action}>
										{action.label}
									</PageHeaderLink>
								))}
							</PageHeaderLinks>
						)}
						<PageHeaderActions>
							{actions.map(action => (
								<ButtonLink key={action.label} {...action} size="md" weight={action.weight ?? 'clear'}>
									<Icon icon={action.icon} mr />
									{action.label}
								</ButtonLink>
							))}
						</PageHeaderActions>
					</PageHeaderTitleRow>
				</PageHeaderContent>
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
							className="gap-1 hover:bg-gray-600 hover:text-gray-100!"
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
					<IndexTableControls
						filters={filters}
						sorts={sorts}
						form={sortFilterForm}
						tableActions={tableActions}
						bulkEdit={bulkEdit}
						filterLabel="singers"
						onToggleFilters={() => setShowFilters(previousValue => !previousValue)}
					/>
				}
				filterPane={
					<FilterDrawer
						closeFn={() => setShowFilters(false)}
						onApply={applyFilterChanges}
						onCancel={cancelFilterChanges}
					>
						<SingerFilters
							statuses={statuses}
							defaultStatus={defaultStatus}
							voiceParts={voiceParts}
							roles={roles}
							form={draftSingerFilterForm}
							ensembles={ensembles}
						/>
					</FilterDrawer>
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

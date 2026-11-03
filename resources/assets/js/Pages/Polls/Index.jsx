import React from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import AppHead from '../../components/AppHead';
import {
	PageHeader,
	PageHeaderActions,
	PageHeaderContent,
	PageHeaderTitle,
} from '../../components/PageHeader/PageHeader';
import PageTopBar, { PageActionsMenu, PageTopNavigation } from '../../components/PageTopBar';
import ActionMenuItem from '../../components/ActionMenu/ActionMenuItem';
import useRoute from '../../hooks/useRoute';
import { Link } from '@inertiajs/react';
import Table, {
	TableCell,
	THead,
	TBody,
	TableHeading,
	TableSelectAll,
	TItemRow,
	TableCellSelect,
} from '../../components/Table';
import Pagination from '../../components/Pagination';
import Icon from '../../components/Icon';
import IndexContainer from '../../components/IndexContainer';
import IndexTableControls from '../../components/IndexTableControls';
import DateTag from '../../components/DateTag';
import TableMobile, {
	TableMobileSelect,
	TableMobileSelectableLink,
	TableMobileListItem,
} from '../../components/TableMobile';
import Badge from '../../components/Badge';
import EnsembleBadges from '../../components/EnsembleBadges';
import useFilterPane from '../../hooks/useFilterPane';
import useSortFilterForm from '../../hooks/useSortFilterForm';
import FilterDrawer from '../../components/FilterDrawer';
import PollFilters from '../../components/PollFilters';
import TableHeadingSort from '../../components/TableHeadingSort';
import useBulkEdit from '../../hooks/useBulkEdit';
import BulkEditBar from '../../components/BulkEditBar';
import BulkEditPollsModal from './BulkEditPollsModal';
import Dialog from '../../components/Dialog';
import Button from '../../components/inputs/ButtonV2';

const Index = ({ polls, pagination, ensembles, can, tenant, setSidebarOpen }) => {
	const { route } = useRoute();
	const [showFilters, setShowFilters] = useFilterPane();

	const showEnsembleColumn = (ensembles ?? tenant.ensembles).length > 1;

	const sorts = [
		{ id: 'created_at', name: 'Created', default: true },
		{ id: 'title', name: 'Title' },
		{ id: 'votes_count', name: 'Voted' },
		{ id: 'close_at', name: 'Deadline' },
	];

	const filters = [
		{ name: 'title', label: 'Title', type: 'text', defaultValue: '' },
		{
			name: 'status',
			label: 'Status',
			defaultValue: '',
			options: [
				{ id: 'open', name: 'Open', icon: 'lock-open', textColour: 'text-emerald-700' },
				{ id: 'closed', name: 'Closed', icon: 'lock', textColour: 'text-gray-600' },
			],
			renderOption: option => (
				<span className={`inline-flex items-center gap-1.5 ${option.textColour}`}>
					<Icon icon={option.icon} />
					{option.name}
				</span>
			),
		},
		...(showEnsembleColumn
			? [
					{
						name: 'ensembles.id',
						label: 'Ensemble',
						multiple: true,
						options: (ensembles ?? tenant.ensembles).map(ensemble => ({
							id: ensemble.id,
							name: ensemble.name,
						})),
						renderOption: (option, compact) => (
							<Badge colour="bg-purple-100 text-purple-800" compact={compact}>
								{option.name}
							</Badge>
						),
					},
			  ]
			: []),
	];

	const sortFilterForm = useSortFilterForm('polls.index', filters, sorts);

	const bulkEdit = useBulkEdit(polls, can.update_poll, can.delete_poll, 'Poll');

	const actions = [
		{ label: 'Add New', url: route('polls.create'), icon: 'plus', intent: 'primary', weight: 'solid' },
	];

	return (
		<>
			<AppHead title="Polls" />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation title="Polls">
					<PageActionsMenu>
						{actions.map((action, key) => (
							<ActionMenuItem key={key} url={action.url} onClick={action.onClick} intent={action.intent}>
								<Icon icon={action.icon} mr />
								{action.label}
							</ActionMenuItem>
						))}
					</PageActionsMenu>
				</PageTopNavigation>
			</PageTopBar>
			<PageHeader hideOnMobile>
				<PageHeaderContent>
					<PageHeaderTitle>
						<Icon icon="poll" type="solid" className="mr-2" /> Polls
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
				title={`Delete ${bulkEdit.selectedIds.length} Polls?`}
				isOpen={bulkEdit.showDeleteModal}
				setIsOpen={bulkEdit.setShowDeleteModal}
				okLabel="Delete"
				okColour="danger"
				okMethod="post"
				data={{ poll_ids: bulkEdit.selectedIds }}
				okUrl={route('polls.bulk-destroy')}
				onOk={() => {
					bulkEdit.setSelectedIds([]);
					bulkEdit.setShowDeleteModal(false);
					bulkEdit.setIsForcedMobile(false);
				}}
			>
				Are you sure you want to delete the selected polls? This action cannot be undone.
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
						filterLabel="polls"
						onToggleFilters={() => setShowFilters(previousValue => !previousValue)}
					/>
				}
				filterPane={
					<FilterDrawer closeFn={() => setShowFilters(false)}>
						<PollFilters form={sortFilterForm} ensembles={ensembles} />
					</FilterDrawer>
				}
				tableMobile={
					<div>
						<TableMobile pagination={<Pagination details={pagination} />}>
							{polls.map(p => (
								<TableMobileListItem key={p.id}>
									<TableMobileSelect bulkEdit={bulkEdit} value={p.id} />
									<TableMobileSelectableLink
										url={route('polls.show', { poll: p.id })}
										bulkEdit={bulkEdit}
										value={p.id}
									>
										<div className="w-full">
											<div className="min-w-0 grid grid-cols-2 lg:gap-4 w-full pr-2">
												<div className="text-sm font-medium text-purple-600 truncate">
													{p.title}
												</div>

												{p.is_closed ? (
													<span className="text-gray-600 flex items-center justify-end text-sm">
														<Icon icon="lock" mr /> Closed
													</span>
												) : (
													<span className="text-emerald-700 flex items-center justify-end text-sm">
														<Icon icon="lock-open" mr /> Open
													</span>
												)}
											</div>
											{showEnsembleColumn && (
												<EnsembleBadges
													ensembles={p.ensembles}
													className="flex flex-wrap gap-1 mt-1"
												/>
											)}
										</div>
									</TableMobileSelectableLink>
								</TableMobileListItem>
							))}
						</TableMobile>
					</div>
				}
				tableDesktop={
					<Table pagination={<Pagination details={pagination} />}>
						<THead>
							<tr>
								<TableSelectAll bulkEdit={bulkEdit} totalItems={polls.length} />
								<TableHeading>
									<TableHeadingSort form={sortFilterForm} sort="title">
										Title
									</TableHeadingSort>
								</TableHeading>
								{showEnsembleColumn && <TableHeading>Ensembles</TableHeading>}
								<TableHeading>Status</TableHeading>
								<TableHeading>
									<TableHeadingSort form={sortFilterForm} sort="close_at">
										Deadline
									</TableHeadingSort>
								</TableHeading>
								<TableHeading>
									<TableHeadingSort form={sortFilterForm} sort="votes_count">
										Voted
									</TableHeadingSort>
								</TableHeading>
								<TableHeading>
									<TableHeadingSort form={sortFilterForm} sort="created_at">
										Created
									</TableHeadingSort>
								</TableHeading>
							</tr>
						</THead>
						<TBody>
							{polls.map(p => (
								<TItemRow key={p.id} bulkEdit={bulkEdit} value={p.id}>
									<TableCellSelect bulkEdit={bulkEdit} value={p.id} />
									<TableCell>
										<Link
											href={route('polls.show', { poll: p.id })}
											className="hover:underline text-purple-700"
											onClick={e => e.stopPropagation()}
										>
											{p.title}
										</Link>
									</TableCell>
									{showEnsembleColumn && (
										<TableCell>
											<EnsembleBadges
												ensembles={p.ensembles}
												className="flex flex-wrap gap-1 max-w-[200px]"
											/>
										</TableCell>
									)}
									<TableCell>
										{p.is_closed ? (
											<span className="text-gray-600 flex items-center">
												<Icon icon="lock" mr /> Closed
											</span>
										) : (
											<span className="text-emerald-700 flex items-center">
												<Icon icon="lock-open" mr /> Open
											</span>
										)}
									</TableCell>
									<TableCell>{p.close_at ? new Date(p.close_at).toLocaleString() : '-'}</TableCell>
									<TableCell>{p.votes_count ?? 0}</TableCell>
									<TableCell>
										<DateTag
											icon="pencil"
											date={p.created_at}
											format="DATE_SHORT"
											className="text-gray-400"
										/>
									</TableCell>
								</TItemRow>
							))}
						</TBody>
					</Table>
				}
			/>

			<BulkEditPollsModal
				isOpen={bulkEdit.showEditModal}
				setIsOpen={bulkEdit.setShowEditModal}
				selectedPollIds={bulkEdit.selectedIds}
				key={bulkEdit.selectedIds}
				ensembles={ensembles ?? tenant.ensembles}
				onSuccess={() => bulkEdit.clearSelections()}
			/>
		</>
	);
};

Index.layout = page => <TenantLayout children={page} />;

export default Index;

import React from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import AppHead from '../../components/AppHead';
import PageHeader from '../../components/PageHeader/PageHeader';
import useRoute from '../../hooks/useRoute';
import { Link, usePage } from '@inertiajs/react';
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
import DateTag from '../../components/DateTag';
import TableMobile, { TableMobileSelect, TableMobileSelectableLink, TableMobileListItem } from '../../components/TableMobile';
import Badge from '../../components/Badge';
import useFilterPane from '../../hooks/useFilterPane';
import useSortFilterForm from '../../hooks/useSortFilterForm';
import FilterSortPane from '../../components/FilterSortPane';
import Sorts from '../../components/Sorts';
import PollFilters from '../../components/PollFilters';
import TableHeadingSort from '../../components/TableHeadingSort';
import useBulkEdit from '../../hooks/useBulkEdit';
import BulkEditBarMobile from '../../components/BulkEditBarMobile';
import BulkEditPollsModal from './BulkEditPollsModal';

const Index = ({ polls, pagination, ensembles, can, tenant }) => {
	const { route } = useRoute();
	const [showFilters, setShowFilters, filterAction, hasNonDefaultFilters] = useFilterPane();

	const showEnsembleColumn = (ensembles ?? tenant.ensembles).length > 1;

	const sorts = [
		{ id: 'created_at', name: 'Created', default: true },
		{ id: 'title', name: 'Title' },
		{ id: 'votes_count', name: 'Votes' },
		{ id: 'close_at', name: 'Deadline' },
	];

	const filters = [
		{ name: 'title', defaultValue: '' },
		{ name: 'status', defaultValue: '' },
		{ name: 'ensembles.id', multiple: true },
	];

	const sortFilterForm = useSortFilterForm('polls.index', filters, sorts);

	const bulkEdit = useBulkEdit(polls, can.update_poll);

	const actions = [
		{ label: 'Add New', url: route('polls.create'), icon: 'plus', variant: 'primary' },
	];

	if (bulkEdit.action) {
		actions.push(bulkEdit.action);
	}

	actions.push(filterAction);

	return (
		<>
			<AppHead title="Polls" />
			<PageHeader
				title="Polls"
				icon="poll"
				breadcrumbs={[
					{ name: 'Dashboard', url: route('dash') },
					{ name: 'Polls', url: route('polls.index') },
				]}
				actions={actions}
				optionsVariant={hasNonDefaultFilters ? 'success-solid' : 'secondary'}
			/>

			<BulkEditBarMobile totalItems={polls.length} bulkEdit={bulkEdit} />

			<IndexContainer
				showFilters={showFilters}
				filterPane={
					<FilterSortPane
						sorts={<Sorts sorts={sorts} form={sortFilterForm} />}
						filters={<PollFilters form={sortFilterForm} ensembles={ensembles} />}
						closeFn={() => setShowFilters(false)}
					/>
				}
				tableMobile={
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
													<Icon icon="check" mr /> Open
												</span>
											)}
										</div>
										{showEnsembleColumn && p.ensembles?.length > 0 && (
											<div className="flex flex-wrap gap-1 mt-1">
												{p.ensembles.map(e => (
													<Badge key={e.id} colour="bg-purple-100 text-purple-800">
														{e.name}
													</Badge>
												))}
											</div>
										)}
									</div>
								</TableMobileSelectableLink>
							</TableMobileListItem>
						))}
					</TableMobile>
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
										Votes
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
											<div className="flex flex-wrap gap-1 max-w-[200px]">
												{p.ensembles?.map(e => (
													<Badge key={e.id} colour="bg-purple-100 text-purple-800">
														{e.name}
													</Badge>
												)) || '-'}
												{p.ensembles?.length === 0 && '-'}
											</div>
										</TableCell>
									)}
									<TableCell>
										{p.is_closed ? (
											<span className="text-gray-600 flex items-center">
												<Icon icon="lock" mr /> Closed
											</span>
										) : (
											<span className="text-emerald-700 flex items-center">
												<Icon icon="check" mr /> Open
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
				isOpen={bulkEdit.showModal}
				setIsOpen={bulkEdit.setShowModal}
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

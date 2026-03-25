import React from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import AppHead from '../../components/AppHead';
import PageHeader from '../../components/PageHeader/PageHeader';
import useRoute from '../../hooks/useRoute';
import { Link, usePage } from '@inertiajs/react';
import Table, { TableCell, THead, TBody, TableHeading } from '../../components/Table';
import Pagination from '../../components/Pagination';
import Icon from '../../components/Icon';
import collect from 'collect.js';
import IndexContainer from '../../components/IndexContainer';
import DateTag from '../../components/DateTag';
import TableMobile, { TableMobileItem } from '../../components/TableMobile';
import Badge from '../../components/Badge';
import useFilterPane from '../../hooks/useFilterPane';
import useSortFilterForm from '../../hooks/useSortFilterForm';
import FilterSortPane from '../../components/FilterSortPane';
import Sorts from '../../components/Sorts';
import PollFilters from '../../components/PollFilters';
import TableHeadingSort from '../../components/TableHeadingSort';

const Index = ({ polls, pagination, ensembles }) => {
	const { route } = useRoute();
	const [showFilters, setShowFilters, filterAction, hasNonDefaultFilters] = useFilterPane();

	const { tenant } = usePage().props;

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

	const headings = collect({
		title: (
			<TableHeading>
				<TableHeadingSort form={sortFilterForm} sort="title">Title</TableHeadingSort>
			</TableHeading>
		),
		ensembles: <TableHeading>Ensembles</TableHeading>,
		status: <TableHeading>Status</TableHeading>,
		deadline: (
			<TableHeading>
				<TableHeadingSort form={sortFilterForm} sort="close_at">Deadline</TableHeadingSort>
			</TableHeading>
		),
		votes: (
			<TableHeading>
				<TableHeadingSort form={sortFilterForm} sort="votes_count">Votes</TableHeadingSort>
			</TableHeading>
		),
		created_at: (
			<TableHeading>
				<TableHeadingSort form={sortFilterForm} sort="created_at">Created</TableHeadingSort>
			</TableHeading>
		),
	}).filter((h, key) => showEnsembleColumn || key !== 'ensembles');

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
				actions={[
					{ label: 'Add New', url: route('polls.create'), icon: 'plus', variant: 'primary' },
					filterAction,
				]}
				optionsVariant={hasNonDefaultFilters ? 'success-solid' : 'secondary'}
			/>

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
							<TableMobileItem key={p.id} url={route('polls.show', { poll: p })}>
								<div className="min-w-0 grid grid-cols-2 lg:gap-4 w-full pr-2">
									<div className="text-sm font-medium text-purple-600 truncate">{p.title}</div>

									{p.is_closed ? (
										<span className="text-gray-600 flex items-center justify-end">
											<Icon icon="lock" mr /> Closed
										</span>
									) : (
										<span className="text-emerald-700 flex items-center justify-end">
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
							</TableMobileItem>
						))}
					</TableMobile>
				}
				tableDesktop={
					<Table pagination={<Pagination details={pagination} />}>
						<THead>
							<tr>{headings.values().toArray()}</tr>
						</THead>
						<TBody>
							{polls.map(p => (
								<tr key={p.id}>
									<TableCell>
										<Link
											href={route('polls.show', { poll: p.id })}
											className="hover:underline text-purple-700"
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
								</tr>
							))}
						</TBody>
					</Table>
				}
			/>
		</>
	);
};

Index.layout = page => <TenantLayout children={page} />;

export default Index;

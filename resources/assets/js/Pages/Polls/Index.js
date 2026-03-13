import React from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import AppHead from '../../components/AppHead';
import PageHeader from '../../components/PageHeader/PageHeader';
import useRoute from '../../hooks/useRoute';
import { Link } from '@inertiajs/react';
import Table, { TableCell } from '../../components/Table';
import Pagination from '../../components/Pagination';
import Icon from '../../components/Icon';
import collect from 'collect.js';
import IndexContainer from '../../components/IndexContainer';
import DateTag from '../../components/DateTag';
import TableMobile, { TableMobileItem } from '../../components/TableMobile';

const Index = ({ polls, pagination }) => {
	const { route } = useRoute();

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
				actions={[{ label: 'Add New', url: route('polls.create'), icon: 'plus', variant: 'primary' }]}
			/>

			<IndexContainer
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
							</TableMobileItem>
						))}
					</TableMobile>
				}
				tableDesktop={
					<Table
						headings={collect({
							title: 'Title',
							status: 'Status',
							deadline: 'Deadline',
							votes: 'Votes',
							created_at: 'Created',
						})}
						body={polls.map(p => (
							<tr key={p.id}>
								<TableCell>
									<Link
										href={route('polls.show', { poll: p.id })}
										className="hover:underline text-purple-700"
									>
										{p.title}
									</Link>
								</TableCell>
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
						pagination={<Pagination details={pagination} />}
					/>
				}
			/>
		</>
	);
};

Index.layout = page => <TenantLayout children={page} />;

export default Index;

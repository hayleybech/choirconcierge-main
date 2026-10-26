import React from 'react';
import Table, { TableCell, THead, TBody, TableHeading } from '../../../components/Table';
import DateTag from '../../../components/DateTag';
import { Link } from '@inertiajs/react';
import useRoute from '../../../hooks/useRoute';
import Icon from '../../../components/Icon';
import Pagination from '../../../components/Pagination';
import MailStatusTag, { mailTypeIcons } from '../../../components/MailStatusTag';
import TableHeadingSort from '../../../components/TableHeadingSort';
 
const MailLogTableDesktop = ({ logs, sortFilterForm }) => {
	const { route } = useRoute();

	return (
		<Table pagination={<Pagination details={logs} />}>
			<THead>
				<tr>
					<TableHeading>
						<TableHeadingSort form={sortFilterForm} sort="subject">
							Subject
						</TableHeadingSort>
					</TableHeading>
					<TableHeading>
						<TableHeadingSort form={sortFilterForm} sort="tenant">
							Tenant
						</TableHeadingSort>
					</TableHeading>
					<TableHeading>
						<TableHeadingSort form={sortFilterForm} sort="from">
							From
						</TableHeadingSort>
					</TableHeading>
					<TableHeading>
						<TableHeadingSort form={sortFilterForm} sort="to">
							To
						</TableHeadingSort>
					</TableHeading>
					<TableHeading>
						<TableHeadingSort form={sortFilterForm} sort="size">
							Size
						</TableHeadingSort>
					</TableHeading>
					<TableHeading>
						<TableHeadingSort form={sortFilterForm} sort="opens">
							Opens
						</TableHeadingSort>
					</TableHeading>
					<TableHeading>Status</TableHeading>
					<TableHeading>
						<TableHeadingSort form={sortFilterForm} sort="created_at">
							Date Created
						</TableHeadingSort>
					</TableHeading>
				</tr>
			</THead>
			<TBody>
				{logs.data.map(log => {
					const mailType = log.uid.split('-')[0];
					return (
						<tr key={log.id}>
							<TableCell compact>
								<Link
									href={route('central.mail-logs.show', { mail_log: log })}
									className="text-purple-600 hover:text-purple-800 focus:text-purple-800"
								>
									<Icon icon={mailTypeIcons[mailType] ?? 'question'} mr />

									{log.subject}

									{!!log.has_attachments && <Icon icon="paperclip" ml />}
								</Link>
							</TableCell>
							<TableCell compact>
								{log.tenants.length > 0 ? (
									log.tenants.map(tenant => (
										<div key={tenant.id} className="text-xs">
											{tenant.name}
										</div>
									))
								) : (
									<span className="text-gray-400 text-xs">—</span>
								)}
							</TableCell>
							<TableCell compact>
								{log.from_user ? (
									<Link
										href={route('central.users.show', { user: log.from_user })}
										className="text-purple-600 hover:text-purple-800 focus:text-purple-800"
									>
										{log.from}
									</Link>
								) : (
									log.from
								)}
							</TableCell>
							<TableCell compact>
								{log.latest_event?.user_group ? (
									<Link
										href={route('central.tenants.show', {
											tenant: log.latest_event.user_group.tenant,
										})}
										className="text-purple-600 hover:text-purple-800 focus:text-purple-800"
									>
										{log.latest_event.user_group.title}
									</Link>
								) : (
									log.to
								)}
							</TableCell>
							<TableCell compact className="whitespace-nowrap">
								{log.size ? (
									<span className="text-gray-500 text-xs">
										{log.size < 1024 * 1024
											? `${(log.size / 1024).toFixed(1)} KB`
											: `${(log.size / (1024 * 1024)).toFixed(1)} MB`}
									</span>
								) : (
									<span className="text-gray-400 text-xs">—</span>
								)}
							</TableCell>
							<TableCell compact>
								{log.opens_count > 0 ? (
									<span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
										<Icon icon="eye" mr />
										{log.opens_count}
									</span>
								) : (
									<span className="text-gray-400 text-xs">—</span>
								)}
							</TableCell>
							<TableCell compact>
								<MailStatusTag event={log.latest_event} />
							</TableCell>
							<TableCell compact>
								<DateTag icon="pencil" date={log.created_at} />
							</TableCell>
						</tr>
					);
				})}
			</TBody>
		</Table>
	);
};

export default MailLogTableDesktop;

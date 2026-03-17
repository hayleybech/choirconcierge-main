import React from 'react';
import Table, {TableCell} from "../../../components/Table";
import collect from "collect.js";
import DateTag from "../../../components/DateTag";
import {Link} from "@inertiajs/react";
import useRoute from "../../../hooks/useRoute";
import Icon from '../../../components/Icon';
import Pagination from '../../../components/Pagination';
import MailStatusTag, { mailTypeIcons } from '../../../components/MailStatusTag';

const MailLogTableDesktop = ({ logs }) => {
    const { route } = useRoute();

    const headings = collect({
        subject: 'Subject',
        tenant: 'Tenant',
        from: 'From',
        to: 'To',
        opens: 'Opens',
        status: 'Status',
        created_at: 'Date Created',
    });

    return (
		<Table
			headings={headings}
			body={logs.data.map(log => {
				const mailType = log.uid.split('-')[0];
				return (
					<tr key={log.id}>
						<TableCell>
							<Link
								href={route('central.mail-logs.show', { mail_log: log })}
								className="text-purple-600 hover:text-purple-800 focus:text-purple-800"
							>
								<Icon icon={mailTypeIcons[mailType] ?? 'question'} mr />

								{log.subject}

								{!!log.has_attachments && <Icon icon="paperclip" ml />}
							</Link>
						</TableCell>
						<TableCell>
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
						<TableCell>{log.from}</TableCell>
						<TableCell>{log.to}</TableCell>
						<TableCell>
							{log.opens_count > 0 ? (
								<span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
									<Icon icon="eye" mr />
									{log.opens_count}
								</span>
							) : (
								<span className="text-gray-400 text-xs">—</span>
							)}
						</TableCell>
						<TableCell>
							<MailStatusTag event={log.latest_event} />
						</TableCell>
						<TableCell>
							<DateTag icon="pencil" date={log.created_at} />
						</TableCell>
					</tr>
				);
			})}
			pagination={<Pagination details={logs} />}
		/>
	);
}

export default MailLogTableDesktop;
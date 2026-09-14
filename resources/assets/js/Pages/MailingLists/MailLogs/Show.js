import React from 'react';
import {
	PageHeader,
	PageHeaderBreadcrumbs,
	PageHeaderContent,
	PageHeaderMeta,
	PageHeaderTitle,
} from '../../../components/PageHeader/PageHeader';
import PageTopBar, { PageTopNavigation } from '../../../components/PageTopBar';
import classNames from '../../../classNames';
import AppHead from '../../../components/AppHead';
import DateTag from '../../../components/DateTag';
import useRoute from '../../../hooks/useRoute';
import Prose from '../../../components/Prose';
import Icon from '../../../components/Icon';
import { mailIconColours, mailIcons, mailTypeIcons } from '../../../components/MailStatusTag';
import TenantLayout from '../../../Layouts/TenantLayout';
import MailStatusDetail from '../../../components/MailStatusDetail';
import TrialAntiSpamNotice from '../TrialAntiSpamNotice';
import SectionLayout from '../../Singers/SectionLayout';
const Show = ({ log, setSidebarOpen }) => {
	const { route } = useRoute();

	const mailType = log.uid.split('-')[0];

	const breadcrumbs = [
		{ name: 'Communications', url: route('communications.index') },
		{ name: log.subject, url: route('communications.show', { mail_log: log }) },
	];

	return (
		<>
			<AppHead title={`${log.subject} - Mail Logs`} />

			<TrialAntiSpamNotice />

			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation breadcrumbs={breadcrumbs}></PageTopNavigation>
			</PageTopBar>
			<PageHeader>
				<PageHeaderContent>
					<PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
					<PageHeaderTitle>
						<Icon icon={mailTypeIcons[mailType] ?? 'question'} type="solid" className="mr-2" />
						{log.subject}
					</PageHeaderTitle>
					<PageHeaderMeta>
						<div>
							Opens:{' '}
							<span className="ml-0.5 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
								<Icon icon="eye" mr />
								<span className="font-medium">{log.opens_count}</span>
							</span>
						</div>
						<div>From: {log.from}</div>
						<div>To: {log.to}</div>
						{log.cc && <div>Cc: {log.cc}</div>}
						{log.bcc && <div>Bcc: {log.bcc}</div>}
						<div className="flex items-center gap-4">
							<div>
								<Icon icon="paperclip" mr />
								{log.has_attachments ? 'Has attachments' : 'No attachments'}
							</div>
							{log.size > 0 && (
								<div>
									<Icon icon="hdd" mr />
									{log.size < 1024 * 1024
										? `${(log.size / 1024).toFixed(1)} KB`
										: `${(log.size / (1024 * 1024)).toFixed(1)} MB`}
								</div>
							)}
						</div>
						<div className="flex items-center gap-2">
							<DateTag icon="pencil" date={log.created_at} label="Created" />
							<DateTag icon="pencil" date={log.updated_at} label="Updated" />
						</div>
					</PageHeaderMeta>
				</PageHeaderContent>
			</PageHeader>

			<SectionLayout
				columns={[
					[
						{
							title: 'Message',
							content: (
								<div className="py-4 px-8 bg-gray-50">
									{mailType === 'notification' ? (
										<iframe srcdoc={log.body} width="100%" height="600" />
									) : (
										<Prose content={log.body} />
									)}
								</div>
							),
						},
					],
					[
						{
							title: 'Activity',
							content: <Activity log={log} />,
						},
					],
				]}
			/>
		</>
	);
};

Show.layout = page => <TenantLayout children={page} />;

export default Show;

const Activity = ({ log }) => {
	const mailType = log.uid.split('-')[0];

	const events = [...log.events];
	if (mailType !== 'notification') {
		events.unshift({
			id: 0,
			status: 'received',
			context: '',
			created_at: log.received_at,
		});
	}

	return (
		<>
			<div className="flow-root px-6 py-8 bg-gray-50">
				<ul role="list" className="-mb-8">
					{events
						.map(event => ({
							...event,
							iconColour: mailIconColours[event.status] ?? 'bg-gray-400',
							icon: mailIcons[event.status] ?? 'question',
						}))
						.reverse()
						.map((event, eventIdx) => (
							<li key={event.id}>
								<div className="relative pb-8">
									{eventIdx !== events.length - 1 ? (
										<div
											aria-hidden="true"
											className="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-500"
										/>
									) : null}
									<div className="relative flex space-x-3">
										<div>
											<span
												className={classNames(
													event.iconColour,
													'flex h-8 w-8 items-center justify-center rounded-full ring-8 ring-gray-100'
												)}
											>
												<Icon
													icon={event.icon ?? 'question'}
													type="regular"
													className="text-white text-sm"
												/>
											</span>
										</div>
										<div className="flex flex-col md:flex-row lg:flex-col xl:flex-row min-w-0 flex-1 justify-between gap-x-4 gap-y-2 pt-1.5">
											<div>
												<MailStatusDetail log={log} event={event} mailType={mailType} />
											</div>
											<div className="md:text-right lg:text-left xl:text-right text-sm whitespace-nowrap text-gray-500">
												<DateTag
													icon="pencil"
													date={event.created_at}
													format={'DATETIME_SHORT'}
												/>
											</div>
										</div>
									</div>
								</div>
							</li>
						))}
				</ul>
			</div>
		</>
	);
};

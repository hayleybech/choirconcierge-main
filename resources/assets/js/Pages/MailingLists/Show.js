import React, { useState } from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import { PageHeader2 } from '../../components/PageHeader/PageHeader';
import Breadcrumbs from '../../components/PageHeader/Breadcrumbs';
import { PageHeading } from '../../components/PageHeader/PageHeading';
import PageTopBar, { PageActionsMenu, PageTopBarTitle, PageTopNavigation } from '../../components/PageTopBar';
import ActionMenuItem from '../../components/ActionMenu/ActionMenuItem';
import Button from '../../components/inputs/Button';
import AppHead from '../../components/AppHead';
import Icon from '../../components/Icon';
import DateTag from '../../components/DateTag';
import DeleteDialog from '../../components/DeleteDialog';
import EmptyState from '../../components/EmptyState';
import useRoute from '../../hooks/useRoute';
import TrialAntiSpamNotice from './TrialAntiSpamNotice';
import SectionLayout from '../Singers/SectionLayout';

const Show = ({ list, setSidebarOpen }) => {
	const { route } = useRoute();
	const [deleteDialogIsOpen, setDeleteDialogIsOpen] = useState(false);
	const actions = [
		{ label: 'Edit', icon: 'edit', url: route('groups.edit', { group: list }), can: 'update_group' },
		{
			label: 'Delete',
			icon: 'trash',
			onClick: () => setDeleteDialogIsOpen(true),
			variant: 'danger-outline',
			can: 'delete_group',
		},
	].filter(action => list.can[action.can]);

	const list_type_labels = {
		chat: 'Chat',
		public: 'Public',
		distribution: 'Mailout',
	};

	return (
		<>
			<AppHead title={`${list.title} - Mailing Lists`} />

			<TrialAntiSpamNotice />

			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<div className="flex justify-between grow">
					<PageTopNavigation
						backUrl={route('groups.index')}
						breadcrumbs={[
							{ name: 'Communications', url: route('communications.index') },
							{ name: 'Mailing Lists', url: route('groups.index') },
						]}
					>
						<PageTopBarTitle title={list.title} />
					</PageTopNavigation>
					<PageActionsMenu>
						{actions.map(action => (
							<ActionMenuItem
								key={action.label}
								url={action.url}
								onClick={action.onClick}
								variant={action.variant}
							>
								<Icon icon={action.icon} mr />
								{action.label}
							</ActionMenuItem>
						))}
					</PageActionsMenu>
				</div>
			</PageTopBar>
			<PageHeader2>
				<div className="lg:flex lg:items-center lg:justify-between">
					<div className="flex-1 min-w-0">
						<div className="hidden lg:block">
							<Breadcrumbs
								breadcrumbs={[
									{ name: 'Communications', url: route('communications.index') },
									{ name: 'Mailing Lists', url: route('groups.index') },
									{ name: list.title, url: route('groups.show', { group: list }) },
								]}
								showLastChevron={false}
							/>
						</div>
						<PageHeading>
							<span className="flex items-center">
								<Icon icon={list.type_icon} mr />
								{list.title}
							</span>
						</PageHeading>
						<div className="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-2 gap-2 sm:gap-6 text-sm sm:items-center text-gray-500">
							<div>
								<Icon icon={list.type_icon} mr className="text-gray-400" />
								{list_type_labels[list.list_type]}
							</div>
							<div>
								<strong>{list.email.split('@')[0]}@</strong>
								<span className="text-gray-500">{list.email.split('@')[1]}</span>
							</div>
							<div className="flex items-center gap-2">
								<DateTag icon="pencil" date={list.created_at} label="Created" />
								<DateTag icon="pencil" date={list.updated_at} label="Updated" />
							</div>
						</div>
					</div>
					<div className="hidden lg:flex mt-0 lg:ml-4 gap-3">
						{actions.map(action => (
							<Button
								key={action.label}
								href={action.url}
								onClick={action.onClick}
								size="sm"
								variant={action.variant}
							>
								<Icon icon={action.icon} mr />
								{action.label}
							</Button>
						))}
					</div>
				</div>
			</PageHeader2>

			<DeleteDialog
				title="Delete Mailing Lists"
				url={route('groups.destroy', { group: list })}
				isOpen={deleteDialogIsOpen}
				setIsOpen={setDeleteDialogIsOpen}
			>
				Are you sure you want to delete this mailing list? This action cannot be undone.
			</DeleteDialog>

			<SectionLayout
				gridClassName="grid-cols-1 divide-y divide-gray-300 sm:grid sm:grid-cols-2 sm:divide-x sm:divide-y-0"
				columnClassNames={['sm:col-span-1', 'sm:col-span-1']}
				columns={[
					[
						{
							title: 'Recipients',
							content: (
								<div className="h-full overflow-y-auto relative">
									{list.recipient_ensembles?.length > 0 && (
										<div className="bg-purple-100 px-6 py-3 border-y border-purple-300">
											<h4 className="text-xs font-semibold text-purple-700 uppercase tracking-wider mb-1">
												Ensemble Filter
											</h4>
											<div className="flex flex-wrap gap-2">
												{list.recipient_ensembles.map(ensemble => (
													<span
														key={ensemble.id}
														className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-200 text-purple-800 border border-purple-400"
													>
														{ensemble.name}
													</span>
												))}
											</div>
											<p className="mt-2 text-xs text-purple-600 italic">
												Only singers in these ensembles who also match the criteria below are
												included.
											</p>
										</div>
									)}
									{list.members?.filter(m => m.memberable_type !== 'App\\Models\\Ensemble')?.length >
									0 ? (
										<ul role="list" className="relative z-0 divide-y divide-gray-200">
											{list.members
												?.filter(m => m.memberable_type !== 'App\\Models\\Ensemble')
												?.map(member => (
													<li key={member.id}>
														<div className="relative px-6 py-5 flex items-center space-x-3">
															<div className="flex items-center justify-between px-4">
																<span className="text-sm font-medium truncate">
																	{`${getTypeName(member.memberable_type)}: `}
																	{member?.memberable?.name ??
																		member?.memberable?.title ??
																		member.memberable_id}
																</span>
															</div>
														</div>
													</li>
												))}
										</ul>
									) : (
										<EmptyState
											title="No recipients"
											description="This mailing list has no base recipients selected, so it won't be able to do very much. "
											actionDescription={
												list.can['update_group']
													? 'To add recipients, edit this list, then assign a singer or an entire role, status or voice part.'
													: 'Ask your admin to finish setting up this list.'
											}
											icon="inbox-in"
											href={
												list.can['update_group'] ? route('groups.edit', { group: list }) : null
											}
											actionLabel="Edit Mailing List"
											actionIcon="edit"
										/>
									)}
								</div>
							),
						},
					],

					...(list.list_type === 'distribution'
						? [
								[
									{
										title: 'Senders',
										content: (
											<div className="h-full overflow-y-auto relative">
												{list.sender_ensembles?.length > 0 && (
													<div className="bg-purple-100 px-6 py-3 border-y border-purple-300">
														<h4 className="text-xs font-semibold text-purple-700 uppercase tracking-wider mb-1">
															Ensemble Filter
														</h4>
														<div className="flex flex-wrap gap-2">
															{list.sender_ensembles.map(ensemble => (
																<span
																	key={ensemble.id}
																	className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-200 text-purple-800 border border-purple-400"
																>
																	{ensemble.name}
																</span>
															))}
														</div>
														<p className="mt-2 text-xs text-purple-600 italic">
															Only people in these ensembles who also match the criteria
															below are permitted to send.
														</p>
													</div>
												)}
												{list.senders?.filter(s => s.sender_type !== 'App\\Models\\Ensemble')
													?.length > 0 ? (
													<ul role="list" className="relative z-0 divide-y divide-gray-200">
														{list.senders
															.filter(s => s.sender_type !== 'App\\Models\\Ensemble')
															.map(sender => (
																<li key={sender.id}>
																	<div className="relative px-6 py-5 flex items-center space-x-3">
																		<div className="flex items-center justify-between px-4">
																			<span className="text-sm font-medium truncate">
																				{`${getTypeName(sender.sender_type)}: `}
																				{sender?.sender?.name ??
																					sender?.sender?.title ??
																					sender.sender_id}
																			</span>
																		</div>
																	</div>
																</li>
															))}
													</ul>
												) : (
													<EmptyState
														title="No senders"
														description="This type of mailing list needs permitted senders to be assigned (a Chat type list just assumes the recipients are the senders). "
														actionDescription={
															list.can['update_group']
																? 'To add senders, edit this list, then assign a singer or an entire role, status or voice part.'
																: 'Ask your admin to finish setting up this list.'
														}
														icon="inbox-out"
														href={
															list.can['update_group']
																? route('groups.edit', { group: list })
																: null
														}
														actionLabel="Edit Mailing List"
														actionIcon="edit"
													/>
												)}
											</div>
										),
									},
								],
						  ]
						: []),
				]}
			/>
		</>
	);
};

Show.layout = page => <TenantLayout children={page} />;

export default Show;

const getTypeName = type => {
	const typeNames = {
		'App\\Models\\Role': 'Role',
		'App\\Models\\VoicePart': 'Voice Part',
		'App\\Models\\SingerCategory': 'Singer Category',
		'App\\Models\\User': 'Singer',
		'App\\Models\\Ensemble': 'Ensemble (Filter)',
		'App\\Enums\\SingerStatus': 'Singer Status',
	};
	return typeNames[type];
};

import React, { useState } from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import { PageHeader2 } from '../../components/PageHeader/PageHeader';
import Breadcrumbs from '../../components/PageHeader/Breadcrumbs';
import { PageHeading } from '../../components/PageHeader/PageHeading';
import PageTopBar, { PageActionsMenu, PageTopBarTitle, PageTopNavigation } from '../../components/PageTopBar';
import ActionMenuItem from '../../components/ActionMenu/ActionMenuItem';
import Button from '../../components/inputs/Button';
import Icon from '../../components/Icon';
import AppHead from '../../components/AppHead';
import RiserStackEditor from '../../components/RiserStack/RiserStackEditor';
import DateTag from '../../components/DateTag';
import DeleteDialog from '../../components/DeleteDialog';
import useRoute from '../../hooks/useRoute';
import { usePage } from '@inertiajs/react';
import Badge from '../../components/Badge';

const Show = ({ stack, setSidebarOpen }) => {
	const { route } = useRoute();
	const [deleteDialogIsOpen, setDeleteDialogIsOpen] = useState(false);
	const { tenant, can } = usePage().props;
	const actions = [
		{ label: 'Edit', icon: 'edit', url: route('stacks.edit', { stack }), can: 'update_stack' },
		{ label: 'Duplicate', icon: 'copy', url: route('stacks.clone', { stack }), can: 'create_stack' },
		{
			label: 'Delete',
			icon: 'trash',
			onClick: () => setDeleteDialogIsOpen(true),
			variant: 'danger-outline',
			can: 'delete_stack',
		},
	].filter(action => stack.can[action.can] || can[action.can]);

	return (
		<>
			<AppHead title={`${stack.title} - Riser Stacks`} />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<div className="flex justify-between grow">
					<PageTopNavigation
						backUrl={route('stacks.index')}
						breadcrumbs={[{ name: 'Riser Stacks', url: route('stacks.index') }]}
					>
						<PageTopBarTitle title={stack.title} />
					</PageTopNavigation>
					<PageActionsMenu>
						{actions.map((action, key) => (
							<ActionMenuItem
								key={key}
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
									{ name: 'Riser Stacks', url: route('stacks.index') },
									{ name: stack.title, url: route('stacks.show', { tenant, stack }) },
								]}
								showLastChevron={false}
							/>
						</div>
						<PageHeading>
							<span className="flex items-center">
								<Icon icon="people-arrows" type="solid" className="mr-2" />
								{stack.title}
							</span>
						</PageHeading>
						<div className="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-2 gap-2 sm:gap-6 text-sm sm:items-center text-gray-500">
							<span>Rows: {stack.rows}</span>
							<span>Columns: {stack.columns}</span>
							<span>Singers on front row: {stack.front_row_length}</span>
							<span>Front row on floor: {stack.front_row_on_floor ? 'Yes' : 'No'}</span>
							{stack.ensembles.length > 0 && (
								<div className="flex flex-wrap gap-1">
									{stack.ensembles.map(ensemble => (
										<Badge key={ensemble.id} colour="bg-purple-100 text-purple-800">
											{ensemble.name}
										</Badge>
									))}
								</div>
							)}
							<DateTag icon="pencil" date={stack.created_at} label="Created" />
							<DateTag icon="pencil" date={stack.updated_at} label="Updated" />
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
				title="Delete Riser Stack"
				url={route('stacks.destroy', { stack })}
				isOpen={deleteDialogIsOpen}
				setIsOpen={setDeleteDialogIsOpen}
			>
				Are you sure you want to delete this riser stack? This action cannot be undone.
			</DeleteDialog>

			<div className="w-full max-w-full overflow-x-auto">
				<RiserStackEditor
					rows={parseInt(stack.rows)}
					columns={parseInt(stack.columns)}
					spotsOnFrontRow={parseInt(stack.front_row_length)}
					frontRowOnFloor={stack.front_row_on_floor}
					singerPositions={stack.members}
					width={1000}
					height={500}
					setSelectedSinger={() => {}}
					removeSingerFromHoldingArea={() => {}}
					selectedSinger={null}
					setPositions={() => {}}
				/>
			</div>
		</>
	);
};

Show.layout = page => <TenantLayout children={page} />;

export default Show;

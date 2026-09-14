import React, { useState } from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import {
	PageHeader,
	PageHeaderActions,
	PageHeaderBreadcrumbs,
	PageHeaderContent,
	PageHeaderMeta,
	PageHeaderTitle,
} from '../../components/PageHeader/PageHeader';
import PageTopBar, { PageActionsMenu, PageTopNavigation } from '../../components/PageTopBar';
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

	const breadcrumbs = [
		{ name: 'Riser Stacks', url: route('stacks.index') },
		{ name: stack.title, url: route('stacks.show', { tenant, stack }) },
	];

	return (
		<>
			<AppHead title={`${stack.title} - Riser Stacks`} />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
					<PageTopNavigation breadcrumbs={breadcrumbs}></PageTopNavigation>
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
			</PageTopBar>
			<PageHeader>
				<PageHeaderContent>
					<PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
					<PageHeaderTitle>
						<span className="flex items-center">
							<Icon icon="people-arrows" type="solid" className="mr-2" />
							{stack.title}
						</span>
					</PageHeaderTitle>
					<PageHeaderMeta>
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
					</PageHeaderMeta>
				</PageHeaderContent>
				<PageHeaderActions>
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
				</PageHeaderActions>
			</PageHeader>
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

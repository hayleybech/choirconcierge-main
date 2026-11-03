import React, { useState } from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import {
	PageHeader,
	PageHeaderActions,
	PageHeaderBreadcrumbs,
	PageHeaderContent,
	PageHeaderMeta,
	PageHeaderTitle,
	PageHeaderTitleRow,
} from '../../components/PageHeader/PageHeader';
import PageTopBar, { PageActionsMenu, PageTopNavigation } from '../../components/PageTopBar';
import ActionMenuItem from '../../components/ActionMenu/ActionMenuItem';
import Button from '../../components/inputs/ButtonV2';
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
		{ label: 'Edit', icon: 'edit', href: route('stacks.edit', { stack }), can: 'update_stack', weight: 'outline' },
		{ label: 'Duplicate', icon: 'copy', href: route('stacks.clone', { stack }), can: 'create_stack' },
		{
			label: 'Delete',
			icon: 'trash',
			onClick: () => setDeleteDialogIsOpen(true),
			intent: 'danger',
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
						<ActionMenuItem key={key} url={action.href} onClick={action.onClick} intent={action.intent}>
							<Icon icon={action.icon} mr />
							{action.label}
						</ActionMenuItem>
					))}
				</PageActionsMenu>
			</PageTopBar>
			<PageHeader>
				<PageHeaderContent>
					<PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
					<PageHeaderTitleRow>
						<PageHeaderTitle>
							<span className="flex items-center">
								<Icon icon="people-arrows" type="solid" className="mr-2" />
								{stack.title}
							</span>
						</PageHeaderTitle>
						<PageHeaderActions>
							{actions.map(action => (
								<Button
									key={action.label}
									{...action}
									size="md"
									weight={action.weight ?? 'clear'}
								>
									<Icon icon={action.icon} mr />
									{action.label}
								</Button>
							))}
						</PageHeaderActions>
					</PageHeaderTitleRow>
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

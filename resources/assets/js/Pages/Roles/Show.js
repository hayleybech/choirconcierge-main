import React, { useState } from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import {
	PageHeader,
	PageHeaderActions,
	PageHeaderBreadcrumbs,
	PageHeaderContent,
	PageHeaderTitle,
} from '../../components/PageHeader/PageHeader';
import PageTopBar, { PageActionsMenu, PageTopNavigation } from '../../components/PageTopBar';
import ActionMenuItem from '../../components/ActionMenu/ActionMenuItem';
import Button from '../../components/inputs/Button';
import AppHead from '../../components/AppHead';
import Icon from '../../components/Icon';
import { modelsAndAbilities } from './modelsAndAbilities';
import classNames from '../../classNames';
import DeleteDialog from '../../components/DeleteDialog';
import FormWrapper from '../../components/FormWrapper';
import useRoute from '../../hooks/useRoute';
import { usePage } from '@inertiajs/react';

const Show = ({ role, setSidebarOpen }) => {
	const { route } = useRoute();

	const page = usePage();

	const [deleteDialogIsOpen, setDeleteDialogIsOpen] = useState(false);
	const actions = [
		{
			label: 'Edit',
			icon: 'edit',
			url: route('roles.edit', { role }),
			can: role.can.update_role || page.props.can.update_role,
		},
		{
			label: 'Duplicate',
			icon: 'copy',
			url: route('roles.clone', role),
			can: role.can.create_role || page.props.can.create_role,
		},
		{
			label: 'Delete',
			icon: 'trash',
			onClick: () => setDeleteDialogIsOpen(true),
			variant: 'danger-outline',
			can: !['User', 'Admin'].includes(role.name) && (role.can.delete_role || page.props.can.delete_role),
		},
	].filter(action => action.can);

	const breadcrumbs = [
		{ name: 'Singers', url: route('singers.index') },
		{ name: 'Roles', url: route('roles.index') },
		{ name: role.name, url: route('roles.show', { role }) },
	];

	return (
		<>
			<AppHead title={`${role.name} - Roles`} />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
					<PageTopNavigation breadcrumbs={breadcrumbs}></PageTopNavigation>
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
			</PageTopBar>
			<PageHeader>
				<PageHeaderContent>
					<PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
					<PageHeaderTitle>
						<Icon icon="user-tag" type="solid" className="mr-2" />
						{role.name}
					</PageHeaderTitle>
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
				title="Delete Role"
				url={route('roles.destroy', { role })}
				isOpen={deleteDialogIsOpen}
				setIsOpen={setDeleteDialogIsOpen}
			>
				Are you sure you want to delete this role? This will affect all users in this role and could break your
				site! This action cannot be undone.
			</DeleteDialog>

			<FormWrapper>
				<table className="w-full">
					<thead>
						<tr className="text-center md:text-left text-gray-900">
							<th className="py-4 text-left">Model</th>
							<th className="py-4">View</th>
							<th className="py-4">Create</th>
							<th className="py-4">Update</th>
							<th className="py-4">Delete</th>
						</tr>
					</thead>
					<tbody>
						{objectMap(modelsAndAbilities, (modelKey, { label: modelName, abilities }) => (
							<tr key={modelKey}>
								<th className="py-4 text-left">
									<span className="font-bold text-gray-700">{modelName}</span>
								</th>
								{abilities.map(abilityKey => (
									<td
										key={`${modelKey}_${abilityKey}`}
										className={classNames(
											'py-4',
											role.abilities.includes(`${modelKey}_${abilityKey}`)
												? 'text-emerald-500'
												: 'text-gray-500'
										)}
									>
										<div className="flex flex-col items-center md:flex-row">
											<Icon
												icon={
													role.abilities.includes(`${modelKey}_${abilityKey}`)
														? 'check'
														: 'times'
												}
												mr
											/>
											{abilityKey[0].toUpperCase() + abilityKey.substring(1)}
										</div>
									</td>
								))}
							</tr>
						))}
					</tbody>
				</table>
			</FormWrapper>
		</>
	);
};

Show.layout = page => <TenantLayout children={page} />;

export default Show;

function objectMap(object, fn) {
	return Object.keys(object).map(key => fn(key, object[key]));
}

import React from 'react'
import TenantLayout from "../../Layouts/TenantLayout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import RoleForm from "./RoleForm";
import useRoute from "../../hooks/useRoute";

const Edit = ({ role }) => {
	const { route } = useRoute();

	return (
		<>
			<AppHead title={`Edit - ${role.name}`} />
			<PageHeader
				title="Edit Singer Role"
				icon="user-tag"
				breadcrumbs={[
					{ name: 'Dashboard', url: route('dash')},
					{ name: 'Singers', url: route('singers.index')},
					{ name: 'Roles', url: route('roles.index')},
					{ name: role.name, url: route('roles.show', {role}) },
					{ name: 'Edit', url: route('roles.edit', {role})},
				]}
			/>

			<RoleForm role={role} />
		</>
	);
}

Edit.layout = page => <TenantLayout children={page} />

export default Edit;
import React, {useState} from 'react'
import TenantLayout from "../../Layouts/TenantLayout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import Icon from "../../components/Icon";
import {modelsAndAbilities} from "./modelsAndAbilities";
import classNames from "../../classNames";
import DeleteDialog from "../../components/DeleteDialog";
import FormWrapper from "../../components/FormWrapper";
import useRoute from "../../hooks/useRoute";

const Show = ({ role }) => {
    const { route } = useRoute();

    const [deleteDialogIsOpen, setDeleteDialogIsOpen] = useState(false);

    return (
        <>
            <AppHead title={`${role.name} - Roles`} />
            <PageHeader
                title={role.name}
                breadcrumbs={[
                    { name: 'Dashboard', url: route('dash')},
                    { name: 'Singers', url: route('singers.index')},
                    { name: 'Roles', url: route('roles.index')},
                    { name: role.name, url: route('roles.show', {role}) },
                ]}
                actions={[
                    { label: 'Edit', icon: 'edit', url: route('roles.edit', {role}), can: 'update_role' },
                    ['User', 'Admin'].includes(role.name)
                      ? null
                      :  { label: 'Delete', icon: 'trash', onClick: () => setDeleteDialogIsOpen(true), variant: 'danger-outline', can: 'delete_role' },
                ].filter(action => !!action && (action.can ? role.can[action.can] : true))}
            />

            <DeleteDialog
                title="Delete Role"
                url={route('roles.destroy', {role})}
                isOpen={deleteDialogIsOpen}
                setIsOpen={setDeleteDialogIsOpen}
            >
                Are you sure you want to delete this role?
                This will affect all users in this role and could break your site!
                This action cannot be undone.
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
                            {(abilities.map((abilityKey) => (
                                <td
                                    key={`${modelKey}_${abilityKey}`}
                                    className={classNames('py-4',
                                        role.abilities.includes(`${modelKey}_${abilityKey}`) ? 'text-emerald-500' : 'text-gray-500',
                                    )}
                                >
                                  <div className="flex flex-col items-center md:flex-row">
                                    <Icon icon={role.abilities.includes(`${modelKey}_${abilityKey}`) ? 'check' : 'times'} mr />
                                    {abilityKey[0].toUpperCase() + abilityKey.substring(1)}
                                  </div>
                                </td>
                            )))}
                        </tr>
                    ))}
                    </tbody>
                </table>
            </FormWrapper>
        </>
    );
}

Show.layout = page => <TenantLayout children={page} />

export default Show;

function objectMap(object, fn) {
    return Object.keys(object).map((key) => fn(key, object[key]));
}
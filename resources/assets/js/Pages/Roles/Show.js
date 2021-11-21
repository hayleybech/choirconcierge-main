import React, {useState} from 'react'
import Layout from "../../Layouts/Layout";
import PageHeader from "../../components/PageHeader";
import Dialog from "../../components/Dialog";
import AppHead from "../../components/AppHead";
import Icon from "../../components/Icon";
import {modelsAndAbilities} from "./modelsAndAbilities";
import classNames from "../../classNames";

const Show = ({ role }) => {
    const [deleteDialogIsOpen, setDeleteDialogIsOpen] = useState(false);

    return (
        <>
            <AppHead title={`${role.name} - Roles`} />
            <PageHeader
                title={role.name}
                breadcrumbs={[
                    { name: 'Dashboard', url: route('dash')},
                    { name: 'Roles', url: route('roles.index')},
                    { name: role.name, url: route('roles.show', role) },
                ]}
                actions={[
                    { label: 'Edit', icon: 'edit', url: route('roles.edit', role), can: 'update_role' },
                    { label: 'Delete', icon: 'trash', onClick: () => setDeleteDialogIsOpen(true), variant: 'danger-outline', can: 'delete_role' },
                ].filter(action => action.can ? role.can[action.can] : true)}
            />

            <DeleteRoleDialog isOpen={deleteDialogIsOpen} setIsOpen={setDeleteDialogIsOpen} role={role} />

            <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">

                <table className="w-full">
                    <thead>
                    <tr className="text-left">
                        <th className="py-4">Model</th>
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
                                <span className="font-bold">{modelName}</span>
                            </th>
                            {(abilities.map((abilityKey) => (
                                <td
                                    key={`${modelKey}_${abilityKey}`}
                                    className={classNames('py-4',
                                        role.abilities.includes(`${modelKey}_${abilityKey}`) ? 'text-green-500' : 'text-gray-500',
                                    )}
                                >
                                    <Icon icon={role.abilities.includes(`${modelKey}_${abilityKey}`) ? 'check' : 'times'} mr />
                                    {abilityKey[0].toUpperCase() + abilityKey.substring(1)}
                                </td>
                            )))}
                        </tr>
                    ))}
                    </tbody>
                </table>
            </div>
        </>
    );
}

Show.layout = page => <Layout children={page} />

export default Show;

const DeleteRoleDialog = ({ isOpen, setIsOpen, role }) => (
    <Dialog
        title="Delete Role"
        okLabel="Delete"
        okUrl={route('roles.destroy', role)}
        okVariant="danger-solid"
        okMethod="delete"
        isOpen={isOpen}
        setIsOpen={setIsOpen}
    >
        <p>
            Are you sure you want to delete this role?
            This will affect all users in this role and could break your site!
            This action cannot be undone.
        </p>
    </Dialog>
);

function objectMap(object, fn) {
    return Object.keys(object).map((key) => fn(key, object[key]));
}
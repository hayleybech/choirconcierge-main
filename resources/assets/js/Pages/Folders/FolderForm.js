import React from 'react';
import {useForm} from "@inertiajs/react";
import FormSection from "../../components/FormSection";
import Label from "../../components/inputs/Label";
import TextInput from "../../components/inputs/TextInput";
import CheckboxGroup from "../../components/inputs/CheckboxGroup";
import Help from "../../components/inputs/Help";
import Error from "../../components/inputs/Error";
import ButtonLink from "../../components/inputs/ButtonLink";
import Button from "../../components/inputs/Button";
import FormFooter from "../../components/FormFooter";
import Form from "../../components/Form";
import FormWrapper from "../../components/FormWrapper";
import useRoute from "../../hooks/useRoute";
import SingerSelect from "../../components/inputs/SingerSelect";

const FolderForm = ({ folder, ensembles, roles, voiceParts, singerStatuses }) => {
    const { route } = useRoute();

    const { data, setData, post, put, processing, errors } = useForm({
        title: folder?.title ?? '',
        ensembles: folder?.ensembles.map(ensemble => ensemble.id) ?? [],
        viewer_users: folder?.viewer_users?.map(user => user.id) ?? [],
        viewer_roles: folder?.viewer_roles?.map(role => role.id) ?? [],
        viewer_voice_parts: folder?.viewer_voice_parts?.map(part => part.id) ?? [],
        viewer_singer_statuses: folder?.viewer_singer_statuses?.map(cat => cat.viewer_id) ?? [],
        editor_users: folder?.editor_users?.map(user => user.id) ?? [],
        editor_roles: folder?.editor_roles?.map(role => role.id) ?? [],
        editor_voice_parts: folder?.editor_voice_parts?.map(part => part.id) ?? [],
        editor_singer_statuses: folder?.editor_singer_statuses?.map(cat => cat.editor_id) ?? [],
    });

    function submit(e) {
        e.preventDefault();
        folder ? put(route('folders.update', { folder })) : post(route('folders.store'));
    }

    return (
        <FormWrapper>
            <Form onSubmit={submit}>

                <FormSection title="Folder Details">
                    <div className="sm:col-span-6">
                        <Label label="Title" forInput="title" />
                        <TextInput
                            name="title"
                            value={data.title}
                            updateFn={value => setData('title', value)}
                            hasErrors={ !! errors['title'] }
                        />
                        {errors.title && <Error>{errors.title}</Error>}
                    </div>

					{ensembles.length > 1 && (
							<fieldset className="bg-purple-100 border border-purple-700 p-4 rounded sm:col-span-6">
								<legend className="text-base font-medium text-purple-600 contents">Ensemble Filter</legend>
								<Help>
									<div className="text-purple-700">Optional: If selected, only singers in these ensembles who also match the criteria below will be able to view/edit this folder. </div>
								</Help>
								<CheckboxGroup
									name="ensembles"
									options={ensembles.map(ensemble => ({ id: ensemble.id, name: ensemble.name }))}
									value={data.ensembles}
									updateFn={value => setData('ensembles', value)}
								/>
								{errors.ensembles && <Error>{errors.ensembles}</Error>}
							</fieldset>
					)}
                </FormSection>

                <FormSection title="Viewing Permissions" description={`Specify who can view this folder. If none are selected, all members ${ensembles.length > 1 ? '(within any selected ensembles) ' : ''}can view. `}>
                    <div className="sm:col-span-6">
                        <Label label="Specific Singers" />
                        <SingerSelect
                            multiple
                            defaultValue={folder?.viewer_users?.map(user => ({
                                value: user.id,
                                label: user.name,
                                name: user.name,
                                avatarUrl: user.avatar_url,
                                email: user.email,
                                roles: user.membership.roles,
                            })) ?? null}
                            updateFn={value => setData('viewer_users', value)}
                        />
                        {errors.viewer_users && <Error>{errors.viewer_users}</Error>}
                    </div>

                    <fieldset className="sm:col-span-6">
                        <legend className="text-base font-medium text-gray-900">Roles</legend>
                        <CheckboxGroup
                            name="viewer_roles"
                            options={roles}
                            value={data.viewer_roles}
                            updateFn={value => setData('viewer_roles', value)}
                        />
                        {errors.viewer_roles && <Error>{errors.viewer_roles}</Error>}
                    </fieldset>

                    <fieldset className="sm:col-span-6">
                        <legend className="text-base font-medium text-gray-900">Voice Parts</legend>
                        <CheckboxGroup
                            name="viewer_voice_parts"
                            options={voiceParts.map(part => ({ id: part.id, name: part.title }))}
                            value={data.viewer_voice_parts}
                            updateFn={value => setData('viewer_voice_parts', value)}
                        />
                        {errors.viewer_voice_parts && <Error>{errors.viewer_voice_parts}</Error>}
                    </fieldset>

                    <fieldset className="sm:col-span-6">
                        <legend className="text-base font-medium text-gray-900">Singer Statuses</legend>
                        <CheckboxGroup
                            name="viewer_singer_statuses"
                            options={singerStatuses}
                            value={data.viewer_singer_statuses}
                            updateFn={value => setData('viewer_singer_statuses', value)}
                        />
                        {errors.viewer_singer_statuses && <Error>{errors.viewer_singer_statuses}</Error>}
                    </fieldset>
                </FormSection>

                <FormSection title="Editing Permissions" description="Specify who can edit this folder. If none are selected, only admins or users with the 'update_folder' permission can edit. ">
                    <div className="sm:col-span-6">
                        <Label label="Specific Singers" />
                        <SingerSelect
                            multiple
                            defaultValue={folder?.editor_users?.map(user => ({
                                value: user.id,
                                label: user.name,
                                name: user.name,
                                avatarUrl: user.avatar_url,
                                email: user.email,
                                roles: user.membership.roles,
                            })) ?? null}
                            updateFn={value => setData('editor_users', value)}
                        />
                        {errors.editor_users && <Error>{errors.editor_users}</Error>}
                    </div>

                    <fieldset className="sm:col-span-6">
                        <legend className="text-base font-medium text-gray-900">Roles</legend>
                        <CheckboxGroup
                            name="editor_roles"
                            options={roles}
                            value={data.editor_roles}
                            updateFn={value => setData('editor_roles', value)}
                        />
                        {errors.editor_roles && <Error>{errors.editor_roles}</Error>}
                    </fieldset>

                    <fieldset className="sm:col-span-6">
                        <legend className="text-base font-medium text-gray-900">Voice Parts</legend>
                        <CheckboxGroup
                            name="editor_voice_parts"
                            options={voiceParts.map(part => ({ id: part.id, name: part.title }))}
                            value={data.editor_voice_parts}
                            updateFn={value => setData('editor_voice_parts', value)}
                        />
                        {errors.editor_voice_parts && <Error>{errors.editor_voice_parts}</Error>}
                    </fieldset>

                    <fieldset className="sm:col-span-6">
                        <legend className="text-base font-medium text-gray-900">Singer Statuses</legend>
                        <CheckboxGroup
                            name="editor_singer_statuses"
                            options={singerStatuses}
                            value={data.editor_singer_statuses}
                            updateFn={value => setData('editor_singer_statuses', value)}
                        />
                        {errors.editor_singer_statuses && <Error>{errors.editor_singer_statuses}</Error>}
                    </fieldset>
                </FormSection>

                <FormFooter>
                    <ButtonLink href={route('folders.index')}>Cancel</ButtonLink>
                    <Button variant="primary" type="submit" className="ml-3" disabled={processing}>Save</Button>
                </FormFooter>
            </Form>
        </FormWrapper>
    );
}

export default FolderForm;
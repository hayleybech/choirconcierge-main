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

const FolderForm = ({ folder, ensembles = [] }) => {
    const { route } = useRoute();

    const { data, setData, post, put, processing, errors } = useForm({
        title: folder?.title ?? '',
        ensembles: folder?.ensembles.map(ensemble => ensemble.id) ?? [],
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
                        <div className="sm:col-span-6">
                            <Label label="Ensembles" forInput="ensembles" />
                            <Help>Sub-groups that may view and access this folder.</Help>
                            <CheckboxGroup
                                name="ensembles"
                                options={ensembles.map(ensemble => ({ id: ensemble.id, name: ensemble.name }))}
                                value={data.ensembles}
                                updateFn={value => setData('ensembles', value)}
                            />
                        </div>
                    )}
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
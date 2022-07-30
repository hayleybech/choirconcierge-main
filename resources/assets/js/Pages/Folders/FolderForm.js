import React from 'react';
import {useForm} from "@inertiajs/inertia-react";
import FormSection from "../../components/FormSection";
import Label from "../../components/inputs/Label";
import TextInput from "../../components/inputs/TextInput";
import Error from "../../components/inputs/Error";
import ButtonLink from "../../components/inputs/ButtonLink";
import Button from "../../components/inputs/Button";
import FormFooter from "../../components/FormFooter";
import Form from "../../components/Form";
import FormWrapper from "../../components/FormWrapper";

const FolderForm = () => {
    const { data, setData, post, processing, errors } = useForm({
        title: '',
    });

    function submit(e) {
        e.preventDefault();
        post(route('folders.store'));
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
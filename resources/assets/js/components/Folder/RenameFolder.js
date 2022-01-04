import React from 'react';
import {useForm} from "@inertiajs/inertia-react";
import Form from "../Form";
import FormSection from "../FormSection";
import Label from "../inputs/Label";
import TextInput from "../inputs/TextInput";
import Error from "../inputs/Error";
import FormFooter from "../FormFooter";
import ButtonLink from "../inputs/ButtonLink";
import Button from "../inputs/Button";

const RenameFolder = ({ folder }) => {
    const {data, setData, put, processing, errors} = useForm({
        title: folder.title,
    });

    function submit(e) {
        e.preventDefault();
        put(route('folders.update', folder));
    }

    return (
        <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <Form onSubmit={submit}>

                <FormSection title="Folder Details">
                    <div className="sm:col-span-6">
                        <Label label="Title" forInput="title"/>
                        <TextInput
                            name="title"
                            value={data.title}
                            updateFn={value => setData('title', value)}
                            hasErrors={!!errors['title']}
                        />
                        {errors.title && <Error>{errors.title}</Error>}
                    </div>
                </FormSection>

                <FormFooter>
                    <ButtonLink href={route('folders.index')}>Cancel</ButtonLink>
                    <Button variant="primary" type="submit" className="ml-3" disabled={processing}>Save</Button>
                </FormFooter>
            </Form>
        </div>
    );
};

export default RenameFolder;
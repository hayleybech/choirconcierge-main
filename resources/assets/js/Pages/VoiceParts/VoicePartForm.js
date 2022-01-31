import React from 'react';
import {useForm} from "@inertiajs/inertia-react";
import FormSection from "../../components/FormSection";
import Label from "../../components/inputs/Label";
import TextInput from "../../components/inputs/TextInput";
import Error from "../../components/inputs/Error";
import ButtonLink from "../../components/inputs/ButtonLink";
import Button from "../../components/inputs/Button";
import Form from "../../components/Form";
import FormFooter from "../../components/FormFooter";

const VoicePartForm = ({ voicePart }) => {
    const { data, setData, post, put, processing, errors } = useForm({
        title: voicePart?.title ?? '',
        colour: voicePart?.colour ?? '',
    });

    function submit(e) {
        e.preventDefault();
        voicePart ? put(route('voice-parts.update', voicePart)) : post(route('voice-parts.store'));
    }

    return (
        <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
            <Form onSubmit={submit}>

                <FormSection title="Voice Part Details">
                    <div className="sm:col-span-6">
                        <Label label="Voice Part Title" forInput="title" />
                        <TextInput name="title" value={data.title} updateFn={value => setData('title', value)} hasErrors={ !! errors['title'] } />
                        {errors.title && <Error>{errors.title}</Error>}
                    </div>

                    <div className="sm:col-span-6">
                        <Label label="Colour" forInput="colour" />
                        <TextInput name="colour" value={data.colour} updateFn={value => setData('colour', value)} hasErrors={ !! errors['colour'] } />
                        {errors.colour && <Error>{errors.colour}</Error>}
                    </div>

                </FormSection>

                <FormFooter>
                    <ButtonLink href={route('voice-parts.index')}>Cancel</ButtonLink>
                    <Button variant="primary" type="submit" className="ml-3" disabled={processing}>Save</Button>
                </FormFooter>
            </Form>
        </div>
    );
}

export default VoicePartForm;
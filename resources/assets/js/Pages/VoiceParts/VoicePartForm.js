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
import FormWrapper from "../../components/FormWrapper";
import ColourPicker from "../../components/inputs/ColourPicker";
import useRoute from "../../hooks/useRoute";

const VoicePartForm = ({ voicePart }) => {
    const { route } = useRoute();

    const { data, setData, post, put, processing, errors } = useForm({
        title: voicePart?.title ?? '',
        colour: voicePart?.colour ?? '',
    });

    function submit(e) {
        e.preventDefault();
        voicePart ? put(route('voice-parts.update', {voice_part: voicePart})) : post(route('voice-parts.store'));
    }

    return (
        <FormWrapper>
            <Form onSubmit={submit}>

                <FormSection title="Voice Part Details">
                    <div className="sm:col-span-6">
                        <Label label="Voice Part Title" forInput="title" />
                        <TextInput name="title" value={data.title} updateFn={value => setData('title', value)} hasErrors={ !! errors['title'] } />
                        {errors.title && <Error>{errors.title}</Error>}
                    </div>

                    <div className="sm:col-span-6">
                        <Label label="Colour" forInput="colour" />
                        <ColourPicker value={data.colour} updateFn={value => setData('colour', value)} />
                        {errors.colour && <Error>{errors.colour}</Error>}
                    </div>

                </FormSection>

                <FormFooter>
                    <ButtonLink href={route('voice-parts.index')}>Cancel</ButtonLink>
                    <Button variant="primary" type="submit" className="ml-3" disabled={processing}>Save</Button>
                </FormFooter>
            </Form>
        </FormWrapper>
    );
}

export default VoicePartForm;
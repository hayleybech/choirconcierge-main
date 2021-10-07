import React from 'react';
import {useForm} from "@inertiajs/inertia-react";
import FormSection from "../../components/FormSection";
import Label from "../../components/inputs/Label";
import TextInput from "../../components/inputs/TextInput";
import Error from "../../components/inputs/Error";
import CheckboxGroup from "../../components/inputs/CheckboxGroup";
import RadioGroup from "../../components/inputs/RadioGroup";
import Help from "../../components/inputs/Help";
import Select from "../../components/inputs/Select";
import CheckboxInput from "../../components/inputs/CheckboxInput";
import ButtonLink from "../../components/inputs/ButtonLink";
import Button from "../../components/inputs/Button";

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
        <div className="bg-gray-50">

            <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <form className="space-y-8 divide-y divide-gray-200" onSubmit={submit}>

                    <div className="space-y-8 divide-y divide-gray-200">

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

                    </div>

                    <div className="pt-5">
                        <div className="flex justify-end">
                            <ButtonLink href={route('voice-parts.index')}>Cancel</ButtonLink>
                            <Button variant="primary" type="submit" className="ml-3" disabled={processing}>Save</Button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    );
}

export default VoicePartForm;
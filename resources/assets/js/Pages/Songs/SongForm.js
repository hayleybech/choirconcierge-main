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
import ButtonLink from "../../components/inputs/ButtonLink";
import Button from "../../components/inputs/Button";
import Form from "../../components/Form";
import FormFooter from "../../components/FormFooter";
import CheckboxWithLabel from "../../components/inputs/CheckboxWithLabel";
import SongStatus from "../../SongStatus";
import RichTextInput from "../../components/inputs/RichTextInput";
import FormWrapper from "../../components/FormWrapper";

const SongForm = ({ categories, statuses, pitches, song}) => {
    const { data, setData, post, put, processing, errors } = useForm({
        title: song?.title ?? '',
        description: song?.description ?? '',
        categories: song?.categories.map(category => category.id) ?? [],
        status: song?.status.id ?? null,
        pitch_blown: song?.pitch_blown ?? 0,
        show_for_prospects: song?.show_for_prospects ?? false,
        send_notification: !song,
    });

    function submit(e) {
        e.preventDefault();
        song ? put(route('songs.update', song)) : post(route('songs.store'));
    }

    return (
        <FormWrapper>
            <Form onSubmit={submit}>

                <FormSection title="Song Details">
                    <div className="sm:col-span-6">
                        <Label label="Song Title" forInput="title" />
                        <TextInput name="title" value={data.title} updateFn={value => setData('title', value)} hasErrors={ !! errors['title'] } />
                        {errors.title && <Error>{errors.title}</Error>}
                    </div>

                    <div className="sm:col-span-6">
                        <Label label="Description" forInput="description" />
                        <RichTextInput value={data.description} updateFn={value => setData('description', value)} />
                        {errors.description && <Error>{errors.description}</Error>}
                    </div>

                    <div className="sm:col-span-6">
                        <Label label="Category" forInput="categories" />
                        <CheckboxGroup
                            name="categories"
                            options={categories.map(category => ({ id: category.id, name: category.title }))}
                            value={data.categories}
                            updateFn={value => setData('categories', value)}
                        />
                    </div>

                    <div className="sm:col-span-6">
                        <RadioGroup
                            label={<Label label="Song Status" />}
                            options={statuses.map(status => ({
                                id: status.id,
                                name: SongStatus.statuses[status.slug].title,
                                textColour: SongStatus.statuses[status.slug].textColour,
                                icon: SongStatus.statuses[status.slug].icon,
                            }))}
                            selected={data.status}
                            setSelected={value => setData('status', value)}
                        />
                        <Help>Songs are hidden from general members when they are "Pending".</Help>
                        {errors.status && <Error>{errors.status}</Error>}
                    </div>

                    <div className="sm:col-span-6">
                        <CheckboxWithLabel
                            label="Audition Song (Show for prospects)"
                            id="show_for_prospects"
                            name="show_for_prospects"
                            value={false}
                            checked={data.show_for_prospects}
                            onChange={e => setData('show_for_prospects', e.target.checked)}
                            className="mr-8 mb-4"
                        />
                    </div>


                    <div className="sm:col-span-6">
                        <Label label="Pitch Blown" forInput="pitch_blown" />
                        <Select
                            name="pitch_blown"
                            options={pitches.map((pitch, key) => ({ key: key, label: pitch}))}
                            value={data.pitch_blown}
                            updateFn={value => setData('pitch_blown', value)}
                        />
                    </div>

                </FormSection>

                <FormSection title="Notifications">
                    <CheckboxWithLabel
                        label={`Send ${song ? '"Song Updated"' : '"New Song"'} notification to singers?`}
                        id="send_notification"
                        name="send_notification"
                        value={true}
                        checked={data.send_notification}
                        onChange={e => setData('send_notification', e.target.checked)}
                        className="mr-8 mb-4"
                    />
                </FormSection>

                <FormFooter>
                    <ButtonLink href={song ? route('songs.show', song) : route('songs.index')}>Cancel</ButtonLink>
                    <Button variant="primary" type="submit" className="ml-3" disabled={processing}>Save</Button>
                </FormFooter>
            </Form>
        </FormWrapper>
    );
}

export default SongForm;
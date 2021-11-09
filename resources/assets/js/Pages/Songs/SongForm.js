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

const SongForm = ({ categories, statuses, pitches, song}) => {
    const { data, setData, post, put, processing, errors } = useForm({
        title: song?.title ?? '',
        categories: song?.categories.map(category => category.id) ?? [],
        status: song?.status.id ?? null,
        pitch_blown: song?.pitch_blown ?? 0,
        send_notification: true,
    });

    function submit(e) {
        e.preventDefault();
        song ? put(route('songs.update', song)) : post(route('songs.store'));
    }

    return (
        <div className="bg-gray-50">

            <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <form className="space-y-8 divide-y divide-gray-200" onSubmit={submit}>

                    <div className="space-y-8 divide-y divide-gray-200">

                        <FormSection title="Song Details">
                            <div className="sm:col-span-6">
                                <Label label="Song Title" forInput="title" />
                                <TextInput name="title" value={data.title} updateFn={value => setData('title', value)} hasErrors={ !! errors['title'] } />
                                {errors.title && <Error>{errors.title}</Error>}
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
                                    options={statuses.map(status => ({ ...status, name: status.title, icon: 'circle'}))}
                                    selected={data.status}
                                    setSelected={value => setData('status', value)}
                                />
                                <Help>Songs are hidden from general members when they are "Pending".</Help>
                                {errors.status && <Error>{errors.status}</Error>}
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
                            <div className="relative flex items-start mr-8 mb-4 sm:col-span-6">
                                <div className="flex items-center h-5">
                                    <CheckboxInput
                                        id="send_notification"
                                        name="send_notification"
                                        value={true}
                                        checked={data.send_notification}
                                        onChange={e => setData('send_notification', e.target.checked)}
                                    />
                                </div>
                                <div className="ml-3 text-sm">
                                    <label htmlFor="send_notification" className="font-medium text-gray-700">
                                        Send {song ? '"Song Updated"' : '"New Song"'} notification to singers?
                                    </label>
                                </div>
                            </div>
                        </FormSection>

                    </div>

                    <div className="pt-5">
                        <div className="flex justify-end">
                            <ButtonLink href={song ? route('songs.show', song) : route('songs.index')}>Cancel</ButtonLink>
                            <Button variant="primary" type="submit" className="ml-3" disabled={processing}>Save</Button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    );
}

export default SongForm;
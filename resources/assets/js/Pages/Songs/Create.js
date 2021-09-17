import React from 'react'
import Layout from "../../Layouts/Layout";
import {useForm} from "@inertiajs/inertia-react";
import Label from "../../components/inputs/Label";
import TextInput from "../../components/inputs/TextInput";
import Error from "../../components/inputs/Error";
import Select from "../../components/inputs/Select";
import FormSection from "../../components/FormSection";
import Button from "../../components/inputs/Button";
import ButtonLink from "../../components/inputs/ButtonLink";
import CheckboxGroup from "../../components/inputs/CheckboxGroup";
import PageHeader from "../../components/PageHeader";
import RadioGroup from "../../components/inputs/RadioGroup";
import CheckboxInput from "../../components/inputs/CheckboxInput";
import Help from "../../components/inputs/Help";

const Create = ({ categories, statuses, pitches }) => {
    const { data, setData, post, processing, errors } = useForm({
        title: '',
        categories: [],
        status: null,
        pitch_blown: 0,
        send_notification: true,
    });

    function submit(e) {
        e.preventDefault();
        post(route('songs.store'));
    }

    return (
        <>
            <PageHeader
                title={'Create Song'}
                icon="fa-list-music"
                breadcrumbs={[
                    { name: 'Dashboard', url: route('dash')},
                    { name: 'Songs', url: route('songs.index')},
                    { name: 'Create', url: route('songs.create')},
                ]}
            />

            <div className="bg-gray-50">

                <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <form className="space-y-8 divide-y divide-gray-200" onSubmit={submit}>

                        <div className="space-y-8 divide-y divide-gray-200">

                            <FormSection title="Song Details">
                                <div className="sm:col-span-6">
                                    <Label label="Song Title" forInput="title" />
                                    <TextInput name="title" autoComplete="given-name" value={data.title} updateFn={value => setData('title', value)} hasErrors={ !! errors['title'] } />
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
                                        label="Select a song status"
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

                                <fieldset className="mt-6 sm:col-span-6">
                                    <legend className="text-base font-medium text-gray-900">Notifications</legend>
                                    <div className="relative flex items-start mr-8 mb-4">
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
                                                Send "New Song" notification to singers?
                                            </label>
                                        </div>
                                    </div>
                                </fieldset>

                            </FormSection>

                        </div>

                        <div className="pt-5">
                            <div className="flex justify-end">
                                <ButtonLink href={route('songs.index')}>Cancel</ButtonLink>
                                <Button variant="primary" type="submit" className="ml-3" disabled={processing}>Save</Button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </>
    );
}

Create.layout = page => <Layout children={page} title="Songs" />

export default Create;
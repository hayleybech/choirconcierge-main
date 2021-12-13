import React from 'react';
import Label from "../inputs/Label";
import TextInput from "../inputs/TextInput";
import {useForm} from "@inertiajs/inertia-react";
import Error from "../inputs/Error";
import Button from "../inputs/Button";
import CheckboxGroup from "../inputs/CheckboxGroup";
import RadioGroup from "../inputs/RadioGroup";
import SectionTitle from "../SectionTitle";
import SectionHeader from "../SectionHeader";
import Icon from "../Icon";

const EventFilters = ({ eventTypes, onClose }) => {
    const params = new URLSearchParams(location.search);

    const { data, setData, get, processing, errors, transform } = useForm({
        title: params.get('filter[title]') ?? '',
        'type.id': params.getAll('filter[type.id][]').map(value => parseInt(value)) ?? [],
        date: params.get('filter[date]') ??'upcoming',
    });

    function submit(e) {
        e.preventDefault();

        get(route('events.index'));
    }

    transform((data) => ({
        filter: {
            ...data,
            date: data.date === 'all' ? null : data.date,
        }
    }));

    return (
        <form onSubmit={submit}>
            <div className="bg-white p-5 flex flex-col items-stretch space-y-4 border-b border-gray-300">
                <SectionHeader>
                    <SectionTitle>Filter</SectionTitle>

                    <Button onClick={onClose} variant="clear" size="sm"><Icon icon="times" /></Button>
                </SectionHeader>

                <div className="">
                    <Label label="Title" forInput="title" />
                    <TextInput name="title" value={data.title} updateFn={value => setData('title', value)} hasErrors={ !! errors['title'] } />
                    {errors.title && <Error>{errors.title}</Error>}
                </div>

                <fieldset className="">
                    <legend className="text-sm font-medium text-gray-700">Type</legend>
                    <CheckboxGroup
                        name="type.id"
                        options={eventTypes.map((type) => ({ id: type.id, name: type.title }))}
                        value={data['type.id']}
                        updateFn={value => setData('type.id', value)}
                    />
                    {errors['type.id'] && <Error>{errors['type.id']}</Error>}
                </fieldset>

                <div className="">
                    <RadioGroup
                        label={<Label label="Date" />}
                        options={[
                            { id: 'all', name: 'All' },
                            { id: 'upcoming', name: 'Upcoming' },
                            { id: 'past', name: 'Past' },
                        ]}
                        selected={data.date}
                        setSelected={value => setData('date', value)}
                        vertical
                    />
                    {errors.date && <Error>{errors.date}</Error>}
                </div>

                <Button variant="primary" type="submit" size="sm" disabled={processing}>Filter</Button>
            </div>
        </form>
    );
}

export default EventFilters;
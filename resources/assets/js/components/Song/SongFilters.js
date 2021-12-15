import React from 'react';
import Label from "../inputs/Label";
import TextInput from "../inputs/TextInput";
import {useForm} from "@inertiajs/inertia-react";
import Error from "../inputs/Error";
import Button from "../inputs/Button";
import CheckboxGroup from "../inputs/CheckboxGroup";
import SectionTitle from "../SectionTitle";
import SectionHeader from "../SectionHeader";
import Icon from "../Icon";

const SongFilters = ({ statuses, categories, onClose }) => {
    const params = new URLSearchParams(location.search);

    const { data, setData, get, processing, errors, transform } = useForm({
        title: params.get('filter[title]') ?? '',
        'status.id': params.getAll('filter[status.id][]').map(value => parseInt(value)) ?? [],
        'categories.id': params.getAll('filter[categories.id][]').map(value => parseInt(value)) ?? [],
    });

    function submit(e) {
        e.preventDefault();

        get(route('songs.index'));
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

                <div>
                    <Label label="Title" forInput="title" />
                    <TextInput name="title" value={data.title} updateFn={value => setData('title', value)} hasErrors={ !! errors['title'] } />
                    {errors.title && <Error>{errors.title}</Error>}
                </div>

                <fieldset>
                    <legend className="text-sm font-medium text-gray-700">Type</legend>
                    <CheckboxGroup
                        name="status.id"
                        options={statuses.map((status) => ({ id: status.id, name: status.title }))}
                        value={data['status.id']}
                        updateFn={value => setData('status.id', value)}
                    />
                    {errors['status.id'] && <Error>{errors['status.id']}</Error>}
                </fieldset>

                <fieldset>
                    <legend className="text-sm font-medium text-gray-700">Category</legend>
                    <CheckboxGroup
                        name="categories.id"
                        options={categories.map((category) => ({ id: category.id, name: category.title }))}
                        value={data['categories.id']}
                        updateFn={value => setData('categories.id', value)}
                    />
                    {errors['categories.id'] && <Error>{errors['categories.id']}</Error>}
                </fieldset>

                <div className="flex gap-x-2">
                    <Button variant="primary" type="submit" size="sm" disabled={processing} className="flex-grow">
                        <Icon icon="check" />
                        Filter
                    </Button>
                    <Button variant="danger-outline" href={route('songs.index')} size="sm">
                        <Icon icon="times" />
                        Clear
                    </Button>
                </div>
            </div>
        </form>
    );
}

export default SongFilters;
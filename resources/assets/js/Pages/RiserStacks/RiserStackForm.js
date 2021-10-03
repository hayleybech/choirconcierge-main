import React from 'react';
import {useForm} from "@inertiajs/inertia-react";
import FormSection from "../../components/FormSection";
import Label from "../../components/inputs/Label";
import TextInput from "../../components/inputs/TextInput";
import Error from "../../components/inputs/Error";
import ButtonLink from "../../components/inputs/ButtonLink";
import Button from "../../components/inputs/Button";
import RiserStackEditor from "../../components/RiserStack/RiserStackEditor";
import DetailToggle from "../../components/inputs/DetailToggle";
import RiserStackHoldingArea from "../../components/RiserStack/RiserStackHoldingArea";

const RiserStackForm = ({ stack, voiceParts }) => {
    const { data, setData, post, put, processing, errors } = useForm({
        title: stack?.title ?? '',
        rows: stack?.rows ?? 4,
        columns: stack?.cols ?? 4,
        front_row_length: stack?.front_row_length ?? 1,
        front_row_on_floor: stack?.front_row_on_floor ?? false,
        singer_positions: stack?.singers ??  [],
    });

    function submit(e) {
        e.preventDefault();
        stack ? put(route('stacks.update', stack)) : post(route('stacks.store'));
    }

    return (
        <div className="bg-gray-50">

            <form className="space-y-8 divide-y divide-gray-200" onSubmit={submit}>

                    <div className="space-y-8 divide-y divide-gray-200">

                        <div className="px-8">
                            <FormSection>
                                <div className="sm:col-span-2">
                                    <Label label="Riser Stack Title" forInput="title" />
                                    <TextInput name="title" value={data.title} updateFn={value => setData('title', value)} hasErrors={ !! errors['title'] } />
                                    {errors.title && <Error>{errors.title}</Error>}
                                </div>

                                <div className="sm:col-span-1">
                                    <Label label="Rows" forInput="rows" />
                                    <TextInput
                                        name="rows"
                                        type="number"
                                        min={1}
                                        value={data.rows}
                                        updateFn={value => setData('rows', value)}
                                        hasErrors={ !! errors['rows'] }
                                    />
                                    {errors.rows && <Error>{errors.rows}</Error>}
                                </div>

                                <div className="sm:col-span-1">
                                    <Label label="Columns" forInput="columns" />
                                    <TextInput
                                        name="columns"
                                        type="number"
                                        min={1}
                                        value={data.columns}
                                        updateFn={value => setData('columns', value)}
                                        hasErrors={ !! errors['columns'] }
                                    />
                                    {errors.columns && <Error>{errors.columns}</Error>}
                                </div>

                                <div className="sm:col-span-1">
                                    <Label label="Front Row Singers" forInput="front_row_length" />
                                    <TextInput
                                        name="front_row_length"
                                        type="number"
                                        min={1}
                                        value={data.front_row_length}
                                        updateFn={value => setData('front_row_length', value)}
                                        hasErrors={ !! errors['front_row_length'] }
                                    />
                                    {errors.front_row_length && <Error>{errors.front_row_length}</Error>}
                                </div>

                                <div className="sm:col-span-1">
                                    <DetailToggle
                                        label="Front row on floor?"
                                        description="Show a 'floor' row in front of the riser stack."
                                        value={data.front_row_on_floor}
                                        updateFn={value => setData('front_row_on_floor',  value)}
                                    />
                                </div>

                            </FormSection>
                        </div>

                        <div className="flex">
                            <div className="w-1/3 border-r border-gray-200">
                                <RiserStackHoldingArea voiceParts={voiceParts} />
                            </div>

                            <div className="w-2/3">
                                <RiserStackEditor
                                    rows={parseInt(data.rows)}
                                    columns={parseInt(data.columns)}
                                    width={1000}
                                    height={500}
                                    frontRowOnFloor={data.front_row_on_floor}
                                    spotsOnFrontRow={parseInt(data.front_row_length)}
                                    singers={data.singer_positions}
                                    voiceParts={voiceParts}
                                    setPositions={value => setData('singer_positions', value)}
                                />
                            </div>
                        </div>


                    </div>

                    <div className="py-5 px-8">
                        <div className="flex justify-end">
                            <ButtonLink href={stack ? route('stacks.show', stack) : route('stacks.index')}>Cancel</ButtonLink>
                            <Button variant="primary" type="submit" className="ml-3" disabled={processing}>Save</Button>
                        </div>
                    </div>
                </form>
        </div>
    );
}

export default RiserStackForm;
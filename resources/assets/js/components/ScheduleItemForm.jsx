import React from 'react';
import {useForm} from "@inertiajs/react";
import Form from "./Form";
import FormSection from "./FormSection";
import Label from "./inputs/Label";
import SongSelect from "./inputs/SongSelect";
import TextInput from "./inputs/TextInput";
import Error from "./inputs/Error";
import Button from "./inputs/Button";
import Icon from "./Icon";
import useRoute from "../hooks/useRoute";

const ScheduleItemForm = ({ event }) => {
    const { route } = useRoute();

    const {data, setData, post, processing, errors} = useForm({
        song_id: null,
        description: '',
        duration: 0,
    });

    function submit(e) {
        e.preventDefault();
        post(route('events.activities.store', {event}));
    }

    return (
        <Form onSubmit={submit}>
            <FormSection title="Add Activity">
                <div className="sm:col-span-2">
                    <Label label="Song (Optional)" />
                    <SongSelect updateFn={(value) => setData('song_id', value)} />
                </div>
                <div className="sm:col-span-3">
                    <Label label="Description (Optional)" forInput="description" />
                    <TextInput name="description" value={data.description} updateFn={value => setData('description', value)} hasErrors={ !! errors['description'] } />
                    {errors.description && <Error>{errors.description}</Error>}
                </div>
                <div className="sm:col-span-1">
                    <Label label="Duration (Optional)" forInput="duration" />
                    <TextInput
                        name="duration"
                        value={data.duration}
                        updateFn={value => setData('duration', value)}
                        hasErrors={ !! errors['duration'] }
                        type="number"
                        min={0}
                    />
                    {errors.duration && <Error>{errors.duration}</Error>}
                </div>
                <div className="">
                    <Button onClick={submit} variant="primary" size="sm" disabled={processing}><Icon icon="check"/>Save</Button>
                </div>
            </FormSection>
        </Form>
    );
};

export default ScheduleItemForm;
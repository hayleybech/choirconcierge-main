import React, {useEffect} from 'react';
import Dialog from "./Dialog";
import {useForm} from "@inertiajs/inertia-react";
import Form from "./Form";
import FormSection from "./FormSection";
import Label from "./inputs/Label";
import SongSelect from "./inputs/SongSelect";
import TextInput from "./inputs/TextInput";
import Error from "./inputs/Error";

const EditScheduleItemDialog = ({ isOpen, setIsOpen, event, activity }) => {
    const {data, setData, put, processing, errors} = useForm({
        song_id: activity?.song_id ?? null,
        description: activity?.description ?? '',
        duration: activity?.duration ?? 0,
    });

    useEffect(() => {
        setData({
            song_id: activity?.song_id ?? null,
            description: activity?.description ?? '',
            duration: activity?.duration ?? 0,
        });
    }, [activity]);

    function submit(e) {
        e.preventDefault();
        put(route('events.activities.update', [event, activity]), {
            onSuccess: () => setIsOpen(false),
        });
    }

    return (
        <Dialog
            title="Edit Schedule Item"
            okLabel="Save"
            okVariant="primary"
            isOpen={isOpen}
            setIsOpen={setIsOpen}
            onOk={submit}
            processing={processing}
        >
            <Form onSubmit={submit}>
                <FormSection title="">
                    <div className="sm:col-span-6">
                        <Label label="Song (Optional)"/>
                        <SongSelect
                            defaultValue={activity?.song ? { value: activity.song_id, label: activity.song.title, name: activity.song.title } : null}
                            updateFn={(value) => setData('song_id', value)}
                        />
                    </div>
                    <div className="sm:col-span-6">
                        <Label label="Description (Optional)" forInput="description"/>
                        <TextInput name="description" value={data.description} updateFn={value => setData('description', value)}
                                   hasErrors={!!errors['description']}/>
                        {errors.description && <Error>{errors.description}</Error>}
                    </div>
                    <div className="sm:col-span-6">
                        <Label label="Duration (Optional)" forInput="duration"/>
                        <TextInput
                            name="duration"
                            value={data.duration}
                            updateFn={value => setData('duration', value)}
                            hasErrors={!!errors['duration']}
                            type="number"
                            min={0}
                        />
                        {errors.duration && <Error>{errors.duration}</Error>}
                    </div>
                </FormSection>
            </Form>
        </Dialog>
    );
};

export default EditScheduleItemDialog;
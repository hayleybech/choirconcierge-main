import React from 'react';
import {useMediaQuery} from "react-responsive";
import EventScheduleDesktop from "./EventScheduleDesktop";
import {useForm} from "@inertiajs/inertia-react";
import FormSection from "../FormSection";
import Label from "../inputs/Label";
import Error from "../inputs/Error";
import Button from "../inputs/Button";
import Icon from "../Icon";
import TextInput from "../inputs/TextInput";

const EventSchedule = ({ event }) => {
    const isDesktop = useMediaQuery({ query: '(min-width: 1024px)' });
    return (
        <div>
            {isDesktop ? <EventScheduleDesktop event={event} /> : null}

            {event.can.update_event && <ScheduleItemForm event={event} />}
        </div>
    );
};

export default EventSchedule;

const ScheduleItemForm = ({ event }) => {
    const {data, setData, post, processing, errors} = useForm({
        description: '',
        duration: 0,
    });

    function submit(e) {
        e.preventDefault();
        post(route('events.activities.store', event));
    }

    return (
        <div className="md:max-w-5xl md:mx-auto px-4 sm:px-6 lg:px-8 pb-8">
            <FormSection title="Add Activity">
                <div className="sm:col-span-3">
                    <div className="sm:col-span-2">
                        <Label label="Description" forInput="description" />
                        <TextInput name="description" value={data.description} updateFn={value => setData('description', value)} hasErrors={ !! errors['description'] } />
                        {errors.description && <Error>{errors.description}</Error>}
                    </div>
                </div>
                <div className="sm:col-span-3">
                    <div className="sm:col-span-2">
                        <Label label="Duration" forInput="duration" />
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
                </div>
                <div className="">
                    <Button onClick={submit} variant="primary" size="sm" disabled={processing}><Icon icon="check"/>Save</Button>
                </div>
            </FormSection>
        </div>
    );
};
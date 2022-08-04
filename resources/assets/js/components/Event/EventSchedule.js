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
import EventScheduleMobile from "./EventScheduleMobile";
import SongSelect from "../inputs/SongSelect";
import EmptyState from "../EmptyState";

const EventSchedule = ({ event }) => {
    const isDesktop = useMediaQuery({ query: '(min-width: 1024px)' });

    return (
        <div>
            {event.activities.length > 0
                ? (isDesktop ? <EventScheduleDesktop event={event} /> : <EventScheduleMobile event={event} />)
                : <EmptyState
                    title="Empty schedule"
                    description={<>
                        Looks like this event doesn't have a schedule. This tool is great for rehearsals and performances. <br />
                        Schedules can assign songs and estimate the event duration.
                    </>}
                    actionDescription={event.can['update_event'] ? 'To get started, use the form below.' : null}
                    icon="stream"
                />
            }

            {event.can.update_event && <ScheduleItemForm event={event} />}
        </div>
    );
};

export default EventSchedule;

const ScheduleItemForm = ({ event }) => {
    const {data, setData, post, processing, errors} = useForm({
        song_id: null,
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
        </div>
    );
};
import React, {useEffect} from 'react';
import {useForm, usePage} from "@inertiajs/inertia-react";
import FormSection from "../../components/FormSection";
import Label from "../../components/inputs/Label";
import TextInput from "../../components/inputs/TextInput";
import Error from "../../components/inputs/Error";
import RadioGroup from "../../components/inputs/RadioGroup";
import Help from "../../components/inputs/Help";
import CheckboxInput from "../../components/inputs/CheckboxInput";
import ButtonLink from "../../components/inputs/ButtonLink";
import Button from "../../components/inputs/Button";
import TextareaInput from "../../components/inputs/TextareaInput";
import LocationInput from "../../components/inputs/LocationInput";
import DateInput from "../../components/inputs/Date";
import TimeInput from "../../components/inputs/Time";
import DetailToggle from "../../components/inputs/DetailToggle";
import {DateTime} from "luxon";
import {TIME_24_SIMPLE} from "luxon/src/impl/formats";

const EventForm = ({ event, types }) => {
    const rawDateFormat = 'yyyy-MM-dd HH:mm:ss';
    const { timezone_label } = usePage().props.tenant;
    const { data, setData, post, put, processing, errors, transform } = useForm({
        title: event?.title ?? '',
        type_id: event?.type.id ?? null,
        location_name: event?.location_name ?? '',
        location_place_id: event?.location_place_id ?? '',
        location_icon: event?.location_icon ?? '',
        location_address: event?.location_address ?? '',
        description: event?.description ?? '',
        send_notification: true,
        is_repeating: event?.is_repeating ?? false,
        repeat_frequency_unit: event?.repeat_frequency_unit ?? null,

        start_date: event?.start_date ? DateTime.fromISO(event.start_date) : DateTime.now(),
        end_date: event?.end_date ? DateTime.fromISO(event.end_date) : DateTime.now(),
        call_time: event?.call_time ? DateTime.fromISO(event.call_time) : DateTime.now(),
        repeat_until: event?.repeat_until ? DateTime.fromISO(event.repeat_until) : DateTime.now(),
    });

    function submit(e) {
        e.preventDefault();
        event ? put(route('events.update', event)) : post(route('events.store'));
    }

    transform((data) => ({
        ...data,
        start_date: data.start_date.toFormat(rawDateFormat),
        end_date: data.end_date.toFormat(rawDateFormat),
        call_time: data.call_time.toFormat(rawDateFormat),
        repeat_until: data.repeat_until.toFormat(rawDateFormat),
    }));

    function setLocation(value) {
        setData({
            ...data,
            location_place_id: value.place_id,
            location_name: value.name,
            location_icon: value.icon,
            location_address: value.address,
        })
    }

    useEffect(() => {
        setData({
            ...data,
            end_date: constrainEndDate(data.end_date),
            call_time: constrainCallTime(data.call_time),
            repeat_until: constrainRepeatUntil(data.repeat_until),
        });
    }, [data.start_date]);

    function setStartDate(value) {
        setData({
            ...data,
            start_date: setDay(DateTime.fromJSDate(value), data.start_date),
            call_time: setDay(DateTime.fromJSDate(value), data.start_date),
        });
    }
    function setStartTime(value) {
        setData('start_date', setTime(DateTime.fromISO(value), data.start_date) );
    }
    function setEndDate(value) {
        setData('end_date', constrainEndDate(setDay(DateTime.fromJSDate(value), data.end_date)));
    }
    function setEndTime(value) {
        setData('end_date', constrainEndDate(setTime(DateTime.fromISO(value), data.end_date)));
    }
    function setCallTime(value) {
        setData('call_time', constrainCallTime(setTime(DateTime.fromISO(value), data.call_time)));
    }
    function setRepeatUntil(value) {
        setData('repeat_until', constrainRepeatUntil(DateTime.fromJSDate(value)));
    }

    function constrainEndDate(newEndDate)
    {
        const minEndDateAfterStartDate = { minutes: 15 };
        console.log(newEndDate);
        console.log(data.start_date);
        console.log(data.start_date.plus(minEndDateAfterStartDate) > newEndDate);
        if (data.start_date.plus(minEndDateAfterStartDate) > newEndDate) {
            return data.start_date.plus(minEndDateAfterStartDate);
        }
        return newEndDate;
    }
    function constrainCallTime(newCallTime)
    {
        const minCallTimeBeforeStartTime = { minutes: 15 };
        if (data.start_date.minus(minCallTimeBeforeStartTime) < newCallTime) {
            return data.start_date.minus(minCallTimeBeforeStartTime);
        }
        return newCallTime;
    }
    function constrainRepeatUntil(newRepeatUntilDate)
    {
        if (data.start_date > newRepeatUntilDate) {
            return data.start_date;
        }
        return newRepeatUntilDate;
    }
    
    function setDay(source, target) {
        return target.set({
            year: source.year,
            month: source.month,
            day: source.day,

            hour: target.hour,
            minute: target.minute,
        })
    }
    function setTime(source, target) {
        return target.set({
            hour: source.hour,
            minute: source.minute,
        })
    }

    function strToTitleCase(str) {
        return str.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
    }

    return (
        <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <form className="space-y-8 divide-y divide-gray-200" onSubmit={submit}>

                <FormSection title="Event Details">
                    <div className="sm:col-span-6">
                        <Label label="Title" forInput="title" />
                        <TextInput
                            name="title"
                            value={data.title}
                            updateFn={value => setData('title', value)}
                            hasErrors={ !! errors['title'] }
                        />
                        {errors.title && <Error>{errors.title}</Error>}
                    </div>

                    <div className="sm:col-span-6">
                        <RadioGroup
                            label={<Label label="Event Type" />}
                            options={types.map(type => ({ id: type.id, name: type.title }))}
                            selected={data.type_id}
                            setSelected={value => setData('type_id', value)}
                        />
                        {errors.type_id && <Error>{errors.type_id}</Error>}
                    </div>

                    <div className="sm:col-span-6">
                        <Label label="Location" forInput="location" />
                        <LocationInput
                            name="location"
                            value={{
                                label: data.location_name,
                                value: {
                                    place_id: data.location_place_id,
                                }
                            }}
                            updateFn={setLocation}
                            hasErrors={ !! errors['location'] }
                        />
                        {errors.location_place_id && <Error>{errors.location_place_id}</Error>}
                        {errors.location_name && <Error>{errors.location_name}</Error>}
                        {errors.location_icon && <Error>{errors.location_icon}</Error>}
                        {errors.location_address && <Error>{errors.location_address}</Error>}
                    </div>

                    <div className="sm:col-span-6">
                        <Label label="Description" forInput="description" />
                        <TextareaInput
                            name="description"
                            value={data.description}
                            updateFn={value => setData('description', value)}
                            hasErrors={ !! errors['description'] }
                        />
                        {errors.description && <Error>{errors.description}</Error>}
                    </div>

                    <div className="sm:col-span-4">
                        <Label label="Start Date" forInput="start_date" />
                        <DateInput
                            name="start_date"
                            value={data.start_date}
                            updateFn={setStartDate}
                            hasErrors={ !! errors.start_date }
                        />
                        {errors.start_date && <Error>{errors.start_date}</Error>}
                        <Help>{data.start_date.toFormat(rawDateFormat)}</Help>
                    </div>

                    <div className="sm:col-span-1">
                        <Label label="Start Time" forInput="start_time" />
                        <TimeInput
                            name="start_time"
                            value={data.start_date.toLocaleString(TIME_24_SIMPLE)}
                            updateFn={setStartTime}
                        />
                    </div>

                    <div className="sm:col-span-1">
                        <Label label="Arrival Time" forInput="call_time" />
                        <TimeInput
                            name="call_time"
                            value={data.call_time.toLocaleString(TIME_24_SIMPLE)}
                            updateFn={setCallTime}
                        />
                        <Help>At least 15 min before</Help>
                    </div>

                    <div className="sm:col-span-4">
                        <Label label="End Date" forInput="end_date" />
                        <DateInput
                            name="end_date"
                            value={data.end_date}
                            updateFn={setEndDate}
                            hasErrors={ !! errors.end_date }
                        />
                        {errors.end_date && <Error>{errors.end_date}</Error>}
                        <Help>Timezone: {timezone_label}</Help>
                    </div>

                    <div className="sm:col-span-2">
                        <Label label="End Time" forInput="end_time" />
                        <TimeInput
                            name="end_time"
                            value={data.end_date.toLocaleString(TIME_24_SIMPLE)}
                            updateFn={setEndTime}
                        />
                        <Help>At least 15 min after</Help>
                    </div>

                    <div className="sm:col-span-6">
                        <DetailToggle
                            label="Repeating Event?"
                            value={data.is_repeating}
                            updateFn={value => setData('is_repeating',  value)}
                        />
                    </div>

                </FormSection>

                {data.is_repeating && (
                    <FormSection title="Repeating Event Details">
                        <div className="sm:col-span-6">
                            <RadioGroup
                                label={<Label label="Repeat Every" />}
                                options={['day', 'week', 'month', 'year'].map(unit => ({ id: unit, name: strToTitleCase(unit) }))}
                                selected={data.repeat_frequency_unit}
                                setSelected={value => setData('repeat_frequency_unit', value)}
                            />
                            {errors.repeat_frequency_unit && <Error>{errors.repeat_frequency_unit}</Error>}
                        </div>

                        <div className="sm:col-span-6">
                            <Label label="Repeat Until" forInput="repeat_until" />
                            <DateInput
                                name="repeat_until"
                                value={data.repeat_until}
                                updateFn={setRepeatUntil}
                                hasErrors={ !! errors.repeat_until }
                            />
                            {errors.repeat_until && <Error>{errors.repeat_until}</Error>}
                        </div>
                    </FormSection>
                )}

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
                                Send "{event ? 'Event Updated' : 'Event Created'}" notification to singers?
                            </label>
                        </div>
                    </div>
                </FormSection>

                <div className="pt-5 flex justify-end">
                    <ButtonLink href={event ? route('songs.show', event) : route('songs.index')}>Cancel</ButtonLink>
                    <Button variant="primary" type="submit" className="ml-3" disabled={processing}>Save</Button>
                </div>
            </form>
        </div>
    );
}

export default EventForm;
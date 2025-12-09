import React from 'react';
import AppHead from "../../../components/AppHead";
import { useForm, usePage } from '@inertiajs/react';
import Form from '../../../components/Form';
import Button from '../../../components/inputs/Button';
import ToastFlash from '../../../components/ToastFlash';
import Icon from '../../../components/Icon';
import {DateTime} from "luxon";

const CheckIn = ({ event, storeUrlSigned }) => {
    const { tenant, flash, user } = usePage().props;

    const {data, setData, post, processing, errors, wasSuccessful} = useForm();

    function submit(e) {
        e.preventDefault();
        post(storeUrlSigned);
    }

    const eventIsUpcoming = DateTime.fromISO(event.start_date) > DateTime.now();

    return (
        <>
            <AppHead title={`Check-In Page - ${event.title}`} />
            <ToastFlash errors={errors} flash={flash} />

            <div className="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-gray-100">
                <div className="sm:mx-auto sm:w-full sm:max-w-md mb-8">

                    <div className="mb-8">
                        {tenant ? (
                            <img src={tenant.logo_url} alt={tenant.name} className="h-16 w-auto mx-auto" />
                        ) : (
                            <img src="/img/vibrant/logo-dark.svg" alt="Choir Concierge" className="h-12 w-auto mx-auto" />
                        )}
                    </div>

                    <h2 className="mb-6 text-center text-4xl font-extrabold text-gray-900">Event Check-In</h2>

                    {eventIsUpcoming ? (
                        <p className="text-center mb-2 text-red-500">Check-in is closed. Come back to this page on the day of the Event.</p>
                    ) : (
                        <p className="text-center mb-2">You are checking in for:</p>
                    )}

                    <h3 className="mb-2 text-center text-4xl font-extrabold text-gray-900">{event.title}</h3>

                    <div className="mt-2 font-bold flex items-center justify-center text-gray-600">
                        <Icon icon="calendar-day" type="regular" mr />
                        {DateTime.fromISO(event.start_date).hasSame(DateTime.fromISO(event.end_date), 'day') ? (
                            <span className="whitespace-nowrap">
								{DateTime.fromISO(event.start_date).toLocaleString(DateTime.DATE_MED)}
							</span>
                        ) : (
                            <div>
								<span className="whitespace-nowrap">
									{DateTime.fromISO(event.start_date).toLocaleString(DateTime.DATE_MED)}
								</span>
                                {' - '}
                                <span className="whitespace-nowrap">
									{DateTime.fromISO(event.end_date).toLocaleString(DateTime.DATE_MED)}
								</span>
                            </div>
                        )}
                    </div>

                </div>

                <div className="sm:mx-auto sm:w-full sm:max-w-md mb-12">
                    <div className="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">

                        <Form onSubmit={submit}>
                            <div className="flex space-x-4 items-center justify-center">
                                {user.profile_avatar_url && <img src={user.profile_avatar_url} alt={user.name} className="h-12 rounded-md" />}
                                <div className="text-gray-800 text-lg font-bold">
                                    {user.name}
                                </div>
                            </div>

                            <Button variant="primary" type="submit" size="sm" className="space-x-2 w-full" disabled={processing || eventIsUpcoming || wasSuccessful}>
                                <Icon icon="check" />
                                I'm Here
                            </Button>
                        </Form>

                    </div>
                </div>

                <img src="/img/vibrant/logo-dark.svg" alt="Choir Concierge" className="h-8 w-auto mx-auto" />

            </div>

        </>
    );
}

export default CheckIn;
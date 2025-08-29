import React from 'react';
import AppHead from "../../../components/AppHead";
import useRoute from "../../../hooks/useRoute";
import SingerSelect from '../../../components/inputs/SingerSelect';
import Label from '../../../components/inputs/Label';
import { useForm, usePage } from '@inertiajs/react';
import Form from '../../../components/Form';
import Button from '../../../components/inputs/Button';
import ToastFlash from '../../../components/ToastFlash';
import Icon from '../../../components/Icon';
import Error from '../../../components/inputs/Error';
import DateTag from '../../../components/DateTag';

const CheckIn = ({ event, storeUrlSigned }) => {
    const { tenant, flash, user } = usePage().props;

    const {data, setData, post, processing, errors} = useForm();

    function submit(e) {
        e.preventDefault();
        post(storeUrlSigned);
    }

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

                    <h2 className="mb-6 text-center text-3xl font-extrabold text-gray-900">Event Check-In</h2>

                    <p className="text-center mb-2">You are checking in for:</p>

                    <h3 className="mb-2 text-center text-xl font-extrabold text-gray-900">{event.title}</h3>

                    <div className="text-gray-700 text-sm flex gap-2 justify-center">
                        <div>This page expires at:</div>
                        <DateTag date={event.end_date} />
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

                            <Button variant="primary" type="submit" size="sm" className="space-x-2 w-full" disabled={processing}>
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
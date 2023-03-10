import React from 'react'
import AppHead from "../../components/AppHead";
import {Link, useForm, usePage} from "@inertiajs/inertia-react";
import Label from "../../components/inputs/Label";
import Button from "../../components/inputs/Button";
import TextInput from "../../components/inputs/TextInput";
import Error from "../../components/inputs/Error";
import Icon from "../../components/Icon";
import useRoute from "../../hooks/useRoute";

const ForgotPassword = ({  }) => {
    const { route } = useRoute();
    const { tenant } = usePage().props;
    const { data, setData, post, processing, errors } = useForm({
        email: '',
    });

    function submit(e) {
        e.preventDefault();

        post(route('password.email'));
    }

    return (
        <>
            <AppHead title="Forgot Password" />

            <div className="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-gray-100">
                <div className="sm:mx-auto sm:w-full sm:max-w-md">

                    <img src="/img/vibrant/logo-dark.svg" alt="Choir Concierge" className="h-12 w-auto mx-auto" />

                    {tenant && <img src={tenant.logo_url} alt={tenant.choir_name} className="h-12 w-auto mx-auto mt-6" />}

                    <h2 className="mt-6 text-center text-3xl font-extrabold text-gray-900">Reset your password</h2>

                </div>

                <div className="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
                    <div className="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                        <form className="space-y-6" onSubmit={submit}>

                            <div>
                                <Label label="Email address" forInput="email" />
                                <TextInput
                                    name="email"
                                    value={data.email}
                                    updateFn={value => setData('email', value)}
                                    hasErrors={ !! errors['email'] }
                                />
                                {errors.email && <Error>{errors.email}</Error>}
                            </div>

                            <div>
                                <Button variant="primary" type="submit" size="sm" className="w-full" disabled={processing}>Send password reset link</Button>
                            </div>

                            <div className="text-sm">
                                <Link href={route('login')} className="font-medium text-purple-600 hover:text-purple-500">
                                    <Icon icon="chevron-left" mr />
                                    Back to login
                                </Link>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </>
    );
}

export default ForgotPassword;

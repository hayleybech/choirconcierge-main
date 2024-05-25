import React from 'react'
import AppHead from "../../components/AppHead";
import {useForm} from "@inertiajs/react";
import Label from "../../components/inputs/Label";
import Button from "../../components/inputs/Button";
import TextInput from "../../components/inputs/TextInput";
import Error from "../../components/inputs/Error";
import useRoute from "../../hooks/useRoute";

const Register = ({  }) => {
    const { route } = useRoute();
    const { data, setData, post, processing, errors } = useForm({
        email: '',
        password: '',
        password_confirmation: '',
        first_name: '',
        last_name: '',
    });

    function submit(e) {
        e.preventDefault();

        post(route('register'));
    }

    return (
        <>
            <AppHead title="Register" />

            <div className="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-gray-100">
                <div className="sm:mx-auto sm:w-full sm:max-w-md">

                    <img src="/img/vibrant/logo-dark.svg" alt="Choir Concierge" className="h-12 w-auto mx-auto" />

                    <h2 className="mt-6 text-center text-3xl font-extrabold text-gray-900">Create an account</h2>
                    <p className="mt-2 text-center text-sm leading-6 text-gray-500">Enter your details to create an account and get your choir started with us. </p>
                    <p className="mt-2 text-center text-sm leading-6 text-gray-500">Are you a regular singer? <a href={route('login')} className="font-medium text-purple-600 hover:text-purple-500">Log in</a></p>

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
                                <Label label="Password" forInput="password" />
                                <TextInput
                                    name="password"
                                    type="password"
                                    value={data.password}
                                    updateFn={value => setData('password', value)}
                                    hasErrors={ !! errors['password'] }
                                />
                                {errors.password && <Error>{errors.password}</Error>}
                            </div>

                            <div className="sm:col-span-3">
                                <Label label="Confirm password" forInput="password_confirmation" />
                                <TextInput type="password" name="password_confirmation" value={data.password_confirmation} updateFn={value => setData('password_confirmation', value)} hasErrors={ !! errors['password_confirmation'] } />
                                {errors.password_confirmation && <Error>{errors.password_confirmation}</Error>}
                            </div>

                            <div className="sm:col-span-3">
                                <Label label="First name" forInput="first_name" />
                                <TextInput name="first_name" autoComplete="given-name" value={data.first_name} updateFn={value => setData('first_name', value)} hasErrors={ !! errors['first_name'] } />
                                {errors.first_name && <Error>{errors.first_name}</Error>}
                            </div>

                            <div className="sm:col-span-3">
                                <Label label="Last name" forInput="last_name" />
                                <TextInput name="last_name" autoComplete="family-name" value={data.last_name} updateFn={value => setData('last_name', value)} hasErrors={ !! errors['last_name'] } />
                                {errors.last_name && <Error>{errors.last_name}</Error>}
                            </div>

                            <div>
                                <Button variant="primary" type="submit" size="sm" className="w-full" disabled={processing}>Register</Button>
                            </div>
                        </form>

                    </div>

                </div>
            </div>
        </>
    );
}

export default Register;

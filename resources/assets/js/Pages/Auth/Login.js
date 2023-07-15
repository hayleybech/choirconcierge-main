import React from 'react'
import AppHead from "../../components/AppHead";
import {Link, useForm, usePage} from "@inertiajs/inertia-react";
import Label from "../../components/inputs/Label";
import Button from "../../components/inputs/Button";
import TextInput from "../../components/inputs/TextInput";
import Error from "../../components/inputs/Error";
import CheckboxWithLabel from "../../components/inputs/CheckboxWithLabel";
import useRoute from "../../hooks/useRoute";

const Login = ({  }) => {
    const { route } = useRoute();
    const { tenant } = usePage().props;
    const { data, setData, post, processing, errors } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    function submit(e) {
        e.preventDefault();

        post(route('login'));
    }

    return (
        <>
            <AppHead title="Login" />

            <div className="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-gray-100">
                <div className="sm:mx-auto sm:w-full sm:max-w-md">

                    <img src="/img/vibrant/logo-dark.svg" alt="Choir Concierge" className="h-12 w-auto mx-auto" />

                    {tenant && <img src={tenant.logo_url} alt={tenant.name} className="h-12 w-auto mx-auto mt-6" />}

                    <h2 className="mt-6 text-center text-3xl font-extrabold text-gray-900">Sign in to your account</h2>

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

                            <div className="flex items-center justify-between">
                                <CheckboxWithLabel
                                    label="Remember me"
                                    id="remember"
                                    name="remember"
                                    checked={data.remember}
                                    onChange={e => setData('remember', e.target.checked)}
                                    className="mr-8 mb-4"
                                />

                                <div className="text-sm">
                                    <Link href={route('password.request')} className="font-medium text-purple-600 hover:text-purple-500">
                                        Forgot your password?
                                    </Link>
                                </div>
                            </div>

                            <div>
                                <Button variant="primary" type="submit" size="sm" className="w-full" disabled={processing}>Sign in</Button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </>
    );
}

export default Login;

import React from 'react'
import Layout from "../../Layouts/Layout";
import SingerPageHeader from "./SingerPageHeader";
import {Link} from "@inertiajs/inertia-react";
import Label from "../../components/inputs/Label";
import TextInput from "../../components/inputs/TextInput";
import DetailToggle from "../../components/inputs/DetailToggle";

const Create = ({voice_parts, roles}) => (
    <>
        <SingerPageHeader
            title={'Create Singer'}
            icon="fa-users"
            breadcrumbs={[
                <Link href={route('dash')} className="text-sm font-medium text-gray-500 hover:text-gray-700">
                    Dashboard
                </Link>,
                <Link href={route('singers.index')} className="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">
                    Singers
                </Link>,
                <Link href={route('singers.create')} className="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">
                    Create
                </Link>
            ]}
        />

        <div className="bg-gray-50">

            <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <form className="space-y-8 divide-y divide-gray-200">

                    <div className="space-y-8 divide-y divide-gray-200">
                        <div>
                            <div>
                                <h3 className="text-lg leading-6 font-medium text-gray-900">User Details</h3>
                                <p className="mt-1 text-sm text-gray-500">
                                    Create an account for the singer.
                                </p>
                            </div>

                            <div className="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                <div className="sm:col-span-3">
                                    <Label label="First name" forInput="first_name" />
                                    <div className="mt-1">
                                        <TextInput name="first_name" autoComplete="given-name" />
                                    </div>
                                </div>

                                <div className="sm:col-span-3">
                                    <Label label="Last name" forInput="last_name" />
                                    <div className="mt-1">
                                        <TextInput name="last_name" autoComplete="family-name" />
                                    </div>
                                </div>

                                <div className="sm:col-span-4">
                                    <Label label="Email address" forInput="email" />
                                    <div className="mt-1">
                                        <TextInput name="email" type="email" autoComplete="email" />
                                    </div>
                                </div>

                                <div className="sm:col-span-3">
                                    <Label label="Password" forInput="password" />
                                    <div className="mt-1">
                                        <TextInput type="password" name="password" />
                                    </div>
                                </div>

                                <div className="sm:col-span-3">
                                    <Label label="Confirm password" forInput="password_confirmation" />
                                    <div className="mt-1">
                                        <TextInput type="password" name="password_confirmation" />
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div className="pt-8">
                            <div>
                                <h3 className="text-lg leading-6 font-medium text-gray-900">Singer Details</h3>
                                <p className="mt-1 text-sm text-gray-500">Start adding information about the singer's membership.</p>
                            </div>
                            <div className="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">

                                <div className="sm:col-span-3">
                                    <Label label="Voice part" forInput="voice_part" />
                                    <div className="mt-1">
                                        <select
                                            id="voice_part"
                                            name="voice_part"
                                            className="shadow-sm focus:ring-purple-500 focus:border-purple-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                        >
                                            <option>Tenor</option>
                                            <option>Lead</option>
                                            <option>Baritone</option>
                                            <option>Bass</option>
                                        </select>
                                    </div>
                                </div>

                                <div className="sm:col-span-6">
                                    <Label label="Why are you joining?" forInput="reason_for_joining" />
                                    <div className="mt-1">
                                        <TextInput name="reason_for_joining" />
                                    </div>
                                </div>

                                <div className="sm:col-span-6">
                                    <Label label="Where did you hear about us?" forInput="referrer" />
                                    <div className="mt-1">
                                        <TextInput name="referrer" />
                                    </div>
                                </div>

                                <div className="sm:col-span-6">
                                    <Label label="Notes / Membership Details" forInput="membership_details" />
                                    <div className="mt-1">
                                        <TextInput name="membership_details" />
                                    </div>
                                </div>

                                <div className="sm:col-span-6">
                                    <DetailToggle label="Is this an existing member?" description="Onboarding will be disabled when adding an existing singer." />
                                </div>

                            </div>
                        </div>

                        <div className="pt-8">
                            <div>
                                <h3 className="text-lg leading-6 font-medium text-gray-900">Existing Member Details</h3>
                                <p className="mt-1 text-sm text-gray-500">

                                </p>
                            </div>
                            <div className="mt-6">
                                <div className="sm:col-span-6">
                                    <Label label="Joined" forInput="joined_at" />

                                    <div className="mt-1 relative rounded-md shadow-sm">
                                        <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i className="far fa-fw fa-calendar-day text-gray-400" />
                                        </div>
                                        <input type="text" name="joined__at" id="joined_at" className="focus:ring-purple-500 focus:border-purple-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md" placeholder="26/09/1991" />
                                    </div>
                                </div>

                                <fieldset className="mt-6">
                                    <legend className="text-base font-medium text-gray-900">Roles</legend>
                                    <div className="mt-4 grid grid-cols-2 md:flex md:flex-wrap">
                                        {roles.map((role, key) => (
                                            <React.Fragment key={key}>
                                                {role.name === 'User'
                                                    ? (<input type="hidden" name="user_roles[]" id={'user_roles_'+role.id} value={role.id} />)
                                                    : (
                                                    <div className="relative flex items-start mr-8 mb-4">
                                                        <div className="flex items-center h-5">
                                                            <input type="checkbox"
                                                               id={'user_roles_'+role.id}
                                                               name="user_roles[]"
                                                               value={role.id}
                                                               className="focus:ring-purple-500 h-4 w-4 text-purple-600 border-gray-300 rounded"
                                                            />
                                                        </div>
                                                        <div className="ml-3 text-sm">
                                                            <label htmlFor={'user_roles_'+role.id} className="font-medium text-gray-700">
                                                                {role.name}
                                                            </label>
                                                        </div>
                                                    </div>
                                                    )
                                                }
                                            </React.Fragment>
                                        ))}
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>

                    <div className="pt-5">
                        <div className="flex justify-end">
                            <button
                                type="button"
                                className="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                className="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
                            >
                                Save
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </>
);

Create.layout = page => <Layout children={page} title="Singers" />

export default Create;
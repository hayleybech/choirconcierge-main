import React from 'react'
import Layout from "../../Layouts/Layout";
import SingerPageHeader from "./SingerPageHeader";
import {Link, useForm} from "@inertiajs/inertia-react";
import Label from "../../components/inputs/Label";
import TextInput from "../../components/inputs/TextInput";
import DetailToggle from "../../components/inputs/DetailToggle";
import Error from "../../components/inputs/Error";
import Select from "../../components/inputs/Select";
import Date from "../../components/inputs/Date";
import Help from "../../components/inputs/Help";
import FormSection from "../../components/FormSection";
import Button from "../../components/inputs/Button";

const Create = ({voice_parts, roles}) => {
    const { data, setData, post, processing, errors } = useForm({
        first_name: '',
        last_name: '',
        email: '',
        password: '',
        password_confirmation: '',

        voice_part: 0,
        reason_for_joining: '',
        referrer: '',
        membership_details: '',

        existing_member: false,
        joined_at: '',
        user_roles: [],
    });

    function submit(e) {
        e.preventDefault();
        console.log(data);
        post(route('singers.store'));
    }

    function syncInput(inputName){
        return {
            value: data[inputName],
            onChange: e => setData(inputName, e.target.value),
        };
    }
    function syncCheckboxes(groupName, inputValue){
        return {
            value: data[groupName].includes(inputValue),
            onChange: e => setData(
                groupName,
                e.target.checked
                    ? arrUnique([...data[groupName], inputValue])
                    : data[groupName].filter(item => item !== inputValue)
            )
        };
    }
    function syncSwitch(inputName){
        return {
            checked: data[inputName],
            onChange: checked => setData(inputName, checked),
        };
    }

    function arrUnique(array){
        console.log(array);
        return [...new Set(array)];
    }

    return (
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
                    <form className="space-y-8 divide-y divide-gray-200" onSubmit={submit}>

                        <div className="space-y-8 divide-y divide-gray-200">

                            <FormSection title="User Details" description="Create an account for the singer.">
                                <div className="sm:col-span-3">
                                    <Label label="First name" forInput="first_name" />
                                    <TextInput name="first_name" autoComplete="given-name" {...syncInput('first_name')} hasErrors={ !! errors['first_name'] } />
                                    {errors.first_name && <Error>{errors.first_name}</Error>}
                                </div>

                                <div className="sm:col-span-3">
                                    <Label label="Last name" forInput="last_name" />
                                    <TextInput name="last_name" autoComplete="family-name" {...syncInput('last_name')} hasErrors={ !! errors['last_name'] } />
                                    {errors.last_name && <Error>{errors.last_name}</Error>}
                                </div>

                                <div className="sm:col-span-4">
                                    <Label label="Email address" forInput="email" />
                                    <TextInput name="email" type="email" autoComplete="email" {...syncInput('email')} hasErrors={ !! errors['email'] } />
                                    {errors.email && <Error>{errors.email}</Error>}
                                </div>

                                <div className="sm:col-span-3">
                                    <Label label="Password" forInput="password" />
                                    <TextInput type="password" name="password" {...syncInput('password')} hasErrors={ !! errors['password'] } />
                                    <Help>You may leave this blank and update it later.</Help>
                                    {errors.password && <Error>{errors.password}</Error>}
                                </div>

                                <div className="sm:col-span-3">
                                    <Label label="Confirm password" forInput="password_confirmation" />
                                    <TextInput type="password" name="password_confirmation" {...syncInput('password_confirmation')} hasErrors={ !! errors['password_confirmation'] } />
                                    {errors.password_confirmation && <Error>{errors.password_confirmation}</Error>}
                                </div>
                            </FormSection>

                            <FormSection title="Singer Details" description="Start adding information about the singer's membership.">
                                <div className="sm:col-span-3">
                                    <Label label="Voice part" forInput="voice_part" />
                                    <Select name="voice_part" options={voice_parts} {...syncInput('voice_part')} />
                                    {errors.voice_part && <Error>{errors.voice_part}</Error>}
                                </div>

                                <div className="sm:col-span-6">
                                    <Label label="Why are you joining?" forInput="reason_for_joining" />
                                    <TextInput name="reason_for_joining" {...syncInput('reason_for_joining')} hasErrors={ !! errors['reason_for_joining'] } />
                                    {errors.reason_for_joining && <Error>{errors.reason_for_joining}</Error>}
                                </div>

                                <div className="sm:col-span-6">
                                    <Label label="Where did you hear about us?" forInput="referrer" />
                                    <TextInput name="referrer" {...syncInput('referrer')} hasErrors={ !! errors['referrer'] } />
                                    {errors.referrer && <Error>{errors.referrer}</Error>}
                                </div>

                                <div className="sm:col-span-6">
                                    <Label label="Notes / Membership Details" forInput="membership_details" />
                                    <TextInput name="membership_details" {...syncInput('membership_details')} hasErrors={ !! errors['membership_details'] } />
                                    {errors.membership_details && <Error>{errors.membership_details}</Error>}
                                </div>

                                <div className="sm:col-span-6">
                                    <DetailToggle
                                        label="Is this an existing member?"
                                        description="Onboarding will be disabled when adding an existing singer."
                                        {...syncSwitch('existing_member')}
                                    />
                                </div>
                            </FormSection>

                            {data.existing_member && (
                            <FormSection title="Existing Member Details">
                                <div className="sm:col-span-6">
                                    <Label label="Joined" forInput="joined_at" />
                                    <Date name="joined_at" placeholder="26/09/1991" hasErrors={ !! errors['joined_at'] } {...syncInput('joined_at')} />
                                    {errors.joined_at && <Error>{errors.joined_at}</Error>}
                                </div>

                                <fieldset className="mt-6 sm:col-span-6">
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
                                                                   {...syncCheckboxes('user_roles', role.id)}
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
                                    {errors.user_roles && <Error>{errors.user_roles}</Error>}
                                </fieldset>
                            </FormSection>
                            )}

                        </div>

                        <div className="pt-5">
                            <div className="flex justify-end">
                                <Button>Cancel</Button>
                                <Button primary type="submit">Save</Button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </>
    );
}

Create.layout = page => <Layout children={page} title="Singers" />

export default Create;
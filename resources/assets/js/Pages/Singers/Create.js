import React from 'react'
import Layout from "../../Layouts/Layout";
import {useForm} from "@inertiajs/inertia-react";
import Label from "../../components/inputs/Label";
import TextInput from "../../components/inputs/TextInput";
import DetailToggle from "../../components/inputs/DetailToggle";
import Error from "../../components/inputs/Error";
import Select from "../../components/inputs/Select";
import Date from "../../components/inputs/Date";
import Help from "../../components/inputs/Help";
import FormSection from "../../components/FormSection";
import Button from "../../components/inputs/Button";
import ButtonLink from "../../components/inputs/ButtonLink";
import CheckboxGroup from "../../components/inputs/CheckboxGroup";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import Form from "../../components/Form";
import FormFooter from "../../components/FormFooter";
import GlobalUserSelect from "../../components/inputs/GlobalUserSelect";

const Create = ({voice_parts, roles}) => {
    const { data, setData, post, processing, errors } = useForm({
        create: true,

        user_id: null,
        first_name: '',
        last_name: '',
        email: '',
        password: '',
        password_confirmation: '',

        voice_part_id: 0,
        reason_for_joining: '',
        referrer: '',
        membership_details: '',

        onboarding_disabled: false,
        joined_at: undefined,
        user_roles: [],
    });

    function submit(e) {
        e.preventDefault();
        post(route('singers.store'));
    }

    function setUser(value) {
        if(typeof value === 'string') {
            setData({
                ...data,
                email: value,
                user_id: null,
            }, value);
            return;
        }

        if(typeof value !== 'number') {
            return;
        }

        setData({
            ...data,
            user_id: value,
            email: null,
        });
    }

    return (
        <>
            <AppHead title="Add Singer" />
            <PageHeader
                title={'Create Singer'}
                icon="fa-users"
                breadcrumbs={[
                    { name: 'Dashboard', url: route('dash')},
                    { name: 'Singers', url: route('singers.index')},
                    { name: 'Create', url: route('singers.create')},
                ]}
            />

            <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <Form onSubmit={submit}>

                    {process.env.MIX_FEATURE_USER_SEARCH_IN_CREATE_SINGER === 'true'
                        ? (
                            <FormSection title="User Details" description=" Link a new or existing user account with this singer.">
                                <div className="sm:col-span-6">
                                    <Label label="Email address" />
                                    <GlobalUserSelect updateFn={setUser} />
                                    {errors.email && <Error>{errors.email}</Error>}
                                    {errors.user_id && <Error>{errors.user_id}</Error>}
                                </div>

                                {data.email && <>
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

                                    <div className="sm:col-span-3">
                                        <Label label="Password" forInput="password" />
                                        <TextInput type="password" name="password" value={data.password} updateFn={value => setData('password', value)} hasErrors={ !! errors['password'] } />
                                        <Help>You may leave this blank and update it later.</Help>
                                        {errors.password && <Error>{errors.password}</Error>}
                                    </div>

                                    <div className="sm:col-span-3">
                                        <Label label="Confirm password" forInput="password_confirmation" />
                                        <TextInput type="password" name="password_confirmation" value={data.password_confirmation} updateFn={value => setData('password_confirmation', value)} hasErrors={ !! errors['password_confirmation'] } />
                                        {errors.password_confirmation && <Error>{errors.password_confirmation}</Error>}
                                    </div>
                                </>}
                            </FormSection>
                        ) : (
                            <FormSection title="User Details" description="Create an account for the singer.">
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

                                <div className="sm:col-span-4">
                                    <Label label="Email address" forInput="email" />
                                    <TextInput name="email" type="email" autoComplete="email" value={data.email} updateFn={value => setData('email', value)} hasErrors={ !! errors['email'] } />
                                    {errors.email && <Error>{errors.email}</Error>}
                                </div>

                                <div className="sm:col-span-3">
                                    <Label label="Password" forInput="password" />
                                    <TextInput type="password" name="password" value={data.password} updateFn={value => setData('password', value)} hasErrors={ !! errors['password'] } />
                                    <Help>You may leave this blank and update it later.</Help>
                                    {errors.password && <Error>{errors.password}</Error>}
                                </div>

                                <div className="sm:col-span-3">
                                    <Label label="Confirm password" forInput="password_confirmation" />
                                    <TextInput type="password" name="password_confirmation" value={data.password_confirmation} updateFn={value => setData('password_confirmation', value)} hasErrors={ !! errors['password_confirmation'] } />
                                    {errors.password_confirmation && <Error>{errors.password_confirmation}</Error>}
                                </div>
                            </FormSection>
                        )}

                    <FormSection title="Singer Details" description="Start adding information about the singer's membership.">
                        <div className="sm:col-span-3">
                            <Label label="Voice part" forInput="voice_part_id" />
                            <Select name="voice_part_id" options={voice_parts.map(part => ({ key: part.id, label: part.title}))} value={data.voice_part_id} updateFn={value => setData('voice_part_id', value)} />
                            {errors.voice_part_id && <Error>{errors.voice_part_id}</Error>}
                        </div>

                        <div className="sm:col-span-6">
                            <Label label="Why are you joining?" forInput="reason_for_joining" />
                            <TextInput name="reason_for_joining" value={data.reason_for_joining} updateFn={value => setData('reason_for_joining', value)} hasErrors={ !! errors['reason_for_joining'] } />
                            {errors.reason_for_joining && <Error>{errors.reason_for_joining}</Error>}
                        </div>

                        <div className="sm:col-span-6">
                            <Label label="Where did you hear about us?" forInput="referrer" />
                            <TextInput name="referrer" value={data.referrer} updateFn={value => setData('referrer', value)} hasErrors={ !! errors['referrer'] } />
                            {errors.referrer && <Error>{errors.referrer}</Error>}
                        </div>

                        <div className="sm:col-span-6">
                            <Label label="Notes / Membership Details" forInput="membership_details" />
                            <TextInput name="membership_details" value={data.membership_details} updateFn={value => setData('membership_details', value)} hasErrors={ !! errors['membership_details'] } />
                            {errors.membership_details && <Error>{errors.membership_details}</Error>}
                        </div>

                        <div className="sm:col-span-6">
                            <DetailToggle
                                label="Is this an existing member?"
                                description="Onboarding will be disabled when adding an existing singer."
                                value={data.onboarding_disabled}
                                updateFn={value => setData('onboarding_disabled',  value)}
                            />
                        </div>
                    </FormSection>

                    {data.onboarding_disabled && (
                    <FormSection title="Existing Member Details">
                        <div className="sm:col-span-6">
                            <Label label="Joined" forInput="joined_at" />
                            <Date
                                name="joined_at"
                                hasErrors={ !! errors.joined_at }
                                value={data.joined_at}
                                updateFn={value => setData('joined_at', value)}
                            />
                            {errors.joined_at && <Error>{errors.joined_at}</Error>}
                        </div>

                        <fieldset className="mt-6 sm:col-span-6">
                            <legend className="text-base font-medium text-gray-900">Roles</legend>
                            <CheckboxGroup name={"user_roles"} options={roles} value={data.user_roles} updateFn={value => setData('user_roles', value)} />
                            {errors.user_roles && <Error>{errors.user_roles}</Error>}
                        </fieldset>
                    </FormSection>
                    )}

                    <FormFooter>
                        <ButtonLink href={route('singers.index')}>Cancel</ButtonLink>
                        <Button variant="primary" type="submit" className="ml-3" disabled={processing}>Save</Button>
                    </FormFooter>
                </Form>
            </div>
        </>
    );
}

Create.layout = page => <Layout children={page} />

export default Create;
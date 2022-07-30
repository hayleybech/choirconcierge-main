import React from 'react'
import Layout from "../../Layouts/Layout";
import PageHeader from "../../components/PageHeader";
import {useForm, usePage} from "@inertiajs/inertia-react";
import Label from "../../components/inputs/Label";
import TextInput from "../../components/inputs/TextInput";
import DetailToggle from "../../components/inputs/DetailToggle";
import Error from "../../components/inputs/Error";
import Select from "../../components/inputs/Select";
import FormSection from "../../components/FormSection";
import Button from "../../components/inputs/Button";
import ButtonLink from "../../components/inputs/ButtonLink";
import CheckboxGroup from "../../components/inputs/CheckboxGroup";
import AppHead from "../../components/AppHead";
import Form from "../../components/Form";
import FormFooter from "../../components/FormFooter";
import {DateTime} from "luxon";
import DayInput from "../../components/inputs/Day";
import Help from "../../components/inputs/Help";
import ButtonGroup from "../../components/inputs/ButtonGroup";
import FormWrapper from "../../components/FormWrapper";

const Edit = ({ voice_parts, roles, singer }) => {
    const { can } = usePage().props;

    const { data, setData, put, processing, errors, transform } = useForm({
        voice_part_id: singer.voice_part.id,
        reason_for_joining: singer.reason_for_joining ?? '',
        referrer: singer.referrer ?? '',
        membership_details: singer.membership_details ?? '',

        onboarding_enabled: singer.onboarding_enabled,
        joined_at: singer.joined_at ? DateTime.fromISO(singer.joined_at) : null,
        paid_until: singer.paid_until ? DateTime.fromISO(singer.paid_until) : null,
        user_roles: singer.roles.map(role => role.id),
    });

    transform((data) => ({
        ...data,
        joined_at: data.joined_at?.toISODate() ?? null,
        paid_until: data.paid_until?.toISODate() ?? null,
    }));

    function submit(e) {
        e.preventDefault();
        put(route('singers.update', singer));
    }

    function renewFor(diff) {
        setData('paid_until', data.paid_until?.plus(diff) ?? DateTime.now().plus(diff));
    }

    return (
        <>
            <AppHead title={`Edit Membership - ${singer.user.name}`} />
            <PageHeader
                title={'Edit Membership'}
                icon="fa-users"
                breadcrumbs={[
                    { name: 'Dashboard', url: route('dash')},
                    { name: 'Singers', url: route('singers.index')},
                    { name: singer.user.name, url: route('singers.show', singer)},
                    { name: 'Edit Membership', url: route('singers.edit', singer)},
                ]}
            />

            <FormWrapper>
                <Form onSubmit={submit}>

                    {can['create_singer'] && (
                    <FormSection title="Membership Details" description="Essential membership info.">
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
                                label="Enable onboarding?"
                                description="Enable this only for new/prospective singers."
                                value={data.onboarding_enabled}
                                updateFn={value => setData('onboarding_enabled',  value)}
                            />
                        </div>

                        <div className="sm:col-span-6">
                            <Label label="Joined" forInput="joined_at" />
                            <DayInput
                                name="joined_at"
                                hasErrors={ !! errors.joined_at }
                                value={data.joined_at?.toISODate() ?? ''}
                                updateFn={value => setData('joined_at', DateTime.fromISO(value) ?? null)}
                                max={DateTime.now().toISODate()}
                            />
                            {errors.joined_at && <Error>{errors.joined_at}</Error>}
                        </div>
                    </FormSection>
                    )}

                    {can['create_song'] && (
                    <FormSection title="Music Details" description="Voice part etc.">
                        <div className="sm:col-span-3">
                            <Label label="Voice part" forInput="voice_part_id" />
                            <Select name="voice_part_id" options={voice_parts.map(part => ({ key: part.id, label: part.title}))} value={data.voice_part_id} updateFn={value => setData('voice_part_id', value)} />
                            {errors.voice_part_id && <Error>{errors.voice_part_id}</Error>}
                        </div>
                    </FormSection>
                    )}

                    {can['manage_finances'] && (
                        <FormSection title="Financial Details" description="Fees etc.">
                            <div className="sm:col-span-6">
                                <Label label="Renew for" forInput="paid_until" />
                                <ButtonGroup options={[
                                    { label: '1 Month', onClick: () => renewFor({ months: 1 }) },
                                    { label: '1 Quarter', onClick: () => renewFor({ quarters: 1 }) },
                                    { label: '6 Months', onClick: () => renewFor({ months: 6 }) },
                                    { label: '1 Year', onClick: () => renewFor({ years: 1 }) },
                                ]} />
                            </div>
                            <div className="sm:col-span-6">
                                <Label label="Membership expires" forInput="paid_until" />
                                <DayInput
                                    name="paid_until"
                                    hasErrors={ !! errors.paid_until }
                                    value={data.paid_until?.toISODate() ?? ''}
                                    updateFn={value => setData('paid_until', DateTime.fromISO(value) ?? null)}
                                />
                                <Help>Manually adjust the expiry date here.</Help>
                                {errors.paid_until && <Error>{errors.paid_until}</Error>}
                            </div>
                        </FormSection>
                    )}

                    {can['create_role'] && (
                    <FormSection title="Account Details" description="Manage permissions etc.">
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
            </FormWrapper>
        </>
    );
}

Edit.layout = page => <Layout children={page} />

export default Edit;
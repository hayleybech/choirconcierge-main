import React from 'react';
import {useForm, usePage} from "@inertiajs/inertia-react";
import FormSection from "../../components/FormSection";
import Label from "../../components/inputs/Label";
import TextInput from "../../components/inputs/TextInput";
import Error from "../../components/inputs/Error";
import CheckboxGroup from "../../components/inputs/CheckboxGroup";
import RadioGroup from "../../components/inputs/RadioGroup";
import ButtonLink from "../../components/inputs/ButtonLink";
import Button from "../../components/inputs/Button";
import EmailSlugInput from "../../components/inputs/EmailSlugInput";
import SingerSelect from "../../components/inputs/SingerSelect";
import FormFooter from "../../components/FormFooter";
import Form from "../../components/Form";

const MailingListForm = ({ list, roles, voiceParts, singerCategories }) => {
    const { props: pageProps } = usePage();
    const { data, setData, post, put, processing, errors } = useForm({
        title: list?.title ?? '',
        slug: list?.slug ?? '',
        list_type: list?.list_type ?? null,
        recipient_users: list?.recipient_users.map(user => user.id) ?? [],
        recipient_roles: list?.recipient_roles.map(role => role.id) ?? [],
        recipient_voice_parts: list?.recipient_voice_parts.map(part => part.id) ?? [],
        recipient_singer_categories: list?.recipient_singer_categories.map(part => part.id) ?? [],
        sender_users: list?.sender_users.map(user => user.id) ?? [],
        sender_roles: list?.sender_roles.map(role => role.id) ?? [],
        sender_voice_parts: list?.sender_voice_parts.map(part => part.id) ?? [],
        sender_singer_categories: list?.sender_singer_categories.map(part => part.id) ?? [],
    });

    function submit(e) {
        e.preventDefault();
        list ? put(route('groups.update', list)) : post(route('groups.store'));
    }

    /**
     * Converts a title to its matching slug
     * See https://stackoverflow.com/questions/1053902/how-to-convert-a-title-to-a-url-slug-in-jquery
     */
    function toSlug(title) {
        return title
            .toLowerCase()
            .replace(/[^\w ]+/g, '')
            .replace(/ +/g, '-');
    }

    const listTypes = [
        {
            id: 'pulic',
            name: 'Public',
            icon: 'envelope-open-text',
            description: <>
                <strong>Best for: </strong> General Enquiries.<br />
                <strong>Example: </strong> Director<br />
                The general public can send to this address, and all recipients can respond.
            </>,
            disabled: false,
        },
        {
            id: 'chat',
            name: 'Chat',
            icon: 'comments',
            description: <>
                <strong>Best for: </strong> Internal communication for teams/groups.<br />
                <strong>Example: </strong> Music Team<br />
                Recipients are able to reply to all other recipients, and can compose new emails to the group.
            </>,
            disabled: false,
        },
        {
            id: 'distribution',
            name: 'Mailout',
            icon: 'paper-plane',
            description: <>
                <strong>Best for: </strong>Notifications, newsletters, reminders, etc.<br />
                <strong>Example: </strong> Active Members<br />
                Recipients can see the emails and can reply to the sender, but cannot "reply-all".
            </>,
            disabled: false,
        },
    ];

    return (
        <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <Form onSubmit={submit}>

                <FormSection title="Mailing List Details" description="Start setting up your mailing list.">
                    <div className="sm:col-span-6">
                        <Label label="Title" forInput="title" />
                        <TextInput
                            name="title"
                            value={data.title}
                            updateFn={value => setData({ ...data, title: value, slug: toSlug(value) })}
                            hasErrors={ !! errors['title'] }
                        />
                        {errors.title && <Error>{errors.title}</Error>}
                    </div>

                    <div className="sm:col-span-6">
                        <Label label="Address" forInput="slug" />
                        <EmailSlugInput
                            name="slug"
                            value={data.slug}
                            host={pageProps.tenant.host}
                            updateFn={value => setData('slug', value)}
                            hasErrors={ !! errors['slug'] }
                        />
                        {errors.slug && <Error>{errors.slug}</Error>}
                    </div>

                    <div className="sm:col-span-6">
                        <RadioGroup
                            label={<Label label="List Type" />}
                            options={listTypes}
                            selected={data.list_type}
                            setSelected={value => setData('list_type', value)}
                            vertical
                        />
                        {errors.list_type && <Error>{errors.list_type}</Error>}
                    </div>

                </FormSection>

                <FormSection title="Recipients" description="Decide who can receive emails from this address. For chat lists, these people can also send.">

                    <div className="sm:col-span-6">
                        <Label label="Singers" />
                        <SingerSelect
                            multiple
                            defaultValue={list?.recipient_users.map(user => ({
                                value: user.id,
                                label: user.name,
                                name: user.name,
                                avatarUrl: user.avatar_url,
                                email: user.email,
                                roles: user.singer.roles,
                            })) ?? null}
                            updateFn={value => setData('recipient_users', value)}
                        />
                        {errors.recipient_users && <Error>{errors.recipient_users}</Error>}
                    </div>


                    <fieldset className="sm:col-span-6">
                        <legend className="text-base font-medium text-gray-900">Roles</legend>
                        <CheckboxGroup
                            name="recipient_roles"
                            options={roles}
                            value={data.recipient_roles}
                            updateFn={value => setData('recipient_roles', value)}
                        />
                        {errors.recipient_roles && <Error>{errors.recipient_roles}</Error>}
                    </fieldset>

                    <fieldset className="sm:col-span-6">
                        <legend className="text-base font-medium text-gray-900">Voice Parts</legend>
                        <CheckboxGroup
                            name="recipient_voice_parts"
                            options={voiceParts.map(part => ({ id: part.id, name: part.title }))}
                            value={data.recipient_voice_parts}
                            updateFn={value => setData('recipient_voice_parts', value)}
                        />
                        {errors.recipient_voice_parts && <Error>{errors.recipient_voice_parts}</Error>}
                    </fieldset>

                    <fieldset className="sm:col-span-6">
                        <legend className="text-base font-medium text-gray-900">Singer Categories</legend>
                        <CheckboxGroup
                            name="recipient_singer_categories"
                            options={singerCategories}
                            value={data.recipient_singer_categories}
                            updateFn={value => setData('recipient_singer_categories', value)}
                        />
                        {errors.recipient_singer_categories && <Error>{errors.recipient_singer_categories}</Error>}
                    </fieldset>

                </FormSection>

                {data.list_type === 'distribution' && (
                    <FormSection title="Senders" description="Add people who can send emails to this address. ">
                        <div className="sm:col-span-6">
                            <Label label="Singers" />
                            <SingerSelect
                                multiple
                                defaultValue={list?.sender_users.map(user => ({
                                    value: user.id,
                                    label: user.name,
                                    name: user.name,
                                    avatarUrl: user.avatar_url,
                                    email: user.email,
                                    roles: user.singer.roles,
                                })) ?? null}
                                updateFn={value => setData('sender_users', value)}
                            />
                            {errors.sender_users && <Error>{errors.sender_users}</Error>}
                        </div>


                        <fieldset className="sm:col-span-6">
                            <legend className="text-base font-medium text-gray-900">Roles</legend>
                            <CheckboxGroup
                                name="sender_roles"
                                options={roles}
                                value={data.sender_roles}
                                updateFn={value => setData('sender_roles', value)}
                            />
                            {errors.sender_roles && <Error>{errors.sender_roles}</Error>}
                        </fieldset>

                        <fieldset className="sm:col-span-6">
                            <legend className="text-base font-medium text-gray-900">Voice Parts</legend>
                            <CheckboxGroup
                                name="sender_voice_parts"
                                options={voiceParts.map(part => ({ id: part.id, name: part.title }))}
                                value={data.sender_voice_parts}
                                updateFn={value => setData('sender_voice_parts', value)}
                            />
                            {errors.sender_voice_parts && <Error>{errors.sender_voice_parts}</Error>}
                        </fieldset>

                        <fieldset className="sm:col-span-6">
                            <legend className="text-base font-medium text-gray-900">Singer Categories</legend>
                            <CheckboxGroup
                                name="sender_singer_categories"
                                options={singerCategories}
                                value={data.sender_singer_categories}
                                updateFn={value => setData('sender_singer_categories', value)}
                            />
                            {errors.sender_singer_categories && <Error>{errors.sender_singer_categories}</Error>}
                        </fieldset>
                    </FormSection>
                )}

                <FormFooter>
                    <ButtonLink href={list ? route('groups.show', list) : route('groups.index')}>Cancel</ButtonLink>
                    <Button variant="primary" type="submit" className="ml-3" disabled={processing}>Save</Button>
                </FormFooter>
            </Form>
        </div>
    );
}

export default MailingListForm;
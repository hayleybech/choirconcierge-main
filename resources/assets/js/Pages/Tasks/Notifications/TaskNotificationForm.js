import React from 'react';
import {useForm} from "@inertiajs/inertia-react";
import Form from "../../../components/Form";
import FormSection from "../../../components/FormSection";
import Label from "../../../components/inputs/Label";
import TextInput from "../../../components/inputs/TextInput";
import Error from "../../../components/inputs/Error";
import Help from "../../../components/inputs/Help";
import FormFooter from "../../../components/FormFooter";
import ButtonLink from "../../../components/inputs/ButtonLink";
import Button from "../../../components/inputs/Button";
import SnippetTag from "../../../components/SnippetTag";
import RichTextInput from "../../../components/inputs/RichTextInput";
import FormWrapper from "../../../components/FormWrapper";

const TaskNotificationForm = ({ task, notification }) => {
    const { data, setData, post, put, processing, errors } = useForm({
        subject: notification?.subject ?? '',
        recipients: notification?.recipients ?? '',
        body: notification?.body ?? '',
        delay: notification?.delay ?? '',
    });

    function submit(e) {
        e.preventDefault();
        notification ? put(route('tasks.notifications.update', [task, notification])) : post(route('tasks.notifications.store', task));
    }

    return (
        <FormWrapper>
            <Form onSubmit={submit}>

                <FormSection title="Notification Details">
                    <div className="sm:col-span-6">
                        <Label label="Subject" forInput="subject" />
                        <TextInput name="subject" value={data.subject} updateFn={value => setData('subject', value)} hasErrors={ !! errors['subject'] } />
                        {errors.subject && <Error>{errors.subject}</Error>}
                    </div>

                    <div className="sm:col-span-6">
                        <Label label="Recipient(s)" forInput="recipients" />
                        <TextInput name="recipients" value={data.recipients} updateFn={value => setData('recipients', value)} hasErrors={ !! errors['recipients'] } />
                        <Help>e.g. The singer: <SnippetTag>singer:0</SnippetTag>, a user role: <SnippetTag>role:1</SnippetTag>, a specific user: <SnippetTag>user:1</SnippetTag>.</Help>
                        {errors.recipients && <Error>{errors.recipients}</Error>}
                    </div>

                    <div className="sm:col-span-6">
                        <Label label="Delay" forInput="delay" />
                        <TextInput name="delay" value={data.delay} updateFn={value => setData('delay', value)} hasErrors={ !! errors['delay'] } />
                        <Help>Try something like "4 hours" or "28 days". </Help>
                        {errors.delay && <Error>{errors.delay}</Error>}
                    </div>

                    <div className="sm:col-span-4">
                        <Label label="Body" forInput="body" />
                        <RichTextInput value={data.body} updateFn={value => setData('body', value)} />
                        {errors.body && <Error>{errors.body}</Error>}
                    </div>
                    <div className="sm:col-span-2">
                        <Help>
                            <strong>You can include any of the following snippets:</strong><br />
                            Singer full name: <SnippetTag>%%singer.name%%</SnippetTag>, <br />
                            Singer first name: <SnippetTag>%%singer.fname%%</SnippetTag>, <br />
                            Singer last name: <SnippetTag>%%singer.lname%%</SnippetTag>, <br />
                            Singer email: <SnippetTag>%%singer.email%%</SnippetTag>, <br />
                            Create profile link: <SnippetTag>%%profile.create%%</SnippetTag>, <br />
                            Create placement link: <SnippetTag>%%placement.create%%</SnippetTag>, <br />
                            Choir name: <SnippetTag>%%choir.name%%</SnippetTag>, <br />
                            Recipient full name: <SnippetTag>%%user.name%%</SnippetTag>, <br />
                            Recipient first name: <SnippetTag>%%user.fname%%</SnippetTag>, <br />
                            Recipient last name: <SnippetTag>%%user.lname%%</SnippetTag>, <br />
                            <br />
                            <strong>If the singer has a member profile: </strong><br />
                            Singer DOB: <SnippetTag>%%singer.dob%%</SnippetTag>, <br />
                            Singer age: <SnippetTag>%%singer.age%%</SnippetTag>, <br />
                            Singer phone: <SnippetTag>%%singer.phone%%</SnippetTag>, <br />
                            <br />
                            <strong>If the singer has a voice placement: </strong><br />
                            Voice part: <SnippetTag>%%singer.section%%</SnippetTag>, <br />
                        </Help>
                    </div>

                </FormSection>

                <FormFooter>
                    <ButtonLink
                        href={notification ? route('tasks.notifications.show', [task, notification]) : route('tasks.show', task)}
                    >
                        Cancel
                    </ButtonLink>
                    <Button variant="primary" type="submit" className="ml-3" disabled={processing}>Save</Button>
                </FormFooter>
            </Form>
        </FormWrapper>
    );
}

export default TaskNotificationForm;
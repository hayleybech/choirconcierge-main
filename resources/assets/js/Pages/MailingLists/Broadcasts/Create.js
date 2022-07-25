import React from 'react'
import Layout from "../../../Layouts/Layout";
import PageHeader from "../../../components/PageHeader";
import AppHead from "../../../components/AppHead";
import Label from "../../../components/inputs/Label";
import TextInput from "../../../components/inputs/TextInput";
import Error from "../../../components/inputs/Error";
import {useForm} from "@inertiajs/inertia-react";
import Form from "../../../components/Form";
import FormSection from "../../../components/FormSection";
import RichTextInput from "../../../components/inputs/RichTextInput";
import ButtonLink from "../../../components/inputs/ButtonLink";
import Button from "../../../components/inputs/Button";
import FormFooter from "../../../components/FormFooter";
import MailingListSelect from "../../../components/inputs/MailingListSelect";
import Icon from "../../../components/Icon";
import ErrorAlert from "../../../components/ErrorAlert";
import FileInput from "../../../components/inputs/FileInput";

const Create = ({ lists }) => {
    const {data, setData, post, processing, errors, progress} = useForm({
        subject: '',
        body: '',
        list: null,
        attachments: [],
    });

    function submit(e) {
        e.preventDefault();
        post(route('groups.broadcasts.store'));
    }

    return (
        <>
            <AppHead title="Send Email"/>
            <PageHeader
                title="Send Email Broadcast"
                icon="inbox-out"
                breadcrumbs={[
                    {name: 'Dashboard', url: route('dash')},
                    {name: 'Mailing Lists', url: route('groups.index')},
                    {name: 'Send Email', url: route('groups.broadcasts.create')},
                ]}
            />

            <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:w-full">
                {lists.length > 0 ? (
                    <Form onSubmit={submit}>
    
                        <FormSection title="Compose email with letterhead" description="Choose a mailing list, then start writing! ">
                            <div className="sm:col-span-6">
                                <Label label="Mailing List" />
                                <MailingListSelect
                                    options={lists.map(({ title, id, ...list }) => ({ label: title, value: id, ...list }))}
                                    updateFn={value => setData('list', value)}
                                />
                                {errors.list && <Error>{errors.list}</Error>}
                            </div>
    
                            <div className="sm:col-span-6">
                                <Label label="Subject" forInput="subject"/>
                                <TextInput
                                    name="subject"
                                    value={data.subject}
                                    updateFn={value => setData({ ...data, subject: value })}
                                    hasErrors={!!errors['subject']}
                                />
                                {errors.subject && <Error>{errors.subject}</Error>}
                            </div>
    
                            <div className="sm:col-span-6">
                                <Label label="Body" forInput="body" />
                                <RichTextInput value={data.body} updateFn={value => setData(data => ({ ...data, body: value }))} />
                                {errors.body && <Error>{errors.body}</Error>}
                            </div>

                            <div className="sm:col-span-6">
                                <Label label="Attachments" forInput="attachments" />
                                <FileInput
                                    name="attachments"
                                    value={data.attachments}
                                    updateFn={value => setData('attachments', value)}
                                    multiple
                                    hasErrors={!!errors['attachments']}
                                />
                                {errors.attachments && <Error>{errors.attachments}</Error>}

                                {progress && (
                                    <div className="w-full bg-gray-200 rounded-full dark:bg-gray-700 mt-2.5">
                                        <div
                                            className="bg-purple-600 text-xs text-purple-100 text-center p-0.5 rounded-full"
                                            style={{ width: `${progress.percentage}%` }}
                                        >
                                            {progress.percentage}%
                                        </div>
                                    </div>
                                )}
                            </div>
                        </FormSection>
    
                        <FormFooter>
                            <ButtonLink href={route('groups.index')}>Cancel</ButtonLink>
                            <Button variant="primary" type="submit" className="ml-3" disabled={processing}><Icon icon="paper-plane" mr/> Send</Button>
                        </FormFooter>
                    </Form>
                ): (
                    <ErrorAlert title="No mailing lists">
                        You're not a permitted sender on any of your choir's lists! If this seems wrong, contact your admin.
                    </ErrorAlert>
                )}
            </div>
        </>
    );
}

Create.layout = page => <Layout children={page} />

export default Create;
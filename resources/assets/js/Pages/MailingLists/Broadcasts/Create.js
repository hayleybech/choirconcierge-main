import React from 'react';
import TenantLayout from '../../../Layouts/TenantLayout';
import {
	PageHeader,
	PageHeaderBreadcrumbs,
	PageHeaderContent,
	PageHeaderTitle,
} from '../../../components/PageHeader/PageHeader';
import PageTopBar, { PageTopNavigation } from '../../../components/PageTopBar';
import AppHead from '../../../components/AppHead';
import Label from '../../../components/inputs/Label';
import TextInput from '../../../components/inputs/TextInput';
import Error from '../../../components/inputs/Error';
import { useForm } from '@inertiajs/react';
import Form from '../../../components/Form';
import FormSection from '../../../components/FormSection';
import RichTextInput from '../../../components/inputs/RichTextInput';
import ButtonLink from '../../../components/inputs/ButtonLinkV2';
import Button from '../../../components/inputs/ButtonV2';
import FormFooter from '../../../components/FormFooter';
import MailingListSelect from '../../../components/inputs/MailingListSelect';
import Icon from '../../../components/Icon';
import ErrorAlert from '../../../components/ErrorAlert';
import FileInput from '../../../components/inputs/FileInput';
import useRoute from '../../../hooks/useRoute';
import TrialAntiSpamNotice from '../TrialAntiSpamNotice';

const Create = ({ lists, selectedList, setSidebarOpen, inboxEnabled }) => {
	const { route } = useRoute();
	const composeTitle = inboxEnabled ? 'Compose Message' : 'Send Broadcast';
	const listOptions = lists.map(({ title, id, ...list }) => ({
		label: title,
		value: id,
		...list,
	}));

	const { data, setData, post, processing, errors, progress } = useForm({
		subject: '',
		body: '',
		list: selectedList,
		attachments: [],
	});

	function submit(e) {
		e.preventDefault();
		post(route('communications.store'));
	}

	const breadcrumbs = [
		{ name: inboxEnabled ? 'Inbox' : 'Communications', url: route('communications.index') },
		{ name: composeTitle, url: route('communications.create') },
	];

	return (
		<>
			<AppHead title={composeTitle} />

			<TrialAntiSpamNotice />

			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation breadcrumbs={breadcrumbs}></PageTopNavigation>
			</PageTopBar>
			<PageHeader>
				<PageHeaderContent>
					<PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
					<PageHeaderTitle>
						<Icon icon="inbox-out" type="solid" className="mr-2" />
						{composeTitle}
					</PageHeaderTitle>
				</PageHeaderContent>
			</PageHeader>

			<div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:w-full">
				{lists.length > 0 ? (
					<Form onSubmit={submit}>
						<FormSection
							title={inboxEnabled ? 'Compose a message' : 'Send Broadcast'}
							description="Choose a mailing list and write your message. Your choir's letterhead is included automatically."
						>
							<div className="sm:col-span-6">
								<Label label="Mailing List" />
								<MailingListSelect
									options={listOptions}
									defaultValue={listOptions.find(option => option.value === selectedList)}
									updateFn={value => setData('list', value)}
								/>
								{errors.list && <Error>{errors.list}</Error>}
							</div>

							<div className="sm:col-span-6">
								<Label label="Subject" forInput="subject" />
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
								<RichTextInput
									value={data.body}
									updateFn={value => setData(data => ({ ...data, body: value }))}
									max={5000}
								/>
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
							<Button intent="primary" weight="solid" type="submit" className="ml-3" disabled={processing}>
								<Icon icon="paper-plane" mr /> Send
							</Button>
						</FormFooter>
					</Form>
				) : (
					<ErrorAlert title="No mailing lists">
						You're not a permitted sender on any of your choir's lists! If this seems wrong, contact your
						admin.
					</ErrorAlert>
				)}
			</div>
		</>
	);
};

Create.layout = page => <TenantLayout children={page} />;

export default Create;

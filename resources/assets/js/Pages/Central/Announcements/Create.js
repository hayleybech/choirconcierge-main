import React from 'react';
import { useForm } from '@inertiajs/react';
import CentralLayout from '../../../Layouts/CentralLayout';
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
import Form from '../../../components/Form';
import FormSection from '../../../components/FormSection';
import RichTextInput from '../../../components/inputs/RichTextInput';
import ButtonLink from '../../../components/inputs/ButtonLink';
import Button from '../../../components/inputs/Button';
import FormFooter from '../../../components/FormFooter';
import Icon from '../../../components/Icon';
import FileInput from '../../../components/inputs/FileInput';
import Select from '../../../components/inputs/Select';
import useRoute from '../../../hooks/useRoute';

const Create = ({ audiences, categories, setSidebarOpen }) => {
	const { route } = useRoute();
	const { data, setData, post, processing, errors, progress } = useForm({
		audience: 'all-users',
		category: 'product',
		subject: '',
		body: '',
		attachments: [],
	});

	function submit(event) {
		event.preventDefault();
		post(route('central.announcements.store'));
	}

	const breadcrumbs = [
		{ name: 'Dashboard', url: route('central.dash') },
		{ name: 'Product Announcements', url: route('central.announcements.create') },
	];

	return (
		<>
			<AppHead title="Product Announcement" />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation breadcrumbs={breadcrumbs} />
			</PageTopBar>
			<PageHeader>
				<PageHeaderContent>
					<PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
					<PageHeaderTitle>
						<Icon icon="bullhorn" type="solid" className="mr-2" />
						Product Announcement
					</PageHeaderTitle>
				</PageHeaderContent>
			</PageHeader>

			<div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:w-full">
				<Form onSubmit={submit}>
					<FormSection
						title="Compose an announcement"
						description="Choose one audience and one email category. Optional categories are sent only to people who subscribed."
					>
						<div className="sm:col-span-6">
							<Label label="Audience" forInput="audience" />
							<Select
								name="audience"
								options={audiences}
								value={data.audience}
								updateFn={value => setData('audience', value)}
								hasErrors={!!errors.audience}
							/>
							{errors.audience && <Error>{errors.audience}</Error>}
						</div>

						<div className="sm:col-span-6">
							<Label label="Email category" forInput="category" />
							<Select
								name="category"
								options={categories}
								value={data.category}
								updateFn={value => setData('category', value)}
								hasErrors={!!errors.category}
							/>
							{errors.category && <Error>{errors.category}</Error>}
						</div>

						<div className="sm:col-span-6">
							<Label label="Subject" forInput="subject" />
							<TextInput
								name="subject"
								value={data.subject}
								updateFn={value => setData('subject', value)}
								hasErrors={!!errors.subject}
							/>
							{errors.subject && <Error>{errors.subject}</Error>}
						</div>

						<div className="sm:col-span-6">
							<Label label="Body" forInput="body" />
							<RichTextInput
								value={data.body}
								updateFn={value => setData('body', value)}
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
								hasErrors={!!errors.attachments}
							/>
							{errors.attachments && <Error>{errors.attachments}</Error>}
							{errors['attachments.0'] && <Error>{errors['attachments.0']}</Error>}

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
						<ButtonLink href={route('central.dash')}>Cancel</ButtonLink>
						<Button variant="primary" type="submit" className="ml-3" disabled={processing}>
							<Icon icon="paper-plane" mr />
							{processing ? 'Queuing...' : 'Send'}
						</Button>
					</FormFooter>
				</Form>
			</div>
		</>
	);
};

Create.layout = page => <CentralLayout children={page} />;

export default Create;
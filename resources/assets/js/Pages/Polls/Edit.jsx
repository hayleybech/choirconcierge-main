import React from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import AppHead from '../../components/AppHead';
import {
	PageHeader,
	PageHeaderBreadcrumbs,
	PageHeaderContent,
	PageHeaderTitle,
} from '../../components/PageHeader/PageHeader';
import PageTopBar, { PageTopNavigation } from '../../components/PageTopBar';
import useRoute from '../../hooks/useRoute';
import { useForm } from '@inertiajs/react';
import TextInput from '../../components/inputs/TextInput';
import Button from '../../components/inputs/ButtonV2';
import FormWrapper from '../../components/FormWrapper';
import Form from '../../components/Form';
import FormSection from '../../components/FormSection';
import FormFooter from '../../components/FormFooter';
import Label from '../../components/inputs/Label';
import CheckboxWithLabel from '../../components/inputs/CheckboxWithLabel';
import Icon from '../../components/Icon';
import DateCalendarInput from '../../components/inputs/DateCalendarInput';
import TimeInput from '../../components/inputs/Time';
import { DateTime } from 'luxon';
import Help from '../../components/inputs/Help';
import CheckboxGroup from '../../components/inputs/CheckboxGroup';
import RichTextInput from '../../components/inputs/RichTextInput';
 
const Edit = ({ poll, ensembles = [], setSidebarOpen }) => {
	const { route } = useRoute();
	const { data, setData, post, processing, errors, transform } = useForm({
		title: poll.title || '',
		description: poll.description || '',
		can_vote_multiple: !!poll.can_vote_multiple,
		close_at: poll.close_at ? DateTime.fromISO(poll.close_at) : null,
		options: (poll.options || []).map(o => o.label),
		option_ids: (poll.options || []).map(o => o.id),
		ensemble_ids: (poll.ensembles || []).map(e => e.id),
	});

	const rawDateFormat = 'yyyy-MM-dd HH:mm:ss';

	transform(data => ({
		...data,
		close_at: data.close_at ? data.close_at.toFormat(rawDateFormat) : null,
		_method: 'PUT',
	}));

	const updateOption = (index, value) => {
		const next = [...data.options];
		next[index] = value;
		setData('options', next);
	};

	const addOption = () => {
		setData('options', [...data.options, '']);
		setData('option_ids', [...data.option_ids, null]);
	};
	const removeOption = idx => {
		setData('options', data.options.filter((_, i) => i !== idx));
		setData('option_ids', data.option_ids.filter((_, i) => i !== idx));
	};

	function setCloseAtDate(value) {
		const date = DateTime.fromJSDate(value);
		const target = data.close_at ?? DateTime.now().set({ hour: 23, minute: 59, second: 59 });
		setData(
			'close_at',
			target.set({
				year: date.year,
				month: date.month,
				day: date.day,
			})
		);
	}

	function setCloseAtTime(value) {
		const time = DateTime.fromISO(value);
		const target = data.close_at ?? DateTime.now();
		setData(
			'close_at',
			target.set({
				hour: time.hour,
				minute: time.minute,
				second: 0,
			})
		);
	}

	const submit = e => {
		e.preventDefault();
		post(route('polls.update', { poll: poll.id }));
	};

	const breadcrumbs = [
		{ name: 'Polls', url: route('polls.index') },
		{ name: poll.title, url: route('polls.show', { poll: poll.id }) },
		{ name: 'Edit Poll', url: route('polls.edit', { poll: poll.id }) },
	];

	return (
		<>
			<AppHead title={`Edit ${poll.title}`} />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation breadcrumbs={breadcrumbs}></PageTopNavigation>
			</PageTopBar>
			<PageHeader>
				<PageHeaderContent>
					<PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
					<PageHeaderTitle>
						<Icon icon="poll" type="solid" className="mr-2" />
						Edit Poll
					</PageHeaderTitle>
				</PageHeaderContent>
			</PageHeader>

			<FormWrapper>
				<Form onSubmit={submit}>
					<FormSection title="Poll Details">
						<div className="sm:col-span-6">
							<Label forInput="title">Title</Label>
							<TextInput
								name="title"
								value={data.title}
								updateFn={v => setData('title', v)}
								hasErrors={!!errors.title}
							/>
							{errors.title && <p className="text-sm text-red-600 mt-1">{errors.title}</p>}
						</div>

						<div className="sm:col-span-6">
							<Label forInput="description">Description</Label>
							<RichTextInput
								max={1000}
								value={data.description}
								updateFn={v => setData('description', v)}
								hasErrors={!!errors.description}
							/>
							{errors.description && <p className="text-sm text-red-600 mt-1">{errors.description}</p>}
						</div>

						<div className="sm:col-span-6">
							<CheckboxWithLabel
								label="Allow multiple selections"
								id="can_vote_multiple"
								name="can_vote_multiple"
								checked={!!data.can_vote_multiple}
								onChange={e => setData('can_vote_multiple', e.target.checked)}
							/>
						</div>

						<div className="sm:col-span-6 text-sm">
							<p className="font-medium text-gray-700">Anonymous voting</p>
							<p className="text-gray-500">
								{poll.is_anonymous ? 'Enabled' : 'Disabled'}; this setting cannot be changed after creation.
							</p>
						</div>

						<div className="sm:col-span-4 relative z-20">
							<Label forInput="close_at">Deadline (optional)</Label>
							<DateCalendarInput
								name="close_at"
								value={data.close_at}
								startMonth={new Date(new Date().getFullYear(), 0)}
								endMonth={new Date(new Date().getFullYear() + 1, 11)}
								updateFn={setCloseAtDate}
								hasErrors={!!errors.close_at}
							/>
							{errors.close_at && <p className="text-sm text-red-600 mt-1">{errors.close_at}</p>}
							{data.close_at && (
								<Help>Will close at {data.close_at.toLocaleString(DateTime.DATETIME_MED)}</Help>
							)}
						</div>

						<div className="sm:col-span-2">
							<Label forInput="close_at_time">Time</Label>
							<TimeInput
								name="close_at_time"
								value={data.close_at?.toLocaleString(DateTime.TIME_24_SIMPLE) ?? null}
								updateFn={setCloseAtTime}
								disabled={!data.close_at}
							/>
						</div>

						{ensembles.length > 1 && (
							<div className="sm:col-span-6">
								<Label label="Ensembles" forInput="ensemble_ids" />
								<Help>
									Only singers in these ensembles will see this poll. Leave empty for all singers.
								</Help>
								<CheckboxGroup
									name="ensemble_ids"
									options={ensembles.map(e => ({ id: e.id, name: e.name }))}
									value={data.ensemble_ids}
									updateFn={v => setData('ensemble_ids', v)}
								/>
							</div>
						)}
					</FormSection>
					<FormSection title="Options">
						<div className="sm:col-span-6">
							<div className="space-y-3">
								{data.options.map((opt, idx) => (
									<div key={idx} className="flex items-center gap-2">
										<TextInput
											name={`option-${idx}`}
											value={opt}
											updateFn={v => updateOption(idx, v)}
											wrapperClasses="flex-1"
											placeholder={`Option ${idx + 1}`}
											hasErrors={!!errors[`options.${idx}`]}
										/>
    						<Button
    							type="button"
    							intent="danger"
    							weight="outline"
    							size="md"
											onClick={() => removeOption(idx)}
											disabled={data.options.length <= 1}
										>
											<Icon icon="trash" mr />
											Remove
										</Button>
									</div>
								))}
								<Button type="button" intent="secondary" weight="solid" size="md" onClick={addOption}>
									<Icon icon="plus" mr />
									Add Option
								</Button>
							</div>
							{errors.options && <p className="text-sm text-red-600 mt-1">{errors.options}</p>}
						</div>
					</FormSection>

					<FormFooter>
						<Button href={route('polls.index')}>Cancel</Button>
						<Button
							type="submit"
							intent="primary"
							weight="solid"
							className="ml-3"
							disabled={processing}
						>
							Save
						</Button>
					</FormFooter>
				</Form>
			</FormWrapper>
		</>
	);
};

Edit.layout = page => <TenantLayout children={page} />;

export default Edit;

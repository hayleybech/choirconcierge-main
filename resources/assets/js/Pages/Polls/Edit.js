import React from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import AppHead from '../../components/AppHead';
import PageHeader from '../../components/PageHeader/PageHeader';
import useRoute from '../../hooks/useRoute';
import { useForm } from '@inertiajs/react';
import TextInput from '../../components/inputs/TextInput';
import Button from '../../components/inputs/Button';
import FormWrapper from '../../components/FormWrapper';
import Form from '../../components/Form';
import FormSection from '../../components/FormSection';
import FormFooter from '../../components/FormFooter';
import Label from '../../components/inputs/Label';
import CheckboxWithLabel from '../../components/inputs/CheckboxWithLabel';
import Icon from '../../components/Icon';
import DateInput from "../../components/inputs/Date";
import TimeInput from "../../components/inputs/Time";
import {DateTime} from "luxon";
import Help from "../../components/inputs/Help";

const Edit = ({ poll }) => {
	const { route } = useRoute();
	const { data, setData, post, processing, errors, transform } = useForm({
		title: poll.title || '',
		can_vote_multiple: !!poll.can_vote_multiple,
		close_at: poll.close_at ? DateTime.fromISO(poll.close_at) : null,
		options: (poll.options || []).map(o => o.label),
	});

	const rawDateFormat = 'yyyy-MM-dd HH:mm:ss';

	transform((data) => ({
		...data,
		close_at: data.close_at ? data.close_at.toFormat(rawDateFormat) : null,
		_method: 'PUT',
	}));

	const updateOption = (index, value) => {
		const next = [...data.options];
		next[index] = value;
		setData('options', next);
	};

	const addOption = () => setData('options', [...data.options, '']);
	const removeOption = idx => setData('options', data.options.filter((_, i) => i !== idx));

	function setCloseAtDate(value) {
		const date = DateTime.fromJSDate(value);
		const target = data.close_at ?? DateTime.now().set({ hour: 23, minute: 59, second: 59 });
		setData('close_at', target.set({
			year: date.year,
			month: date.month,
			day: date.day,
		}));
	}

	function setCloseAtTime(value) {
		const time = DateTime.fromISO(value);
		const target = data.close_at ?? DateTime.now();
		setData('close_at', target.set({
			hour: time.hour,
			minute: time.minute,
			second: 0,
		}));
	}

	const submit = e => {
		e.preventDefault();
		post(route('polls.update', { poll: poll.id }));
	};

	return (
		<>
			<AppHead title={`Edit ${poll.title}`} />
			<PageHeader
				title={`Edit Poll`}
				icon="fa-poll"
				breadcrumbs={[
					{ name: 'Dashboard', url: route('dash') },
					{ name: 'Polls', url: route('polls.index') },
					{ name: poll.title, url: route('polls.show', { poll: poll.id }) },
					{ name: 'Edit', url: route('polls.edit', { poll: poll.id }) },
				]}
			/>

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
							<CheckboxWithLabel
								label="Allow multiple selections"
								id="can_vote_multiple"
								name="can_vote_multiple"
								checked={!!data.can_vote_multiple}
								onChange={e => setData('can_vote_multiple', e.target.checked)}
							/>
						</div>

						<div className="sm:col-span-4 relative z-20">
							<Label forInput="close_at">Deadline (optional)</Label>
							<DateInput
								name="close_at"
								value={data.close_at}
								updateFn={setCloseAtDate}
								hasErrors={!!errors.close_at}
							/>
							{errors.close_at && <p className="text-sm text-red-600 mt-1">{errors.close_at}</p>}
							{data.close_at && <Help>Will close at {data.close_at.toLocaleString(DateTime.DATETIME_MED)}</Help>}
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
											variant="danger-outline"
											size="sm"
											onClick={() => removeOption(idx)}
											disabled={data.options.length <= 1}
										>
											<Icon icon="trash" mr />
											Remove
										</Button>
									</div>
								))}
								<Button type="button" variant="secondary" size="sm" onClick={addOption}>
									<Icon icon="plus" mr />
									Add Option
								</Button>
							</div>
							{errors.options && <p className="text-sm text-red-600 mt-1">{errors.options}</p>}
						</div>
					</FormSection>

					<FormFooter>
						<Button type="submit" variant="primary" disabled={processing}>
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

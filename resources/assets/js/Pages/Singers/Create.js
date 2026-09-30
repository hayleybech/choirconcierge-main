import React from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import { useForm } from '@inertiajs/react';
import Label from '../../components/inputs/Label';
import TextInput from '../../components/inputs/TextInput';
import DetailToggle from '../../components/inputs/DetailToggle';
import Error from '../../components/inputs/Error';
import Help from '../../components/inputs/Help';
import FormSection from '../../components/FormSection';
import Button from '../../components/inputs/Button';
import ButtonLink from '../../components/inputs/ButtonLink';
import CheckboxGroup from '../../components/inputs/CheckboxGroup';
import {
	PageHeader,
	PageHeaderBreadcrumbs,
	PageHeaderContent,
	PageHeaderTitle,
} from '../../components/PageHeader/PageHeader';
import AppHead from '../../components/AppHead';
import Form from '../../components/Form';
import FormFooter from '../../components/FormFooter';
import GlobalUserSelect from '../../components/inputs/GlobalUserSelect';
import FormWrapper from '../../components/FormWrapper';
import useRoute from '../../hooks/useRoute';
import PageTopBar, { PageTopNavigation } from '../../components/PageTopBar';
import Icon from '../../components/Icon';
import RadioGroup from '../../components/inputs/RadioGroup';
import Select from '../../components/inputs/Select';
import SingerStatus from '../../SingerStatus';
import VoicePartTag, { voicePartTextColourClasses } from '../../components/VoicePartTag';
import Dialog from '../../components/Dialog';

const Create = ({ voice_parts, ensembles, roles, statuses, setSidebarOpen }) => {
	const { route } = useRoute();
	const [isEnrolmentDialogOpen, setIsEnrolmentDialogOpen] = React.useState(false);
	const [selectedEnsemble, setSelectedEnsemble] = React.useState(ensembles[0]?.id ?? null);
	const [selectedVoicePart, setSelectedVoicePart] = React.useState(null);

	const { data, setData, post, processing, errors } = useForm({
		create: true,

		user_id: null,
		first_name: '',
		last_name: '',
		email: '',
		password: '',
		password_confirmation: '',

		voice_part_id: null,
		ensemble_ids: [],
		enrolments: [],
		reason_for_joining: '',
		referrer: '',
		membership_details: '',
		status: 'prospects',

		onboarding_disabled: false,
		user_roles: [],
	});

	function submit(e) {
		e.preventDefault();
		post(route('singers.store'));
	}

	function setUser(value) {
		if (typeof value === 'string') {
			setData(
				{
					...data,
					email: value,
					user_id: null,
				},
				value
			);
			return;
		}

		if (typeof value !== 'number') {
			return;
		}

		setData({
			...data,
			user_id: value,
			email: null,
		});
	}

	function addEnrolment() {
		if (!selectedEnsemble || data.enrolments.some(enrolment => enrolment.ensemble_id === selectedEnsemble)) return;
		const enrolments = [...data.enrolments, { ensemble_id: selectedEnsemble, voice_part_id: selectedVoicePart }];
		setData({ ...data, ensemble_ids: enrolments.map(enrolment => enrolment.ensemble_id), enrolments });
		setIsEnrolmentDialogOpen(false);
		setSelectedEnsemble(
			ensembles.find(ensemble => !enrolments.some(enrolment => enrolment.ensemble_id === ensemble.id))?.id ?? null
		);
		setSelectedVoicePart(null);
	}

	function removeEnrolment(ensembleId) {
		const enrolments = data.enrolments.filter(enrolment => enrolment.ensemble_id !== ensembleId);
		setData({ ...data, ensemble_ids: enrolments.map(enrolment => enrolment.ensemble_id), enrolments });
	}

	const breadcrumbs = [
		{ name: 'Singers', url: route('singers.index') },
		{ name: 'Create Singer', url: route('singers.create') },
	];

	return (
		<>
			<AppHead title="Add Singer" />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation breadcrumbs={breadcrumbs}></PageTopNavigation>
			</PageTopBar>

			<PageHeader>
				<PageHeaderContent>
					<PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
					<PageHeaderTitle>
						<Icon icon="users" type="solid" className="mr-2" /> Create Singer
					</PageHeaderTitle>
				</PageHeaderContent>
			</PageHeader>

			<FormWrapper>
				<Form onSubmit={submit}>
					<FormSection
						title="User Details"
						description=" Link a new or existing user account with this singer."
					>
						<div className="sm:col-span-6">
							<Label label="Email address" />
							<GlobalUserSelect updateFn={setUser} />
							{errors.email && <Error>{errors.email}</Error>}
							{errors.user_id && <Error>{errors.user_id}</Error>}
						</div>

						{data.email && (
							<>
								<div className="sm:col-span-3">
									<Label label="First name" forInput="first_name" />
									<TextInput
										name="first_name"
										autoComplete="given-name"
										value={data.first_name}
										updateFn={value => setData('first_name', value)}
										hasErrors={!!errors['first_name']}
									/>
									{errors.first_name && <Error>{errors.first_name}</Error>}
								</div>

								<div className="sm:col-span-3">
									<Label label="Last name" forInput="last_name" />
									<TextInput
										name="last_name"
										autoComplete="family-name"
										value={data.last_name}
										updateFn={value => setData('last_name', value)}
										hasErrors={!!errors['last_name']}
									/>
									{errors.last_name && <Error>{errors.last_name}</Error>}
								</div>

								<div className="sm:col-span-3">
									<Label label="Password" forInput="password" />
									<TextInput
										type="password"
										name="password"
										value={data.password}
										updateFn={value => setData('password', value)}
										hasErrors={!!errors['password']}
									/>
									<Help>You may leave this blank and update it later.</Help>
									{errors.password && <Error>{errors.password}</Error>}
								</div>

								<div className="sm:col-span-3">
									<Label label="Confirm password" forInput="password_confirmation" />
									<TextInput
										type="password"
										name="password_confirmation"
										value={data.password_confirmation}
										updateFn={value => setData('password_confirmation', value)}
										hasErrors={!!errors['password_confirmation']}
									/>
									{errors.password_confirmation && <Error>{errors.password_confirmation}</Error>}
								</div>
							</>
						)}
					</FormSection>

					<FormSection
						title="Singer Details"
						description="Start adding information about the singer's membership."
					>
						<div className="sm:col-span-6">
							<RadioGroup
								label={<Label label="Member status" />}
								options={statuses.map(status => ({
									id: status.id,
									name: status.name,
									textColour: new SingerStatus(status.slug).textColour,
									icon: new SingerStatus(status.slug).icon,
								}))}
								selected={data.status}
								setSelected={value => setData('status', value)}
							/>
							{errors.status && <Error>{errors.status}</Error>}
						</div>

						{ensembles.length === 1 && (
							<div className="sm:col-span-6">
								<RadioGroup
									label={<Label label="Voice part" />}
									options={voice_parts.map(part => ({
										id: part.id,
 									name: part.title,
 									textColour: voicePartTextColourClasses[part.colour] ?? voicePartTextColourClasses.gray,
 									icon: 'circle',
									}))}
									selected={data.voice_part_id}
									setSelected={value => setData('voice_part_id', value)}
								/>
								{errors.voice_part_id && <Error>{errors.voice_part_id}</Error>}
							</div>
						)}

						{ensembles.length > 1 && (
							<div className="sm:col-span-6">
								<Label label="Ensembles" />
								<table className="w-full divide-y divide-gray-200 rounded-md border border-gray-200">
									<thead className="bg-gray-50">
										<tr>
											<th className="px-3 py-2 text-left text-xs font-medium uppercase text-gray-500">
												Ensemble
											</th>
											<th className="px-3 py-2 text-left text-xs font-medium uppercase text-gray-500">
												Voice part
											</th>
											<th className="px-3 py-2">
												<span className="sr-only">Delete</span>
											</th>
										</tr>
									</thead>
									<tbody className="divide-y divide-gray-200 bg-white">
										{data.enrolments.map(enrolment => {
											const ensemble = ensembles.find(item => item.id === enrolment.ensemble_id);
											const voicePart = voice_parts.find(
												item => item.id === enrolment.voice_part_id
											);
											return (
												<tr key={enrolment.ensemble_id}>
													<td className="px-3 py-3 text-sm text-gray-700">
														{ensemble?.name}
													</td>
													<td className="px-3 py-3">
														{voicePart && (
															<VoicePartTag
																title={voicePart.title}
																colour={voicePart.colour}
															/>
														)}
													</td>
													<td className="px-3 py-3 text-right">
														<Button
															variant="danger-outline"
															size="xs"
															onClick={() => removeEnrolment(enrolment.ensemble_id)}
														>
															<Icon icon="trash" />
														</Button>
													</td>
												</tr>
											);
										})}
										<tr>
											<td colSpan="3" className="bg-gray-50 px-3 py-2">
												<Button
													variant="primary"
													size="sm"
													type="button"
													onClick={() => setIsEnrolmentDialogOpen(true)}
												>
													<Icon icon="plus" /> Add
												</Button>
											</td>
										</tr>
									</tbody>
								</table>
								{errors.enrolments && <Error>{errors.enrolments}</Error>}
							</div>
						)}
						<div className="sm:col-span-6">
							<Label label="Why are you joining?" forInput="reason_for_joining" />
							<TextInput
								name="reason_for_joining"
								value={data.reason_for_joining}
								updateFn={value => setData('reason_for_joining', value)}
								hasErrors={!!errors['reason_for_joining']}
							/>
							{errors.reason_for_joining && <Error>{errors.reason_for_joining}</Error>}
						</div>

						<div className="sm:col-span-6">
							<Label label="Where did you hear about us?" forInput="referrer" />
							<TextInput
								name="referrer"
								value={data.referrer}
								updateFn={value => setData('referrer', value)}
								hasErrors={!!errors['referrer']}
							/>
							{errors.referrer && <Error>{errors.referrer}</Error>}
						</div>

						<div className="sm:col-span-6">
							<Label label="Notes / Membership Details" forInput="membership_details" />
							<TextInput
								name="membership_details"
								value={data.membership_details}
								updateFn={value => setData('membership_details', value)}
								hasErrors={!!errors['membership_details']}
							/>
							{errors.membership_details && <Error>{errors.membership_details}</Error>}
						</div>

						<div className="sm:col-span-6">
							<DetailToggle
								label="Enable onboarding automations for this user"
								description="Automatically create onboarding tasks for this singer."
								value={!data.onboarding_disabled}
								updateFn={value => setData('onboarding_disabled', !value)}
							/>
						</div>
					</FormSection>

					<FormSection title="Roles" description="Assign roles to this singer.">
						<fieldset className="sm:col-span-6">
							<CheckboxGroup
								name={'user_roles'}
								options={roles}
								value={data.user_roles}
								updateFn={value => setData('user_roles', value)}
							/>
							{errors.user_roles && <Error>{errors.user_roles}</Error>}
						</fieldset>
					</FormSection>

					<FormFooter>
						<ButtonLink href={route('singers.index')}>Cancel</ButtonLink>
						<Button variant="primary" type="submit" className="ml-3" disabled={processing}>
							Save
						</Button>
					</FormFooter>
				</Form>
			</FormWrapper>
			<Dialog
				title="Create Enrolment"
				okLabel="Add"
				okVariant="primary"
				onOk={addEnrolment}
				isOpen={isEnrolmentDialogOpen}
				setIsOpen={setIsEnrolmentDialogOpen}
			>
				<p className="mb-2">Enrol the singer in an ensemble and assign a voice part.</p>
				<div className="mb-2">
					<Label label="Ensemble" forInput="ensemble_id" />
					<Select
						name="ensemble_id"
						options={ensembles
							.filter(
								ensemble => !data.enrolments.some(enrolment => enrolment.ensemble_id === ensemble.id)
							)
							.map(ensemble => ({ key: ensemble.id, label: ensemble.name }))}
						value={selectedEnsemble}
						updateFn={value => setSelectedEnsemble(value)}
					/>
				</div>
				<RadioGroup
					label="Select a voice part"
					options={voice_parts.map(part => ({
						id: part.id,
						name: part.title,
						textColour: voicePartTextColourClasses[part.colour] ?? voicePartTextColourClasses.gray,
						icon: 'circle',
					}))}
					selected={selectedVoicePart}
					setSelected={setSelectedVoicePart}
					vertical
				/>
			</Dialog>
		</>
	);
};

Create.layout = page => <TenantLayout children={page} />;

export default Create;

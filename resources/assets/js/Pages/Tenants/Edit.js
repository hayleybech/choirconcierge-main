import React, { useEffect, useState } from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import { PageHeader, PageHeaderTitle } from '../../components/PageHeader/PageHeader';
import PageTopBar, { PageTopNavigation } from '../../components/PageTopBar';
import { useForm } from '@inertiajs/react';
import Label from '../../components/inputs/Label';
import TextInput from '../../components/inputs/TextInput';
import Error from '../../components/inputs/Error';
import FormSection from '../../components/FormSection';
import Button from '../../components/inputs/ButtonV2';
import ButtonLink from '../../components/inputs/ButtonLinkV2';
import AppHead from '../../components/AppHead';
import Form from '../../components/Form';
import FormFooter from '../../components/FormFooter';
import SubdomainInput from '../../components/inputs/SubdomainInput';
import TimezoneSelect from '../../components/inputs/TimezoneSelect';
import AvatarUpload from '../../components/AvatarUpload';
import Help from '../../components/inputs/Help';
import FormWrapper from '../../components/FormWrapper';
import useRoute from '../../hooks/useRoute';
import Icon from '../../components/Icon';
import Dialog from '../../components/Dialog';
import SingerSelect from '../../components/inputs/SingerSelect';
import CurrentPlan from './CurrentPlan';

const Edit = ({ organisation, centralDomain, timezones, setSidebarOpen }) => {
	const [activeTab, setActiveTab] = useState('organisation');
	const tabs = [
		['organisation', 'Organisation'],
		['billing', 'Billing'],
		['ensembles', 'Ensembles'],
	];

	return (
		<>
			<AppHead title="Organisation Settings" />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation title="Organisation Settings" />
			</PageTopBar>
			<PageHeader>
				<PageHeaderTitle>
					<Icon icon="cogs" type="solid" className="mr-2" />
					Organisation Settings
				</PageHeaderTitle>
			</PageHeader>

			<FormWrapper>
				<nav
					className="mb-6 flex w-full border-b border-gray-200"
					aria-label="Organisation settings tabs"
					role="tablist"
				>
					{tabs.map(([tab, label]) => (
						<button
							key={tab}
							type="button"
							role="tab"
							id={`organisation-settings-tab-${tab}`}
							aria-controls={`organisation-settings-panel-${tab}`}
							className={`flex-1 cursor-pointer border-b-2 px-3 py-3 text-sm font-medium ${
								activeTab === tab
									? 'border-purple-600 text-purple-600'
									: 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'
							}`}
							onClick={() => setActiveTab(tab)}
							aria-selected={activeTab === tab}
						>
							{label}
						</button>
					))}
				</nav>

				<div
					role="tabpanel"
					id="organisation-settings-panel-organisation"
					aria-labelledby="organisation-settings-tab-organisation"
					hidden={activeTab !== 'organisation'}
				>
					<EditForm organisation={organisation} centralDomain={centralDomain} timezones={timezones} />
				</div>
				<div
					role="tabpanel"
					id="organisation-settings-panel-billing"
					aria-labelledby="organisation-settings-tab-billing"
					hidden={activeTab !== 'billing'}
				>
					<BillingForm organisation={organisation} />
				</div>
				<div
					role="tabpanel"
					id="organisation-settings-panel-ensembles"
					aria-labelledby="organisation-settings-tab-ensembles"
					hidden={activeTab !== 'ensembles'}
				>
					<EnsemblesList organisation={organisation} />
					<AddEnsembleForm organisation={organisation} />
				</div>
			</FormWrapper>
		</>
	);
};

Edit.layout = page => <TenantLayout children={page} />;

export default Edit;

const EditForm = ({ organisation, centralDomain, timezones }) => {
	const { route } = useRoute();

	const { data, setData, post, processing, errors } = useForm({
		tab: 'organisation',
		name: organisation.name,
		logo: null,
		primary_domain: organisation.primary_domain,
		timezone: organisation.timezone,
	});

	function submit(e) {
		e.preventDefault();
		post(route('organisation.update'), { forceFormData: true });
	}

	return (
		<Form onSubmit={submit}>
			<FormSection title="Basic Details">
				<div className="sm:col-span-6">
					<Label label="Organisation Name" forInput="name" />
					<TextInput
						name="name"
						value={data.name}
						updateFn={value => setData('name', value)}
						hasErrors={!!errors['name']}
					/>
					{errors.name && <Error>{errors.name}</Error>}
				</div>

				<div className="sm:col-span-6">
					<Label label="Organisation Logo" forInput="logo" />
					<AvatarUpload
						name="logo"
						currentImage={data.logo ? URL.createObjectURL(data.logo) : organisation.logo_url}
						isSquare={false}
						updateFn={value => setData('logo', value)}
					/>
				</div>

				<div className="sm:col-span-3">
					<Label label="Primary Subdomain" forInput="primary_domain" />
					<SubdomainInput
						name="primary_domain"
						value={data.primary_domain}
						host={centralDomain}
						updateFn={value => setData('primary_domain', value)}
						hasErrors={!!errors['primary_domain']}
					/>
					{errors.primary_domain && <Error>{errors.primary_domain}</Error>}
				</div>
				<div className="sm:col-span-3">
					<Help>
						<span className="font-bold">All subdomains</span>
						<br />
						These hang around and redirect to your primary one, so people don't get lost:
						<ul className="list-disc list-inside">
							{organisation.domains.map(({ domain }) => (
								<li key={domain}>
									{domain}.{centralDomain}
								</li>
							))}
						</ul>
					</Help>
				</div>

				<div className="sm:col-span-6">
					<Label label="Timezone" forInput="timezone" />
					<TimezoneSelect
						defaultValue={data.timezone}
						options={timezones.map(label => ({ label, value: label }))}
						updateFn={value => setData('timezone', value)}
					/>
					{errors.timezone && <Error>{errors.timezone}</Error>}
				</div>
			</FormSection>
			<FormFooter>
				<ButtonLink href={route('singers.index')}>Cancel</ButtonLink>
				<Button intent="primary" weight="solid" type="submit" className="ml-3" disabled={processing}>
					Save
				</Button>
			</FormFooter>
		</Form>
	);
};

const BillingForm = ({ organisation }) => {
	const { route } = useRoute();
	const { data, setData, post, processing, errors } = useForm({
		tab: 'billing',
		billing_user: organisation.billing_user?.id ?? null,
	});

	function submit(e) {
		e.preventDefault();
		post(route('organisation.update'));
	}

	return process.env.MIX_FEATURE_BILLING ? (
		<Form onSubmit={submit}>
			<FormSection title="Billing Details">
				<div className="sm:col-span-6">
					<Label label="Billing Contact" forInput="billing_user" />
					<SingerSelect
						defaultValue={
							organisation.billing_user
								? {
										value: organisation.billing_user.id,
										label: organisation.billing_user.name,
										name: organisation.billing_user.name,
										avatarUrl: organisation.billing_user.avatar_url,
										email: organisation.billing_user.email,
										roles: organisation.billing_user.membership.roles,
								  }
								: null
						}
						updateFn={value => setData('billing_user', value)}
					/>
					{errors.billing_user && <Error>{errors.billing_user}</Error>}
				</div>

				<div className="sm:col-span-6">
					<Label label="Current Plan" />
					<CurrentPlan
						plan={organisation.plan}
						billing={organisation.billing_status}
						tenantId={organisation.id}
					/>
				</div>
			</FormSection>

			<FormFooter>
				<ButtonLink href={route('singers.index')}>Cancel</ButtonLink>
				<Button intent="primary" weight="solid" type="submit" className="ml-3" disabled={processing}>
					Save
				</Button>
			</FormFooter>
		</Form>
	) : (
		<FormSection title="Billing Details">
			<div className="sm:col-span-6">Billing is not enabled for this organisation.</div>
		</FormSection>
	);
};

const EnsemblesList = ({ organisation }) => {
	const [editingEnsemble, setEditingEnsemble] = useState(null);
	const [deletingEnsemble, setDeletingEnsemble] = useState(null);

	return (
		<>
			<FormSection title="Ensembles">
				{organisation.ensembles.length > 0 && (
					<ul className="sm:col-span-6 divide-y divide-gray-200">
						{organisation.ensembles.map(ensemble => (
							<li key={ensemble.id} className="py-4 flex space-x-4 justify-between items-center">
								<div className="flex space-x-4 items-center">
									{ensemble.logo && (
										<img
											src={ensemble.logo_url}
											alt={ensemble.name}
											className="max-h-10 w-auto shrink"
										/>
									)}
									<div>{ensemble.name}</div>
								</div>
								<div className="flex gap-2">
									<Button
										intent="primary"
										weight="solid"
										size="sm"
										onClick={() => setEditingEnsemble(ensemble)}
									>
										<Icon icon="edit" />
										Edit
									</Button>
									<Button
										intent="danger"
										weight="solid"
										size="sm"
										onClick={() => setDeletingEnsemble(ensemble)}
									>
										<Icon icon="trash" />
										Delete
									</Button>
								</div>
							</li>
						))}
					</ul>
				)}
			</FormSection>

			<EditEnsembleDialog
				isOpen={!!editingEnsemble}
				setIsOpen={setEditingEnsemble}
				organisation={organisation}
				ensemble={editingEnsemble}
			/>
			<DeleteEnsembleDialog
				isOpen={!!deletingEnsemble}
				setIsOpen={setDeletingEnsemble}
				organisation={organisation}
				ensemble={deletingEnsemble}
			/>
		</>
	);
};

const EditEnsembleDialog = ({ isOpen, setIsOpen, organisation, ensemble }) => {
	const { route } = useRoute();

	const { data, setData, post, errors } = useForm({
		_method: 'put',
		name: ensemble?.name ?? '',
		logo: null,
	});

	useEffect(() => {
		setData('name', ensemble?.name ?? '');
	}, [ensemble, setData]);

	function submit(e) {
		e.preventDefault();

		post(route('organisations.ensembles.update', { organisation, ensemble }), {
			onSuccess: () => setIsOpen(null),
		});
	}

	return (
		<Dialog
			title="Edit ensemble"
			okLabel="Save"
			onOk={submit}
			okColour="primary"
			isOpen={isOpen}
			setIsOpen={setIsOpen}
		>
			<Form onSubmit={submit}>
				<div className="flex flex-col gap-y-6">
					<div>
						<Label label="Name" forInput="name" />
						<TextInput
							name="name"
							value={data.name}
							updateFn={value => setData('name', value)}
							hasErrors={!!errors['name']}
						/>
						{errors.name && <Error>{errors.name}</Error>}
					</div>

					<div>
						<Label label="Ensemble Logo" forInput="logo" />
						<AvatarUpload
							name="logo_edit"
							currentImage={data.logo ? URL.createObjectURL(data.logo) : ensemble?.logo_url}
							isSquare={false}
							updateFn={value => setData('logo', value)}
						/>
					</div>
				</div>
			</Form>
		</Dialog>
	);
};

const DeleteEnsembleDialog = ({ isOpen, setIsOpen, organisation, ensemble }) => {
	const { route } = useRoute();
	const { data, setData, delete: deleteRequest, errors, processing } = useForm({
		confirmation: '',
	});

	useEffect(() => {
		setData('confirmation', '');
	}, [ensemble, setData]);

	function destroy(e) {
		e.preventDefault();
		deleteRequest(route('organisations.ensembles.destroy', { organisation, ensemble }), {
			onSuccess: () => setIsOpen(null),
		});
	}

	return (
		<Dialog
			title="Delete ensemble"
			okLabel="Delete ensemble"
			onOk={destroy}
			okColour="danger"
			isOpen={isOpen}
			setIsOpen={setIsOpen}
			processing={processing}
		>
			<Form onSubmit={destroy}>
				<div className="flex flex-col gap-y-4">
					<p>
						This will permanently delete {ensemble?.name} and remove its {ensemble?.enrolments_count ?? 0}{' '}
						members from the ensemble.
					</p>
					<div>
						<Label label="Type DELETE to confirm" forInput="confirmation" />
						<TextInput
							name="confirmation"
							value={data.confirmation}
							updateFn={value => setData('confirmation', value)}
						/>
						{errors.confirmation && <Error>{errors.confirmation}</Error>}
					</div>
				</div>
			</Form>
		</Dialog>
	);
};

const AddEnsembleForm = ({ organisation }) => {
	const { route } = useRoute();
	const { data, setData, post, processing, errors, reset } = useForm({
		name: '',
		logo: null,
	});

	function submit(e) {
		e.preventDefault();
		post(route('organisations.ensembles.store', { organisation }));
	}

	return (
		<Form onSubmit={submit}>
			<FormSection title="Add Ensemble">
				<div className="sm:col-span-6">
					<Label label="Ensemble Name" forInput="name" />
					<TextInput
						name="name"
						value={data.name}
						updateFn={value => setData('name', value)}
						hasErrors={!!errors['name']}
					/>
					{errors.name && <Error>{errors.name}</Error>}
				</div>

				<div className="sm:col-span-6">
					<Label label="Ensemble Logo" forInput="ensemble_logo" />
					<AvatarUpload
						name="ensemble_logo"
						currentImage={data.logo ? URL.createObjectURL(data.logo) : null}
						isSquare={false}
						updateFn={value => setData('logo', value)}
					/>
				</div>
			</FormSection>

			<FormFooter>
				<Button intent="secondary" weight="solid" onClick={() => reset()}>
					Cancel
				</Button>
				<Button intent="primary" weight="solid" type="submit" className="ml-3" disabled={processing}>
					Save
				</Button>
			</FormFooter>
		</Form>
	);
};

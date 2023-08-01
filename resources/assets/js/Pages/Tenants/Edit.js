import React, {useEffect, useState} from 'react'
import TenantLayout from "../../Layouts/TenantLayout";
import PageHeader from "../../components/PageHeader";
import {useForm} from "@inertiajs/inertia-react";
import Label from "../../components/inputs/Label";
import TextInput from "../../components/inputs/TextInput";
import Error from "../../components/inputs/Error";
import FormSection from "../../components/FormSection";
import Button from "../../components/inputs/Button";
import ButtonLink from "../../components/inputs/ButtonLink";
import AppHead from "../../components/AppHead";
import Form from "../../components/Form";
import FormFooter from "../../components/FormFooter";
import SubdomainInput from "../../components/inputs/SubdomainInput";
import TimezoneSelect from "../../components/inputs/TimezoneSelect";
import AvatarUpload from "../../components/AvatarUpload";
import Help from "../../components/inputs/Help";
import FormWrapper from "../../components/FormWrapper";
import useRoute from "../../hooks/useRoute";
import Icon from "../../components/Icon";
import Dialog from "../../components/Dialog";
import SingerSelect from "../../components/inputs/SingerSelect";

const Edit = ({ organisation, centralDomain, timezones }) => {
    const { route } = useRoute();

    return (
        <>
            <AppHead title="Organisation Settings" />
            <PageHeader
                title="Organisation Settings"
                icon="cogs"
                breadcrumbs={[
                    { name: 'Dashboard', url: route('dash')},
                    { name: 'Organisation Settings', url: route('organisation.edit')},
                ]}
            />

            <EditForm organisation={organisation} centralDomain={centralDomain} timezones={timezones} />
            <EnsemblesList organisation={organisation} />
            <AddEnsembleForm organisation={organisation} />
        </>
    );
}

Edit.layout = page => <TenantLayout children={page} />

export default Edit;

const EditForm = ({ organisation, centralDomain, timezones }) => {
    const { route } = useRoute();

    const { data, setData, post, processing, errors } = useForm({
        name: organisation.name,
        logo: null,
        primary_domain: organisation.primary_domain,
        timezone: organisation.timezone.timezone,
        billing_user: organisation.billing_user ?? null,
    });

    function submit(e) {
        e.preventDefault();
        post(route('organisation.update'));
    }

    return (
        <FormWrapper>
            <Form onSubmit={submit}>

                <FormSection title="Basic Details">
                    <div className="sm:col-span-6">
                        <Label label="Organisation Name" forInput="name" />
                        <TextInput name="name" value={data.name} updateFn={value => setData('name', value)} hasErrors={ !! errors['name'] } />
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
                            hasErrors={ !! errors['primary_domain'] }
                        />
                        {errors.primary_domain && <Error>{errors.primary_domain}</Error>}
                    </div>
                    <div className="sm:col-span-3">
                        <Help>
                            <span className="font-bold">All subdomains</span><br />
                            These hang around and redirect to your primary one, so people don't get lost:
                            <ul className="list-disc list-inside">
                                {organisation.domains.map(({ domain }) => <li key={domain}>{domain}.{centralDomain}</li>)}
                            </ul>
                        </Help>
                    </div>

                    <div className="sm:col-span-6">
                        <Label label="Timezone" forInput="timezone" />
                        <TimezoneSelect
                            defaultValue={data.timezone}
                            options={timezones.map((label) => ({ label, value: label }))}
                            updateFn={value => setData('timezone', value)}
                        />
                        {errors.timezone && <Error>{errors.timezone}</Error>}
                    </div>

                    <div className="sm:col-span-6">
                        <Label label="Billing Contact" forInput="billing_user" />
                        <SingerSelect
                          defaultValue={ organisation.billing_user ? {
                              value: organisation.billing_user.id,
                              label: organisation.billing_user.name,
                              name: organisation.billing_user.name,
                              avatarUrl: organisation.billing_user.avatar_url,
                              email: organisation.billing_user.email,
                              roles: organisation.billing_user.membership.roles,
                          } : null}
                          updateFn={(value) => setData('billing_user', value)}
                        />
                        {<errors className="billing_user"></errors> && <Error>{errors.billing_user}</Error>}
                    </div>
                </FormSection>

                <FormFooter>
                    <ButtonLink href={route('singers.index')}>Cancel</ButtonLink>
                    <Button variant="primary" type="submit" className="ml-3" disabled={processing}>Save</Button>
                </FormFooter>

            </Form>
        </FormWrapper>
    );
}

const EnsemblesList = ({ organisation }) => {
    const [editingEnsemble, setEditingEnsemble] = useState(null);

    return (
        <FormWrapper>
            <FormSection title="Ensembles">
                {organisation.ensembles.length > 0 && (
                    <ul className="sm:col-span-6 divide-y divide-gray-200">
                        {organisation.ensembles.map((ensemble) => (
                            <li key={ensemble.id} className="py-4 flex space-x-4 justify-between items-center">
                                <div className="flex space-x-4 items-center">
                                    {ensemble.logo && <img src={ensemble.logo_url} alt={ensemble.name} className="max-h-10 w-auto shrink" />}
                                    <div>{ensemble.name}</div>
                                </div>
                                <Button variant="primary" size="sm" onClick={() => setEditingEnsemble(ensemble)}>
                                    <Icon icon="edit" />
                                    Edit
                                </Button>
                            </li>
                        ))}
                    </ul>
                )}
            </FormSection>

            <EditEnsembleDialog isOpen={!!editingEnsemble} setIsOpen={setEditingEnsemble} organisation={organisation} ensemble={editingEnsemble} />
        </FormWrapper>
    );
}

const EditEnsembleDialog = ({ isOpen, setIsOpen, organisation, ensemble }) => {
    const { route } = useRoute();

    const { data, setData, post, errors } = useForm({
        _method: 'put',
        name: ensemble?.name ?? '',
        logo: null,
    });

    useEffect(() => {
        setData('name', ensemble?.name ?? '');
    }, [ensemble]);

    function submit(e) {
        e.preventDefault();

        post(route('organisations.ensembles.update', {organisation, ensemble}), {
            onSuccess: () => setIsOpen(false),
        });
    }

    return (
        <Dialog
            title="Edit ensemble"
            okLabel="Save"
            onOk={submit}
            okVariant="primary"
            isOpen={isOpen}
            setIsOpen={setIsOpen}
        >
            <Form onSubmit={submit}>
                <div className="flex flex-col gap-y-6">
                    <div>
                        <Label label="Name" forInput="name" />
                        <TextInput name="name" value={data.name} updateFn={value => setData('name', value)} hasErrors={ !! errors['name'] } />
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
    )
};

const AddEnsembleForm = ({ organisation }) => {
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        logo: null,
    });

    function submit(e) {
        e.preventDefault();
        post(route('organisations.ensembles.store', {organisation}));
    }

    return (
        <FormWrapper>
            <Form onSubmit={submit}>

                <FormSection title="Add Ensemble">
                    <div className="sm:col-span-6">
                        <Label label="Ensemble Name" forInput="name" />
                        <TextInput name="name" value={data.name} updateFn={value => setData('name', value)} hasErrors={ !! errors['name'] } />
                        {errors.name && <Error>{errors.name}</Error>}
                    </div>

                    <div className="sm:col-span-6">
                        <Label label="Ensemble Logo" forInput="logo" />
                        <AvatarUpload
                            name="logo_create"
                            currentImage={data.logo ? URL.createObjectURL(data.logo) : null}
                            isSquare={false}
                            updateFn={value => setData('logo', value)}
                        />
                    </div>
                </FormSection>

                <FormFooter>
                    <Button variant="secondary" onClick={reset}>Cancel</Button>
                    <Button variant="primary" type="submit" className="ml-3" disabled={processing}>Save</Button>
                </FormFooter>

            </Form>
        </FormWrapper>
    );
}
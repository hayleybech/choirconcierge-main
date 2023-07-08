import React from 'react'
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
    const { data, setData, post, processing, errors } = useForm({
        choir_name: organisation.choir_name,
        choir_logo: null,
        primary_domain: organisation.primary_domain,
        timezone: organisation.timezone.timezone,
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
                        <Label label="Organisation Name" forInput="choir_name" />
                        <TextInput name="choir_name" value={data.choir_name} updateFn={value => setData('choir_name', value)} hasErrors={ !! errors['choir_name'] } />
                        {errors.choir_name && <Error>{errors.choir_name}</Error>}
                    </div>

                    <div className="sm:col-span-6">
                        <Label label="Organisation Logo" forInput="choir_logo" />
                        <AvatarUpload
                            name="choir_logo"
                            currentImage={data.choir_logo ? URL.createObjectURL(data.choir_logo) : organisation.logo_url}
                            isSquare={false}
                            updateFn={value => setData('choir_logo', value)}
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
                </FormSection>

                <FormFooter>
                    <ButtonLink href={route('singers.index')}>Cancel</ButtonLink>
                    <Button variant="primary" type="submit" className="ml-3" disabled={processing}>Save</Button>
                </FormFooter>

            </Form>
        </FormWrapper>
    );
}

const EnsemblesList = ({ organisation }) => (
  <FormWrapper>
      <FormSection title="Ensembles">
          {organisation.ensembles.length > 0 && (
              <ul className="sm:col-span-6 divide-y divide-gray-200">
                  {organisation.ensembles.map((ensemble) => (
                      <li key={ensemble.id} className="py-4 flex space-x-4 items-center">
                          <div>{ensemble.name}</div>
                          {ensemble.logo && <img src={ensemble.logo_url} alt={ensemble.name} className="max-h-10 w-auto shrink" />}
                      </li>
                  ))}
              </ul>
          )}
      </FormSection>
  </FormWrapper>
);

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
                            name="logo"
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
import React from 'react'
import PageHeader from "../../../components/PageHeader";
import AppHead from "../../../components/AppHead";
import useRoute from "../../../hooks/useRoute";
import CentralLayout from "../../../Layouts/CentralLayout";
import {useForm} from "@inertiajs/react";
import Form from "../../../components/Form";
import FormSection from "../../../components/FormSection";
import Label from "../../../components/inputs/Label";
import TextInput from "../../../components/inputs/TextInput";
import Error from "../../../components/inputs/Error";
import AvatarUpload from "../../../components/AvatarUpload";
import SubdomainInput from "../../../components/inputs/SubdomainInput";
import Help from "../../../components/inputs/Help";
import TimezoneSelect from "../../../components/inputs/TimezoneSelect";
import FormFooter from "../../../components/FormFooter";
import ButtonLink from "../../../components/inputs/ButtonLink";
import Button from "../../../components/inputs/Button";
import FormWrapper from "../../../components/FormWrapper";

const Create = ({ centralDomain, timezones }) => {
    const { route } = useRoute();

    const { data, setData, post, processing, errors } = useForm({
        name: '',
        logo: null,
        primary_domain: '',
        timezone: '',
        
        ensemble_name: '',
        ensemble_logo: null,
    });

    function submit(e) {
        e.preventDefault();
        post(route('central.tenants.store'));
    }

    return (
        <>
            <AppHead title="Create Organisation" />
            <PageHeader
                title="Create Organisation"
                breadcrumbs={[
                    { name: 'Dashboard', url: route('central.dash')},
                    { name: 'Organisations', url: route('central.tenants.index')},
                    { name: 'Create', url: route('central.tenants.create') },
                ]}
            />

            <FormWrapper>
                <Form onSubmit={submit}>

                    <FormSection
                        title="Basic Details"
                        description="Here, you will set up your site and enter details about your organisation. If you don't have a formal club, just use the same name and logo as your musical ensemble.">
                        <div className="sm:col-span-6">
                            <Label label="Organisation/Club Name" forInput="name" />
                            <TextInput name="name" value={data.name} updateFn={value => setData('name', value)} hasErrors={ !! errors['name'] } />
                            {errors.name && <Error>{errors.name}</Error>}
                            <Help>For smaller groups, this can be the same as your ensemble name.</Help>
                        </div>

                        <div className="sm:col-span-6">
                            <Label label="Organisation Logo" forInput="logo" />
                            <AvatarUpload
                                name="logo"
                                currentImage={data.logo ? URL.createObjectURL(data.logo) : null}
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

                    <FormSection title="Ensembles" description="Create your first group here. You will be able to make more later.">
                        <div className="sm:col-span-6">
                            <Label label="Ensemble (Choir, Quartet etc) Name" forInput="ensemble_name" />
                            <TextInput
                                name="ensemble_name"
                                value={data.ensemble_name}
                                updateFn={value => setData('ensemble_name', value)}
                                hasErrors={ !! errors['ensemble_name'] }
                            />
                            {errors.ensemble_name && <Error>{errors.ensemble_name}</Error>}
                        </div>

                        <div className="sm:col-span-6">
                            <Label label="Ensemble Logo" forInput="ensemble_logo" />
                            <AvatarUpload
                                name="ensemble_logo"
                                currentImage={data.ensemble_logo ? URL.createObjectURL(data.ensemble_logo) : null}
                                isSquare={false}
                                updateFn={value => setData('ensemble_logo', value)}
                            />
                        </div>
                    </FormSection>

                    <FormFooter>
                        <ButtonLink href={route('central.tenants.index')}>Cancel</ButtonLink>
                        <Button variant="primary" type="submit" className="ml-3" disabled={processing}>Create</Button>
                    </FormFooter>

                </Form>
            </FormWrapper>
        </>
    );
}

Create.layout = page => <CentralLayout children={page} />

export default Create;

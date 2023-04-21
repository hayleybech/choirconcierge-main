import React from 'react'
import TenantLayout from "../../Layouts/TenantLayout";
import PageHeader from "../../components/PageHeader";
import {useForm} from "@inertiajs/react";
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

const Edit = ({ tenant, centralDomain, timezones, choirLogo }) => {
    const { route } = useRoute();

    const { data, setData, post, processing, errors } = useForm({
        choir_name: tenant.choir_name,
        choir_logo: null,
        primary_domain: tenant.primary_domain,
        timezone: tenant.timezone.timezone,
    });

    function submit(e) {
        e.preventDefault();
        post(route('choir-settings.update'));
    }

    return (
        <>
            <AppHead title="Choir Settings" />
            <PageHeader
                title="Choir Settings"
                icon="cogs"
                breadcrumbs={[
                    { name: 'Dashboard', url: route('dash')},
                    { name: 'Choir Settings', url: route('choir-settings.edit')},
                ]}
            />

            <FormWrapper>
                <Form onSubmit={submit}>

                    <FormSection title="Basic Details">
                        <div className="sm:col-span-6">
                            <Label label="Choir Name" forInput="choir_name" />
                            <TextInput name="choir_name" value={data.choir_name} updateFn={value => setData('choir_name', value)} hasErrors={ !! errors['choir_name'] } />
                            {errors.choir_name && <Error>{errors.choir_name}</Error>}
                        </div>

                        <div className="sm:col-span-6">
                            <Label label="Choir Logo" forInput="choir_logo" />
                            <AvatarUpload
                                currentImage={data.choir_logo ? URL.createObjectURL(data.choir_logo) : choirLogo}
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
                                    {tenant.domains.map(({ domain }) => <li key={domain}>{domain}.{centralDomain}</li>)}
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
        </>
    );
}

Edit.layout = page => <TenantLayout children={page} />

export default Edit;
import React from 'react'
import PageHeader from "../../../components/PageHeader";
import classNames from "../../../classNames";
import {usePage} from "@inertiajs/inertia-react";
import AppHead from "../../../components/AppHead";
import DateTag from "../../../components/DateTag";
import CollapsePanel from "../../../components/CollapsePanel";
import CollapseGroup from "../../../components/CollapseGroup";
import useRoute from "../../../hooks/useRoute";
import CentralLayout from "../../../Layouts/CentralLayout";
import BillingTag from "./BillingTag";

const DetailList = ({ items, gridCols = 'sm:grid-cols-2 md:grid-cols-4' }) => (
    <dl className={classNames("grid grid-cols-1 gap-x-4 gap-y-8", gridCols)}>
        {items.map(({ label, value, colClass = "sm:col-span-1" }) => (
            <div key={label} className={colClass}>
                <dt className="text-sm font-medium text-gray-500">
                    {label}
                </dt>
                <dd className="mt-1 text-sm text-gray-900">
                    {value}
                </dd>
            </div>
        ))}
    </dl>
);

const Show = ({ tenant }) => {
    const { route } = useRoute();

    return (
        <>
            <AppHead title={`${tenant.name} - Tenants`} />
            <PageHeader
                title={tenant.name}
                image={tenant.logo_url}
                meta={[
                    tenant.timezone.timezone,
                    tenant.renews_at && <DateTag date={tenant.renews_at} label="Renews" />,
                    <DateTag date={tenant.created_at} label="Created" />,
                    <BillingTag billing={tenant.billing_status} />,
                ]}
                breadcrumbs={[
                    { name: 'Dashboard', url: route('central.dash')},
                    { name: 'Tenants', url: route('central.tenants.index')},
                    { name: tenant.name, url: route('central.tenants.show', {tenant}) },
                ]}
                actions={[
                  { label: 'Open', icon: 'sign-in-alt', url: route('dash', {tenant}), variant: 'primary' },
                ]}
            />

            <div className="grid grid-cols-1 sm:grid-cols-3 xl:grid-cols-4 divide-y divide-gray-300 sm:divide-y-0 sm:divide-x">

                <div className="sm:col-span-2 xl:col-span-3 divide-y divide-y-gray-300">
                    <CollapseGroup items={[
                        {
                            title: 'Choir Details',
                            show: true,
                            defaultOpen: true,
                            content: <ChoirDetails tenant={tenant} />,
                        },
                    ]} />
                </div>

            </div>
        </>
    );
}

Show.layout = page => <CentralLayout children={page} />

export default Show;

const ChoirDetails = ({ tenant }) => (
    <CollapsePanel>
        <DetailList items={[
            {
                label: 'Domains',
                value: tenant.domains.map(domainItem => domainItem.domain).join(),
            },
            {
                label: 'Timezone',
                value: tenant.timezone.timezone,
            },
            {
                label: 'Billing Details',
                value: <div className="text-gray-600">
                  <span className="font-bold">Current Plan:</span> {tenant.plan?.name ?? 'None'}<br />
                  <span className="font-bold">Billing Status:</span> <BillingTag billing={tenant.billing_status} /><br />
                  <span className="font-bold">Active Users:</span> {tenant.billing_status.activeUserQuota.activeUserCount} / {tenant.billing_status.activeUserQuota.quota}
                </div>,
            },
        ]}/>
    </CollapsePanel>
);
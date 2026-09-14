import React from 'react';

import {
	PageHeader,
	PageHeaderActions,
	PageHeaderBreadcrumbs,
	PageHeaderContent,
	PageHeaderMeta,
	PageHeaderTitle,
} from '../../../components/PageHeader/PageHeader';
import PageTopBar, { PageActionsMenu, PageTopNavigation } from '../../../components/PageTopBar';
import ActionMenuItem from '../../../components/ActionMenu/ActionMenuItem';
import Button from '../../../components/inputs/Button';
import Icon from '../../../components/Icon';
import classNames from '../../../classNames';
import AppHead from '../../../components/AppHead';
import DateTag from '../../../components/DateTag';
import SimplePanel from '../../../components/SimplePanel';
import SectionLayout from '../../Singers/SectionLayout';
import useRoute from '../../../hooks/useRoute';
import CentralLayout from '../../../Layouts/CentralLayout';
import BillingTag from './BillingTag';
import Badge from '../../../components/Badge';
import { usePage } from '@inertiajs/react';
import DomainTag from '../../../components/DomainTag';

const DetailList = ({ items, gridCols = 'sm:grid-cols-2 md:grid-cols-4' }) => (
	<dl className={classNames('grid grid-cols-1 gap-x-4 gap-y-8', gridCols)}>
		{items.map(({ label, value, colClass = 'sm:col-span-1' }) => (
			<div key={label} className={colClass}>
				<dt className="text-sm font-medium text-gray-500">{label}</dt>
				<dd className="mt-1 text-sm text-gray-900">{value}</dd>
			</div>
		))}
	</dl>
);

const Show = ({ tenant, setSidebarOpen }) => {
	const { route } = useRoute();
	const { can } = usePage().props;
	const breadcrumbs = [
		{ name: 'Tenants', url: route('central.tenants.index') },
		{ name: tenant.name, url: route('central.tenants.show', { tenant }) },
	];
	const actions = [
		tenant.setup_done && { label: 'Open', icon: 'sign-in-alt', url: route('dash', { tenant }), variant: 'primary' },
		!tenant.had_demo && can.list_tenants && {
			label: 'Demo done',
			icon: 'check',
			url: route('central.tenants.track-demo', { tenant }),
			variant: 'secondary',
		},
		tenant.billing_status.hasExpiredTrial && {
			label: 'Reset Trial',
			icon: 'undo',
			url: route('central.tenants.trial.update', { tenant }),
			variant: 'secondary',
		},
	].filter(action => !!action);

	return (
		<>
			<AppHead title={`${tenant.name} - Tenants`} />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation breadcrumbs={breadcrumbs}>
					<PageActionsMenu>
						{actions.map(action => (
							<ActionMenuItem key={action.label} {...action}>
								<Icon icon={action.icon} mr />
								{action.label}
							</ActionMenuItem>
						))}
					</PageActionsMenu>
				</PageTopNavigation>
			</PageTopBar>
			<PageHeader>
				<PageHeaderContent>
					<PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
					<div className="flex items-center gap-4">
						{tenant.logo_url && <img src={tenant.logo_url} alt="" className="h-16 rounded-md" />}
						<PageHeaderTitle>{tenant.name}</PageHeaderTitle>
					</div>
					<PageHeaderMeta>
					<>
					<>
						<span>{tenant.timezone}</span>
						{tenant.renews_at && <DateTag date={tenant.renews_at} label="Renews" />}
						<DateTag icon="pencil" date={tenant.created_at} label="Created" />
						<div>
							<BillingTag billing={tenant.billing_status} />
						</div>
						{!tenant.setup_done && (
							<div>
								<Badge colour="bg-orange-100 text-orange-700">Setup Pending - Come back later</Badge>
							</div>
						)}
					</>
					</>
					</PageHeaderMeta>
				</PageHeaderContent>
				<PageHeaderActions>
					{actions.map(action => (
						<Button key={action.label} href={action.url} size="sm" variant={action.variant}>
							<Icon icon={action.icon} mr />
							{action.label}
						</Button>
					))}
				</PageHeaderActions>
			</PageHeader>

			<SectionLayout
				columns={[
					[
						{
							title: 'Choir Details',
							content: <ChoirDetails tenant={tenant} showSales={can.list_tenants} />,
						},
					],
				]}
			/>
		</>
	);
};

Show.layout = page => <CentralLayout children={page} />;

export default Show;

const ChoirDetails = ({ tenant, showSales }) => (
	<SimplePanel>
		<DetailList
			items={[
				{
					label: 'Domains',
					value: (
						<div className="flex gap-1 flex-wrap">
							{tenant.domains.map(domainItem => (
								<DomainTag>{domainItem.domain}</DomainTag>
							))}
						</div>
					),
				},
				{
					label: 'Timezone',
					value: tenant.timezone,
				},
				{
					label: 'Billing Details',
					value: (
						<div className="text-gray-600">
							<p>
								<span className="font-bold">Current Plan:</span> {tenant.plan?.name ?? 'None'}
							</p>
							<p>
								<span className="font-bold">Billing Status:</span>{' '}
								<BillingTag billing={tenant.billing_status} />
							</p>
							<p>
								<span className="font-bold">Active Users:</span>{' '}
								{tenant.billing_status.activeUserQuota.activeUserCount} /{' '}
								{tenant.billing_status.activeUserQuota.quota}
							</p>
							{tenant.billing_status.onTrial && (
								<div className="flex gap-1">
									<span className="font-bold">Trial Expires:</span>{' '}
									<DateTag date={tenant.billing_status.trialEndsAt} />
									<br />
								</div>
							)}
						</div>
					),
				},
				showSales
					? {
							label: 'Sales Details',
							value: (
								<div className="text-gray-600">
									<span className="font-bold">Demo Booked:</span> {tenant.had_demo ? 'Yes' : 'No'}
									<br />
								</div>
							),
					  }
					: undefined,
			].filter(item => !!item)}
		/>
	</SimplePanel>
);

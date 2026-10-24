import React from 'react';
import Panel from '../../../components/Panel';

const TenantStatsWidget = ({
	activeTenants,
	tenantsOnTrial,
	tenantsTrialExpired,
	activeMembers,
	trialConversionRate,
	medianRetentionTime,
	panel = true,
}) => {
	const content = (
		<div className="w-full">
			<dl className="mx-auto grid w-full grid-cols-2 px-6 sm:px-6 md:px-8 py-4 lg:grid-cols-3 xl:grid-cols-6 gap-x-4 gap-y-10 sm:gap-x-6 xl:gap-x-8 justify-between">
				<div className="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2">
					<dt className="text-sm font-medium leading-6 text-gray-500">Active Tenants</dt>
					<dd className="w-full flex-none text-3xl font-medium leading-10 tracking-tight text-gray-900">
						{activeTenants}
					</dd>
					<div className="text-xs text-gray-400">Active Plan, Trial, Gratis, Grace Period, etc</div>
				</div>
				<div className="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2">
					<dt className="text-sm font-medium leading-6 text-gray-500">Tenants On Trial</dt>
					<dd className="w-full flex-none text-3xl font-medium leading-10 tracking-tight text-gray-900">
						{tenantsOnTrial}
					</dd>
				</div>
				<div className="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2">
					<dt className="text-sm font-medium leading-6 text-gray-500">Tenants with Expired Trial</dt>
					<dd className="w-full flex-none text-3xl font-medium leading-10 tracking-tight text-gray-900">
						{tenantsTrialExpired}
					</dd>
				</div>
				<div className="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2">
					<dt className="text-sm font-medium leading-6 text-gray-500">Active Members</dt>
					<dd className="w-full flex-none text-3xl font-medium leading-10 tracking-tight text-gray-900">
						{activeMembers}
					</dd>
					<div className="text-xs text-gray-400">Active Members of Active Tenants</div>
				</div>
				<div className="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2">
					<dt className="text-sm font-medium leading-6 text-gray-500">Trial Conversion Rate</dt>
					<dd className="w-full flex-none text-3xl font-medium leading-10 tracking-tight text-gray-900">
						{trialConversionRate}%
					</dd>
				</div>
				<div className="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2">
					<dt className="text-sm font-medium leading-6 text-gray-500">Median Retention Time</dt>
					<dd className="w-full flex-none text-3xl font-medium leading-10 tracking-tight text-gray-900">
						{medianRetentionTime} months
					</dd>
					<div className="text-xs text-gray-400">Paid customers only</div>
				</div>
			</dl>
		</div>
	);

	return panel ? <Panel noPadding>{content}</Panel> : content;
};

export default TenantStatsWidget;
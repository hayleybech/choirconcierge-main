import SectionTitle from "../../../components/SectionTitle";
import React from "react";
import Panel from "../../../components/Panel";

const TenantStatsWidget = ({activeTenants, tenantsOnTrial, tenantsTrialExpired, activeMembers, trialConversionRate, medianPurchaseValue, medianRetentionTime}) => (
	<Panel noPadding>

		<dl className="mx-auto grid grid-cols-1 gap-px sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-4">
			<div
				className="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2 px-4 py-10 sm:px-6 xl:px-8">
				<dt className="text-sm font-medium leading-6 text-gray-500">Active Tenants</dt>
				<dd className="w-full flex-none text-3xl font-medium leading-10 tracking-tight text-gray-900">{activeTenants}</dd>
				<div className="text-xs text-gray-400">Active Plan, Trial, Gratis, Grace Period, etc</div>
				{/*<dd className="w-full flex-none text-3xl font-medium leading-10 tracking-tight text-gray-900">$30,156.00</dd>*/}
			</div>
			<div
				className="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2 px-4 py-10 sm:px-6 xl:px-8">
				<dt className="text-sm font-medium leading-6 text-gray-500">Tenants On Trial</dt>
				<dd className="w-full flex-none text-3xl font-medium leading-10 tracking-tight text-gray-900">{tenantsOnTrial}</dd>
			</div>
			<div
				className="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2 px-4 py-10 sm:px-6 xl:px-8">
				<dt className="text-sm font-medium leading-6 text-gray-500">Tenants with Expired Trial</dt>
				<dd className="w-full flex-none text-3xl font-medium leading-10 tracking-tight text-gray-900">{tenantsTrialExpired}</dd>
			</div>
			<div
				className="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2 px-4 py-10 sm:px-6 xl:px-8">
				<dt className="text-sm font-medium leading-6 text-gray-500">Active Members</dt>
				<dd className="w-full flex-none text-3xl font-medium leading-10 tracking-tight text-gray-900">{activeMembers}</dd>
				<div className="text-xs text-gray-400">Active Members of Active Tenants</div>
			</div>
		</dl>
		<dl className="mx-auto grid grid-cols-1 gap-px sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 border-t border-gray-100">
			<div
				className="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2 px-4 py-10 sm:px-6 xl:px-8">
				<dt className="text-sm font-medium leading-6 text-gray-500">Trial Conversion Rate</dt>
				<dd className="w-full flex-none text-3xl font-medium leading-10 tracking-tight text-gray-900">{trialConversionRate}%</dd>
			</div>
			<div
				className="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2 px-4 py-10 sm:px-6 xl:px-8">
				<dt className="text-sm font-medium leading-6 text-gray-500">Median Purchase Value</dt>
				<dd className="w-full flex-none text-3xl font-medium leading-10 tracking-tight text-gray-900">${medianPurchaseValue}</dd>
			</div>
			<div
				className="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2 px-4 py-10 sm:px-6 xl:px-8">
				<dt className="text-sm font-medium leading-6 text-gray-500">Median Retention Time</dt>
				<dd className="w-full flex-none text-3xl font-medium leading-10 tracking-tight text-gray-900">{medianRetentionTime} days</dd>
				<div className="text-xs text-gray-400">Paid customers only</div>
			</div>
		</dl>
	</Panel>
);

export default TenantStatsWidget;
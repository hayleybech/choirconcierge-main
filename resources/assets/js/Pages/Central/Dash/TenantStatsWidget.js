import SectionTitle from "../../../components/SectionTitle";
import React from "react";
import Panel from "../../../components/Panel";

const TenantStatsWidget = ({activeTenants, tenantsOnTrial, tenantsTrialExpired, activeMembers}) => (
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
				className="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2px-4 py-10 sm:px-6 xl:px-8">
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
				<dt className="text-sm font-medium leading-6 text-gray-500">Active Members (of active tenants)</dt>
				<dd className="w-full flex-none text-3xl font-medium leading-10 tracking-tight text-gray-900">{activeMembers}</dd>
			</div>
		</dl>
	</Panel>
);

export default TenantStatsWidget;
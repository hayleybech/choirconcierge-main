import React, { useEffect } from 'react';
import { usePage } from '@inertiajs/react';
import TenantLayout from "../../Layouts/TenantLayout";
import PageHeader from "../../components/PageHeader/PageHeader";
import AppHead from "../../components/AppHead";
import useRoute from "../../hooks/useRoute";
import Panel, { PanelTitle } from "../../components/Panel";
import Icon from "../../components/Icon";
import Button from "../../components/inputs/Button";
import BillingTag from "../Central/Tenants/BillingTag";
import classNames from "../../classNames";
import { DateTime } from "luxon";

const formatDate = (date, format = 'DATE_MED') => (
    DateTime.fromJSDate(new Date(date)).toLocaleString(DateTime[format])
);

const Billing = ({ plans, tenant, termsUrl }) => {
	const { route } = useRoute();
	const { plan, billing_status: billing } = tenant;

	return (
		<>
			<AppHead title="Billing" />
			<PageHeader
				title="Billing"
				icon="credit-card"
				breadcrumbs={[
					{ name: 'Dashboard', url: route('dash') },
					{ name: 'Organisation Settings', url: route('organisation.edit') },
					{ name: 'Billing', url: route('organisation.billing') },
				]}
			/>

			<div className="py-6">
				<div className="mx-auto px-4 sm:px-6 lg:px-16">
					{/* Current Plan Section */}
					<Panel
						header={
							<div className="flex justify-between items-center">
								<PanelTitle>Current Subscription</PanelTitle>
								<BillingTag billing={billing} />
							</div>
						}
					>
						{plan ? (
							<div className="flex flex-wrap justify-between items-start gap-8">
								<div>
									<div className="text-2xl font-bold text-gray-900">{plan.name}</div>
									<div className="text-gray-600 mt-1">{plan.short_description}</div>
									{billing.trialEndsAt && billing.onTrial && (
										<div className="mt-4 text-sm text-blue-700 bg-blue-50 p-2 rounded border border-blue-100">
											Trial ends on {formatDate(billing.trialEndsAt)}
										</div>
									)}
								</div>

								<div className="w-full md:w-1/3">
									<div className="flex justify-between items-end mb-1">
										<span className="text-sm font-medium text-gray-700">Active Users</span>
										<span className="text-sm text-gray-500">
											{billing.activeUserQuota.activeUserCount} / {billing.activeUserQuota.quota}
										</span>
									</div>
									<div className="overflow-hidden rounded-full bg-gray-200 h-3">
										<div
											className={classNames(
												'h-full rounded-full transition-all duration-500',
												billing.activeUserQuota.quotaExceeded
													? 'bg-red-600'
													: billing.activeUserQuota.onGracePeriod
													? 'bg-orange-500'
													: billing.activeUserQuota.quotaNearlyExceeded
													? 'bg-yellow-500'
													: 'bg-green-600'
											)}
											style={{
												width: `${Math.min(
													100,
													(billing.activeUserQuota.activeUserCount /
														billing.activeUserQuota.quota) *
														100
												)}%`,
											}}
										/>
									</div>
									{billing.activeUserQuota.onGracePeriod && (
										<p className="mt-2 text-xs text-orange-600 italic">
											Quota exceeded. Grace period ends{' '}
											{formatDate(billing.activeUserQuota.gracePeriodEndsAt)}.
										</p>
									)}
								</div>
							</div>
						) : (
							<div className="py-6 text-center">
								<p className="text-gray-500 mb-4">No active subscription found.</p>
								{billing.onTrial && (
									<p className="text-blue-600 font-medium">
										You are currently on a free trial until {formatDate(billing.trialEndsAt)}.
									</p>
								)}
							</div>
						)}
					</Panel>

					{/* Plans Selection */}
					<div className="mt-12">
						<h3 className="text-2xl font-extrabold text-gray-900 text-center mb-8">Available Plans</h3>
						<div className="grid grid-cols-1 md:grid-cols-3 gap-6">
							{plans.map(p => {
								const isCurrent = plan?.id === p.id;
								return (
									<div
										key={p.id}
										className={classNames(
											'flex flex-col border rounded-xl bg-white shadow-sm overflow-hidden',
											isCurrent
												? 'border-purple-500 ring-2 ring-purple-500 ring-opacity-50'
												: 'border-gray-200'
										)}
									>
										{isCurrent && (
											<div className="bg-purple-500 text-white text-center py-1 text-xs font-bold uppercase tracking-wider">
												Current Plan
											</div>
										)}
										<div className="p-6 flex-grow">
											<h4 className="text-xl font-bold text-gray-900">{p.name}</h4>
											<p className="mt-2 text-gray-500 text-sm leading-relaxed">
												{p.description}
											</p>
											<div className="mt-4">
												<span className="text-3xl font-extrabold text-gray-900">
													{p.short_description}
												</span>
											</div>

											<ul className="mt-6 space-y-3">
												{p.features.map((feature, i) => (
													<li key={i} className="flex items-start text-sm text-gray-600">
														<Icon
															icon="check"
															className="text-green-500 mr-2 mt-0.5 shrink-0"
															size="xs"
														/>
														<span>{feature}</span>
													</li>
												))}
											</ul>
										</div>
										<div className="p-6 bg-gray-50 border-t border-gray-100">
											<a
												href="#!"
												variant={isCurrent ? 'secondary' : 'primary'}
												className="paddle_button !w-full !justify-center !inline-flex !items-center !gap-x-1.5 !border !shadow-sm !font-medium !focus:outline-none !focus:ring-2 !focus:ring-offset-2 !focus:ring-purple-500 !bg-purple-600 !border-transparent !text-white !hover:bg-purple-700 !hover:from-purple-600 !hover:to-purple-500 !py-2 !px-4 !text-md !rounded-md !box-border !bg-gradient-to-b !from-purple-500 !to-purple-600"
												data-override={p.payLink}
											>
												{isCurrent ? 'Current Plan' : 'Subscribe'}
											</a>
										</div>
									</div>
								);
							})}
						</div>
					</div>

					{/* Footer links */}
					<div className="mt-12 text-center text-sm text-gray-500">
						<p>
							By subscribing, you agree to our{' '}
							<a
								href={termsUrl}
								target="_blank"
								rel="noopener noreferrer"
								className="text-purple-600 hover:text-purple-500 underline hover:no-underline focus:no-underline font-medium"
							>
								Terms of Service
							</a>
							.
						</p>
						<p className="mt-2">
							Questions? Contact our support team at{' '}
							<a
								href="mailto:hello@choirconcierge.com"
								className="text-purple-600 underline hover:no-underline focus:no-underline"
							>
								hello@choirconcierge.com
							</a>
						</p>
					</div>
				</div>
			</div>
		</>
	);
};

Billing.layout = page => <TenantLayout children={page} />

export default Billing;

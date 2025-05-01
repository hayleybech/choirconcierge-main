import { DateTime } from "luxon";
import React from "react";
import ButtonLink from "../../components/inputs/ButtonLink";
import useRoute from "../../hooks/useRoute";
import classNames from "../../classNames";
import BillingTag from "../Central/Tenants/BillingTag";

const formatDate = (date, format = 'DATE_MED') => (
  DateTime.fromJSDate(new Date(date)).toLocaleString(DateTime[format])
);

const CurrentPlan = ({ plan, billing, tenantId }) => {
  const { route } = useRoute(false);

  const billingLink = route('spark.portal', { type: 'tenant', id: tenantId });

  if (plan) {

    const withinQuota = billing.activeUserQuota.activeUserCount + plan.options.activeUserQuotaBuffer < billing.activeUserQuota.quota;
    const quotaPercentUsed = billing.activeUserQuota.activeUserCount / billing.activeUserQuota.quota * 100;

    return (
      <div className="border border-gray-300 rounded bg-white p-4 mt-2 flex flex-wrap justify-between items-start gap-4">
        <div>
          <BillingTag billing={billing} />
          <div className="font-bold mt-2">{plan.name}</div>
          <div className="text-gray-800">{plan.short_description}</div>
        </div>
        <div className="w-full md:w-1/3 order-last md:order-none">
          <div className="text-gray-600 text-sm">Active Users</div>
          <div className="overflow-hidden rounded-full bg-gray-200">
            <div
              className={classNames('h-2 rounded-full',
                withinQuota && 'bg-green-600',
                billing.activeUserQuota.quotaNearlyExceeded && 'bg-blue-600',
                billing.activeUserQuota.onGracePeriod && 'bg-orange-600',
                billing.activeUserQuota.quotaExceeded && 'bg-red-600',
              )}
              style={{ width: `${quotaPercentUsed}%` }}
            />
          </div>
          <div className="text-gray-600 text-sm">
            {billing.activeUserQuota.activeUserCount} / {billing.activeUserQuota.quota}
          </div>
        </div>
        <ButtonLink href={billingLink} variant="primary" size="sm" className="shrink-0">Change Plan</ButtonLink>
      </div>
    )
  }

  if (!plan && billing.onTrial) {
    return (
      <div className="border border-blue-300 rounded bg-blue-100 p-4 mt-2 flex justify-between items-start gap-4">
        <div>
          <div className="font-bold text-blue-700">30-day Trial</div>
          <div className="text-blue-800">
            You're currently on a 30-day trial. Your trial ends {formatDate(billing.trialEndsAt)}.
          </div>
        </div>
        <ButtonLink href={billingLink} variant="primary" size="sm" className="shrink-0">Select Plan</ButtonLink>
      </div>
    );
  }

  if(!plan && !billing.onTrial ) {
      return (
          <div className="border border-red-300 rounded bg-red-100 p-4 mt-2 flex justify-between items-start gap-4">
              <div>
                  <div className="font-bold text-red-700">No Plan Found</div>
                  <div className="text-red-800">
                      We couldn't find billing details for your organisation.
                  </div>
              </div>
              <ButtonLink href={billingLink} variant="primary" size="sm" className="shrink-0">Select Plan</ButtonLink>
          </div>
      )
  }
};

export default CurrentPlan;
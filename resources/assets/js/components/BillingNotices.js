import TenantNotice from "./TenantNotice";
import { DateTime } from "luxon";
import Icon from "./Icon";
import useRoute from "../hooks/useRoute";
import ButtonLink from "./inputs/ButtonLink";

const formatDate = (date, format = 'DATE_MED') => (
  DateTime.fromJSDate(new Date(date)).toLocaleString(DateTime[format])
);

const BillingNotices = ({ billing, tenantId }) => {
  // On Trial
  if(billing.onTrial) {
    return (
      <TenantNotice variant="info">
        Your organisation is on a trial until {formatDate(billing.trialEndsAt)}. <BillingLink tenantId={tenantId} />
      </TenantNotice>
    );
  }

  // On Trial - Expired
  if(billing.hasExpiredTrial) {
    return (
      <TenantNotice variant="danger">
        Your organisation's trial has expired. Your members won't be able to log in until you purchase a subscription. <BillingLink tenantId={tenantId} />
      </TenantNotice>
    );
  }

  // Past Due
  if(billing.pastDue) {
    return (
      <TenantNotice variant="danger">
        Your organisation's subscription is overdue. Only Admins and Accounts Team can log in. <BillingLink tenantId={tenantId} />
      </TenantNotice>
    );
  }

  // Cancelled - Grace Period
  if(billing.onGracePeriod) {
    return (
      <TenantNotice variant="warning">
        Your organisation's subscription has been cancelled. You may keep using the app until the end of your billing period. <BillingLink tenantId={tenantId} />
      </TenantNotice>
    );
  }

  // Cancelled - Ended
  if(billing.ended) {
    return (
      <TenantNotice variant="danger">
        Your organisation's subscription has ended. Only Admins and Accounts Team can log in. <BillingLink tenantId={tenantId} />
      </TenantNotice>
    );
  }

  // Paused - Grace Period
  if(billing.onPausedGracePeriod) {
    return (
      <TenantNotice variant="warning">
        Your organisation's subscription has been paused. You may keep using the app until the end of your billing period. <BillingLink tenantId={tenantId} />
      </TenantNotice>
    );
  }

  // Paused - Ended
  if(billing.paused) {
    return (
      <TenantNotice variant="danger">
        Your organisation's subscription has been paused. Only Admins and Accounts Team can log in. <BillingLink tenantId={tenantId} />
      </TenantNotice>
    );
  }

  // Active User Quota - Nearly Exceeded
  if(billing.activeUserQuota.quotaNearlyExceeded) {
    return (
      <TenantNotice variant="info">
        Your organisation's has almost exceeded its quota of active members. You may need to upgrade your plan soon. <BillingLink tenantId={tenantId} />
      </TenantNotice>
    );
  }

  // Active User Quota - On Grace Period
  if(billing.activeUserQuota.onGracePeriod) {
    // @todo better copy here
    return (
      <TenantNotice variant="warning">
        Your organisation's has exceeded its quota of active members. The app will function normally until {formatDate(billing.activeUserQuota.gracePeriodEndsAt)}. <BillingLink tenantId={tenantId} />
      </TenantNotice>
    );
  }

  // Active User Quota - Exceeded
  if(billing.activeUserQuota.quotaExceeded) {
    return (
      <TenantNotice variant="danger">
        Your organisation's has exceeded its quota of active members. Your members won't be able to log in until you upgrade your subscription. <BillingLink tenantId={tenantId} />
      </TenantNotice>
    );
  }

  // Fallback - No valid subscription
  if(!billing.valid) {
    return (
      <TenantNotice variant="danger">
        There is an issue with your organisation's billing. Your members won't be able to log in until you purchase a subscription. <BillingLink tenantId={tenantId} />
      </TenantNotice>
    );
  }

  return null;
};

export default BillingNotices;

const BillingLink = ({ tenantId }) => {
  const { route } = useRoute(false);

  const billingLink = route('spark.portal', { type: 'tenant', id: tenantId });
  return (
    <ButtonLink variant="secondary" size="xs" href={billingLink} className="shrink-0">
      Go to Billing
      <Icon icon="chevron-right" />
    </ButtonLink>
  );
};

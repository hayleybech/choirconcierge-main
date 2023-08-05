import classNames from "../../../classNames";

const BillingTag = ({ billing }) => {
  // On Trial
  if(billing.onTrial) {
    return <Badge variant="info">On Trial</Badge>;
  }

  // On Trial - Expired
  if(billing.hasExpiredTrial) {
    return <Badge variant="danger">Expired Trial</Badge>;
  }

  // Past Due
  if(billing.pastDue) {
    return <Badge variant="danger">Overdue</Badge>;
  }

  // Cancelled - Grace Period
  if(billing.onGracePeriod) {
    return <Badge variant="warning">Cancelling</Badge>;
  }

  // Cancelled - Ended
  if(billing.ended) {
    return <Badge variant="danger">Cancelled</Badge>;
  }

  // Paused - Grace Period
  if(billing.onPausedGracePeriod) {
    return <Badge variant="warning">Pausing</Badge>;
  }
  // Paused - Ended
  if(billing.paused) {
    return <Badge variant="danger">Paused</Badge>;
  }

  // Active User Quota - Nearly Exceeded
  if(billing.activeUserQuota.quotaNearlyExceeded) {
    return <Badge variant="info">Near Quota</Badge>;
  }

  // Active User Quota - On Grace Period
  if(billing.activeUserQuota.onGracePeriod) {
    return <Badge variant="warning">Exceeding Quota</Badge>;
  }

  // Active User Quota - Exceeded
  if(billing.activeUserQuota.quotaExceeded) {
    return <Badge variant="danger">Quota Exceeded</Badge>;
  }

  // Fallback - No valid subscription
  if(!billing.valid) {
    return <Badge variant="danger">Invalid Status</Badge>;
  }

  return <Badge variant="success">Active</Badge>;
};

export default BillingTag;

const Badge = ({ variant, children }) => (
  <span className={classNames(
    'inline-flex items-center rounded-md px-1.5 py-0.5 text-xs font-medium',
    variant === 'success' && 'bg-green-100 text-green-700',
    variant === 'info' && 'bg-blue-100 text-blue-700',
    variant === 'warning' && 'bg-orange-100 text-orange-700',
    variant === 'danger' && 'bg-red-100 text-red-700',
  )}>
    {children}
  </span>
);
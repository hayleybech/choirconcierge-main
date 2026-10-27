# 04: Manage Billing subscriptions and hosted account tasks

**What to build:** Give Paddle Billing customers app-specific plan context and self-service plan changes, while handing supported payment, invoice, and cancellation tasks to Paddle and leaving Spark customers on Spark’s existing portal.

**Blocked by:** 03: Start new tenant subscriptions on Paddle Billing.

**Status:** ready-for-agent

- [ ] Billing customers can see their current plan, interval, features, and relevant product-specific billing context in Choir Concierge.
- [ ] The customer experience links to Paddle-hosted payment-method, invoice, and cancellation flows rather than duplicating those interfaces in Choir Concierge.
- [ ] Billing customers can choose a plan change in Choir Concierge; changes take effect immediately with Paddle-calculated proration.
- [ ] Spark-owned tenants continue to use the Spark portal and do not see Billing-only monthly options or actions.
- [ ] The new customer experience does not expose pre-migration Classic receipts; Paddle Billing invoices remain available through Paddle.
- [ ] Feature tests verify tenant-appropriate portal routing, available actions, and plan-change outcomes.
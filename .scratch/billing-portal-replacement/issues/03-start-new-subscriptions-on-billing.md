# 03: Start new tenant subscriptions on Paddle Billing

**What to build:** Let new tenants choose and purchase a Paddle Billing plan with the agreed catalog and trial terms, while establishing Billing as their sole subscription owner and reflecting confirmed access through Subscriptionify.

**Blocked by:**
- 01: Prove parallel Spark and Paddle Billing compatibility.
- 02: Keep legacy and free-tenant entitlements unified.

**Status:** ready-for-agent

- [ ] New tenants can select Small, Medium, or Large with monthly or annual billing; monthly options are not offered to Spark-owned tenants.
- [ ] Existing annual prices and tier limits are preserved, and configured monthly prices make annual billing 20% less than twelve monthly payments.
- [ ] Both Billing intervals provide a 30-day trial; `FIRSTYR` is annual-only at 10% off the first annual term and stacks with annual savings for 28% total first-year savings versus twelve monthly payments.
- [ ] Paddle-hosted checkout creates the subscription, and the tenant becomes Billing-owned only for the resulting subscription; the same tenant cannot be billed by Spark concurrently.
- [ ] Confirmed Billing subscription state updates Subscriptionify so tenant access matches the purchased plan.
- [ ] Feature tests cover available plans, checkout outcomes, interval/trial/discount rules, and ownership/access results without live provider calls.
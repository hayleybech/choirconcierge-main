# 05: Daily forum digest and navigation

**What to build:** Eligible members can discover the forum from tenant navigation and receive one preference-aware daily digest with the forum activity relevant to them, delivered in the choir’s morning timezone.

**Blocked by:** 03: Reactions and followed topics; 04: Access-safe member mentions.

**Status:** ready-for-agent

- [ ] Eligible members can reach the forum through permission-gated tenant navigation.
- [ ] New-topic email is enabled by default; digest recipients are active members who can currently view the topic and have not opted out.
- [ ] Reply activity is included for topic followers, and mention activity is included only for members with mention email opted in.
- [ ] Activity is aggregated into no more than one digest per eligible member per tenant day and delivered using the tenant’s configured morning timezone.
- [ ] Authors are excluded from email about their own actions; recipient filtering respects current tenant and ensemble access and applicable email preferences.
- [ ] Feature coverage verifies digest aggregation, duplicate prevention, recipient rules, actor exclusion, and tenant-timezone scheduling.
# ADR-0001: Central Announcements and Email Preferences

- Status: Accepted
- Date: 2026-10-21

## Context

The existing Send Email Broadcast flow is tenant-scoped: a sender chooses a choir mailing list, composes a subject and rich-text body, may attach files, and sends immediately through the queue. Product announcements need a central, cross-tenant audience. The `notification-settings` prototype places email preferences in Account → Notifications, but currently keeps those values only in client-side state.

## Decision

### Central announcement flow

- Only super-admins may compose and send announcements from the central area.
- Each announcement has one audience and one communication category.
- Preserve the existing composer capabilities: subject, rich-text body, multiple attachments, and immediate queued sending. Record sent messages in the central mail log.
- Use `Choir Concierge <hayley@choirconcierge.com>` as the From identity and `hayley@choirconcierge.com` as the reply destination.
- Deliver at most one copy per user account, even when multiple choir memberships match. All users means every non-deleted account, including accounts without an active choir membership. Membership-based audiences are aggregated across choirs and include only memberships with status `Members`.
- The audience presets are All users, All active members, Choir admins, Music Team, Events Team, and Membership Team. Role-based presets require an active membership carrying that role.

### Email preferences

- Persist email preferences account-wide, applying them across all choir memberships. The first version supports email only; push preferences are out of scope.
- The Account → Notifications page manages all eight email categories and saves changes through an explicit Save action.
- Defaults apply to existing and future accounts:
  - Communications and System & Security are mandatory and cannot be disabled.
  - Songs, Events, Documents, and Product Updates are enabled by default.
  - Promotions & Offers and Community & Content are disabled by default.
- Enforce the saved preference for each matching email category. Mandatory categories bypass opt-out filtering but still respect an announcement's selected audience. Disabled Songs, Events, and Documents preferences suppress their corresponding organisation emails across all of the user's choirs.
- Every optional-category email includes a preference-management link and a no-login link that immediately unsubscribes the recipient from that category only. Mandatory-category emails are not unsubscribable.

## Consequences

- The announcement recipient set is the intersection of the selected audience and category eligibility, except that mandatory categories do not require an enabled preference.
- Account-wide preferences must be enforced by the existing organisation notification senders as well as central announcements.
- Communications remains informational and always enabled; it does not remove recipients from choir mailing-list broadcasts.
- The preference UI and persistence must preserve the prototype's mandatory-category presentation while removing the prototype-only local state and out-of-scope push controls.
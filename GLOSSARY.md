# Glossary

## Account-wide Email Preference

A user's saved choice for an email category. It belongs to the user account and applies across all of that user's choir memberships.

## Active Member

A membership whose status is `Members` (`SingerStatus::MEMBERS`). Prospects, inactive members, and former members are not active members.

## All Users

Every non-deleted user account, whether or not it has an active choir membership.

## Announcement Audience

The single cross-tenant recipient group selected for a central announcement: All users, All active members, Choir admins, Music Team, Events Team, or Membership Team. A user matching through multiple memberships receives one copy.

## Communication Category

A classification that determines which account-wide email preference applies to a message. The categories are Communications, Songs, Events, Documents, System & Security, Product Updates, Promotions & Offers, and Community & Content.

## Mandatory Email Category

A category that is always enabled and cannot be opted out of: Communications or System & Security. Its disabled preference control communicates that the category exists; it is not an opt-out.

## Product Announcement

A centrally composed email sent by a super-admin to one selected announcement audience and assigned one communication category.

## Category Unsubscribe Link

A no-login link in an optional-category email that immediately disables that category for the recipient's account. It does not unsubscribe the recipient from other categories.
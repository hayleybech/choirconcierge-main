# 04: Access-safe member mentions

**What to build:** Members can find and mention only people who can access a discussion, and each member can independently opt in to email about mentions.

**Blocked by:** 02: Topics, replies, and moderation.

**Status:** ready-for-agent

- [ ] Topic and reply editors provide an `@` member picker limited to active members who can access that discussion.
- [ ] Mention rendering resolves only valid, accessible members and does not expose inaccessible member information.
- [ ] A separate account-wide email preference controls mention email and defaults to opt-in disabled.
- [ ] Feature coverage verifies member-picker visibility boundaries and the mention preference behavior.
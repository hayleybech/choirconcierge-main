# 02: Topics, replies, and moderation

**What to build:** Members can start and participate in accessible discussions using safe Markdown, while authors and authorized moderators can manage content and moderators can control topic state.

**Blocked by:** 01: Tenant-scoped categories.

**Status:** ready-for-agent

- [ ] Active members can create topics and replies only in categories they can access; topics and replies inherit category visibility.
- [ ] Topics and replies support safely rendered Markdown.
- [ ] Replies can target a topic or a direct reply, but cannot exceed one nested reply level.
- [ ] Authors can edit or remove their own topics and replies; authorized moderators can edit, remove, and restore any topic or reply.
- [ ] Authorized moderators can lock and pin topics; locked topics reject new replies while remaining readable.
- [ ] Thread views and feature coverage verify access boundaries, ownership, moderation, locking, and reply depth.
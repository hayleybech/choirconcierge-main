Status: ready-for-agent

# Discussions Forum

## Problem Statement

Active choir members need a shared, organized place to discuss choir matters while preserving tenant and ensemble visibility boundaries. Authorized members also need moderation controls, and forum email should keep participants informed without creating excessive or unwanted messages.

## Solution

Provide a tenant-scoped forum with flat categories, category-level ensemble restrictions, topics, one-level nested replies, safe Markdown, emoji reactions, topic following, and access-filtered mentions. Use membership role abilities for category management and moderation. Aggregate eligible forum activity into one daily digest delivered in the tenant’s morning timezone.

## User Stories

1. As an active choir member, I want to browse categories available to my membership, so that I can find relevant discussions.
2. As an active choir member, I want to see choir-wide categories, so that I can participate in conversations intended for the whole choir.
3. As an ensemble member, I want to see categories restricted to my ensembles, so that ensemble conversations remain appropriately scoped.
4. As a member without access to an ensemble-restricted category, I want its topics and replies hidden, so that category visibility is respected throughout the discussion.
5. As an authorized member, I want to create, edit, and delete categories, so that the forum can remain organized.
6. As an active choir member, I want to create a topic in an accessible category, so that I can start a discussion.
7. As an active choir member, I want to format topics and replies with safely rendered Markdown, so that my messages are readable without unsafe markup.
8. As an active choir member, I want to reply to a topic, so that I can contribute to its conversation.
9. As an active choir member, I want to reply to a reply, so that I can respond directly within a conversation while keeping nesting limited.
10. As a topic author, I want to edit my own topic, so that I can correct or clarify it.
11. As a reply author, I want to edit my own reply, so that I can correct or clarify it.
12. As a topic or reply author, I want to remove my own content, so that I can withdraw something I posted.
13. As an authorized moderator, I want to edit or remove any topic or reply, so that I can address inappropriate or incorrect content.
14. As an authorized moderator, I want to restore removed content, so that removals can be reversed when appropriate.
15. As an authorized moderator, I want to lock a topic, so that further replies stop while the discussion remains readable.
16. As an authorized moderator, I want to pin a topic, so that important discussions remain prominent in their category.
17. As an active choir member, I want to react to topics and replies using an emoji, so that I can respond succinctly.
18. As a member, I want each of my emoji reactions on a target to be unique, so that reaction counts remain meaningful.
19. As an active choir member, I want to follow a topic, so that I can receive reply updates and find the topic later.
20. As an active choir member, I want a list of topics I follow, so that I can return to ongoing conversations.
21. As an active choir member, I want to mention accessible members through a member picker, so that I can address them directly without exposing inaccessible members.
22. As a member mentioned in a discussion, I want to control mention emails with a separate opt-in preference, so that direct notifications are under my control.
23. As a member who can see a category, I want new-topic emails enabled by default but subject to my email preference, so that new discussions are discoverable while I retain control.
24. As a topic follower, I want reply activity included in my digest, so that I can keep up with conversations I chose to follow.
25. As a member eligible for forum email, I want one daily digest covering eligible new topics, followed-topic replies, and opted-in mentions, so that forum activity arrives in a predictable consolidated message.
26. As a forum participant, I want authors excluded from email about their own actions, so that I do not receive redundant notifications.
27. As a member, I want forum visibility, member suggestions, and digest recipients to respect my current tenant and ensemble access, so that restricted discussion content is not disclosed.

## Implementation Decisions

- Follow the existing Laravel Eloquent, controller, policy, tenant-route, and Inertia React conventions; do not introduce a dedicated service layer.
- Scope forum content to active memberships, using the glossary definition of Active Member (`SingerStatus::MEMBERS`).
- Use flat categories; categories may be choir-wide or restricted to selected ensembles. Topics and their replies inherit category visibility.
- Store categories, topics, replies, reactions, follows, and mentions relationally. Enforce a maximum of one reply-to-reply level.
- Allow any emoji on topics and replies, with uniqueness per member, emoji, and target.
- Allow authors to edit and remove their own content; use membership role abilities for category management and moderator actions, including lock, pin, remove, and restore.
- Render safe Markdown and restrict mention lookup to members who can access the content.
- Topic following powers both a followed-topics list and reply digest eligibility.
- New-topic email is enabled by default for eligible visible members; replies are included for topic followers; mention email has a separate opt-in. Exclude the actor from their own activity notifications.
- Aggregate eligible activity into one daily digest per member, delivered in the tenant’s morning timezone and honoring email preferences and current visibility.
- Extend the existing tenant navigation, email preference, and queued notification conventions.

## Testing Decisions

- Prefer feature tests at the user-facing request/authorization seam; assert observable access, content, and email behavior rather than internal implementation details.
- Test category and inherited topic/reply visibility across tenants and ensembles, including mention suggestions and digest recipients.
- Test role-ability authorization, author ownership, moderation actions, locked-topic reply rejection, and the one-level reply limit.
- Test Markdown safety, reaction uniqueness, topic following/list behavior, preference defaults and opt-in, actor exclusion, digest aggregation, and tenant-timezone delivery.
- Use the existing Pest poll feature tests, including `PollEnsembleTest` and `PollFilterSortTest`, as prior art for visibility and feature coverage.

## Out of Scope

- Attachments, real-time updates, category following, arbitrary-depth reply trees, and features not agreed in the forum design.
- Sending separate immediate email for each event; agreed forum activity is consolidated into a daily digest.

## Further Notes

- The forum design was confirmed in the preceding discussion. Relevant existing patterns include polls, ensemble visibility, queued notifications, email preferences, tenant routes, and Inertia pages.
- Record resolved domain vocabulary in the existing `GLOSSARY.md`. Create an ADR only if implementation uncovers a consequential, hard-to-reverse decision.
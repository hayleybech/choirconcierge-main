Status: ready-for-agent

# Parallel task workflow in JetBrains Air

## Problem Statement

Agents need to work on separate tasks across unrelated projects and in parallel within one repository without editing the same checkout at the same time. The repository currently has no Air worktree setup or documented parallel-task lifecycle. Local setup guidance also conflicts with the PHP version required by Composer, tests assume MySQL, and frontend setup is not consistently limited to one package manager.

## Solution

Use JetBrains Air's native Temporary Worktree mode for isolated tasks in this repository; keep unrelated repositories as separate project roots. Configure Air to prepare each new worktree automatically with independent dependencies, a fresh local environment file, and persistent SQLite for development. Use in-memory SQLite for tests, while retaining MySQL for production, CI, and explicit engine-specific debugging.

Each task starts from the latest `main`, owns its task branch, and submits its own PR. Agents may edit overlapping files, but the workflow must make clear that worktrees prevent in-progress file overwrites—not Git merge conflicts. A task branch merges updated `main` before its review checkpoint, resolves conflicts in its own worktree, and reruns relevant checks. Human review and approval precede merging to `main`; the Air task/worktree is deleted after merge when follow-up is no longer needed. There is no batch coordinator or separate generic worktree helper in this version.

## User Stories

1. As a developer, I want to start a separate Air task in Temporary Worktree mode, so that an agent can work without modifying my current checkout.
2. As a developer, I want to choose Local mode when isolation is not needed, so that I can intentionally have an agent work in my current checkout.
3. As a developer, I want to understand that another session within the same Air task shares its workspace, so that I do not mistake it for an isolated parallel worker.
4. As a developer working across unrelated projects, I want each repository to remain its own project root, so that parallel work does not require nested worktrees or a central checkout.
5. As a task agent, I want each independent task branch to start from the latest `main`, so that tasks do not accidentally depend on unmerged work.
6. As a task agent, I want Air to prepare dependencies and local configuration automatically, so that a new worktree is ready for development without repeating setup manually.
7. As a developer, I want each worktree to have a separate dependency installation, so that package changes in one task do not affect another task.
8. As a developer, I want worktree setup to create a fresh `.env` without copying secrets from another checkout, so that credentials and machine-specific values are not propagated accidentally.
9. As a developer, I want setup to generate an application key and migrate a persistent SQLite database for that worktree, so that local development is immediately usable without creating seed data.
10. As a test author, I want tests to use in-memory SQLite separately from the persistent development database, so that test runs are isolated and fast.
11. As a developer investigating database-engine behavior, I want an explicit way to run tests against MySQL, so that SQLite compatibility does not hide MySQL-specific issues.
12. As an operator, I want production and CI to continue using MySQL, so that this local-development change does not alter deployed or canonical CI behavior.
13. As a frontend developer, I want pnpm to be the only supported package manager, so that dependency installation is consistent between worktrees and CI.
14. As a maintainer, I want accidental npm/Yarn installation paths to fail with a clear explanation, so that competing lockfiles and dependency trees do not reappear.
15. As a task agent, I want to run relevant checks, commit and push my task branch, and open or update its PR, so that the work is handed off in a reviewable form.
16. As a reviewer, I want every task PR reviewed and approved before it reaches `main`, so that parallel execution does not bypass the normal integration gate.
17. As a task agent with an open PR, I want to merge the latest `main` into my branch before requesting or updating review, so that I can resolve conflicts without rewriting published branch history.
18. As a task agent, I want to know that overlapping edits may still conflict during integration, so that I can check the merged result and rerun relevant tests.
19. As a developer, I want to retain an Air task while its PR or follow-up is active and delete it after the work is complete, so that worktrees are not removed before handoff is finished or retained indefinitely.
20. As an agent, I want `.scratch/<feature>/` claims to record task intent without requiring disjoint file ownership, so that multiple agents can work independently even when their changes touch the same files.

## Implementation Decisions

- Use Air's native Temporary Worktree workflow as the primary same-repository isolation mechanism. Do not build a duplicate standalone worktree lifecycle helper or a persistent/batch coordinator.
- Keep unrelated projects in their own repository roots. For same-repository parallel work, start a separate Air task per task and branch from the latest `main`.
- Provide repository-specific, idempotent worktree preparation through Air's supported worktree setup configuration. The setup must keep dependencies local to the worktree, create a fresh environment file from the example without copying secrets, generate a key when needed, and initialize/migrate a persistent per-worktree SQLite database without seeding a tenant.
- Use persistent SQLite for local/worktree development and in-memory SQLite for automated tests. Provide a documented explicit MySQL test/debug path. Keep production and CI on MySQL.
- Make SQLite support a real compatibility effort: address MySQL-only migration operations and query expressions while preserving MySQL semantics. Ensure a clean SQLite database can reach the application's required schema through the supported migration/setup route.
- Make pnpm the only supported JavaScript package manager. Replace npm/Yarn lock and setup paths, pin the package-manager version, update CI and setup guidance, and reject accidental npm/Yarn installation attempts with a clear message.
- Reconcile setup documentation with the Composer PHP requirement and CI runtime versions; select one compatible Node version for developer setup and CI.
- Agents test, commit, push, and open/update one PR per task. Human review and approval are required before merging to `main`. Before review, an open task merges the latest `main` into its branch, resolves conflicts there, reruns relevant checks, and pushes the result.
- Remove an Air task/worktree only after its PR is merged and no further handoff or follow-up is needed.
- Document the workflow in agent guidance and the README. Record the consequential mixed SQLite/MySQL development strategy in an ADR; update the glossary only if shared vocabulary needs a new domain term.

## Testing Decisions

- Prefer one high-level user-visible setup seam: create a clean Air Temporary Worktree, run its repository setup, verify the per-worktree environment/dependencies and persistent SQLite database, then run fresh migrations and representative tests using in-memory SQLite. The user confirmed this seam.
- Tests should assert observable behavior—successful clean schema creation, test isolation, query/search/sort results, and clear refusal of unsupported install paths—rather than internal helper implementation details.
- Use the existing Laravel `RefreshDatabase` test setup as prior art. Add focused regression coverage for SQLite migrations and the database-sensitive query paths; exercise MySQL-sensitive behavior through representative MySQL tests.
- Validate a clean pnpm install and the relevant build/test commands; verify npm and Yarn install attempts stop with the intended guidance.
- Preserve the MySQL CI run and validate the documented MySQL debug/test option separately from the SQLite-default local path.
- Review the Air setup configuration and the task/PR/cleanup instructions against Air's supported worktree behavior.

## Out of Scope

- A standalone generic Python worktree CLI, custom branch/worktree lifecycle management, or a long-running coordinator.
- Nested worktrees or a central workspace for unrelated repositories.
- A shared integration branch or automatic task-branch merging; each task uses its own PR.
- Replacing MySQL in production or CI.
- Copying credentials between checkouts, automatically seeding a local tenant, or requiring agents to have disjoint file ownership.
- Rebase-based synchronization of already-published task branches.

## Further Notes

- Current repository evidence includes npm-oriented setup and CI, both npm and Yarn lockfiles, a local PHP-version mismatch with the Composer requirement, and MySQL-specific migration/query behavior despite SQLite being configured as a connection.
- Compatibility review must include the migration that issues `ALTER TABLE ... ENGINE = InnoDB`, the migration that queries `INFORMATION_SCHEMA`, and query paths using `CONCAT(...)`.
- The private scoped registry configuration contains authentication material. Do not copy it into setup helpers, committed guidance, or new worktree environment files.
- Air's Temporary Worktree isolates working files, not eventual integration: overlapping edits can still require manual conflict resolution.
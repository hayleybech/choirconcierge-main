# 03: Prepare Air Temporary Worktrees automatically

**What to build:** When Air creates a Temporary Worktree, its repository-specific setup leaves the new task ready for local development without sharing dependencies, environment state, or database state with another checkout.

**Blocked by:** 01: Support SQLite local development and isolated tests; 02: Enforce pnpm across local setup and CI.

**Status:** ready-for-agent

- [ ] Air invokes an idempotent repository setup for a newly created Temporary Worktree.
- [ ] Composer and pnpm dependencies are installed independently within that worktree.
- [ ] Setup creates a fresh worktree-local environment file from the example and generates an application key when needed; it does not copy secrets from another checkout.
- [ ] Setup creates and migrates a persistent SQLite database local to the worktree, without automatically seeding a tenant.
- [ ] Re-running setup does not overwrite existing environment values or damage existing local database state.
- [ ] A clean worktree setup can run the documented application checks with tests isolated on in-memory SQLite, while the explicit MySQL path remains usable.
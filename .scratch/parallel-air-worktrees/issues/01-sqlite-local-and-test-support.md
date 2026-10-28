# 01: Support SQLite local development and isolated tests

**What to build:** Developers can use SQLite for regular local and worktree development and tests, while production and CI continue using MySQL and developers retain an explicit MySQL option for engine-specific debugging.

**Blocked by:** None (can start immediately).

**Status:** ready-for-agent

- [ ] Fresh migrations complete on SQLite, including migrations that currently rely on MySQL-specific schema operations or metadata.
- [ ] Representative search, sort, and other database-sensitive application behavior works on SQLite while preserving intended MySQL behavior.
- [ ] Local development can use a persistent SQLite database, while automated tests use isolated in-memory SQLite and do not modify the persistent database.
- [ ] An explicit MySQL test/debug path remains available; production and CI continue to use MySQL.
- [ ] Focused regression coverage demonstrates clean SQLite migration/test behavior and checks relevant MySQL-sensitive behavior.
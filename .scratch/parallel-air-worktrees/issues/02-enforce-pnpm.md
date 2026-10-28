# 02: Enforce pnpm across local setup and CI

**What to build:** Developers and CI use one consistent, pinned pnpm workflow for frontend installation and commands, and accidental npm or Yarn installs are rejected with a clear explanation.

**Blocked by:** None (can start immediately).

**Status:** ready-for-agent

- [ ] The supported package-manager version is declared and the pnpm lockfile is the sole supported JavaScript dependency lockfile.
- [ ] Developer setup and CI use compatible Node and pnpm versions for clean install, build, and test workflows.
- [ ] Accidental npm and Yarn installation attempts fail with clear guidance to use pnpm.
- [ ] No private registry credentials are copied into helper code, committed guidance, or other newly introduced configuration.
- [ ] A clean pnpm install and relevant frontend build/test commands succeed.
# 04: Document the Air parallel-task and PR lifecycle

**What to build:** Developers can use a consistent Air-first process for independent tasks across repositories and parallel tasks within one repository, from choosing a worktree through PR review, branch synchronization, and safe cleanup.

**Blocked by:** 03: Prepare Air Temporary Worktrees automatically.

**Status:** ready-for-agent

- [ ] Guidance explains when to use Air Local versus a separate task in Temporary Worktree mode, and clarifies that another session within a task shares its workspace.
- [ ] Guidance describes keeping unrelated repositories as separate project roots and starting each independent task from the latest `main`.
- [ ] The process covers task claims using the existing scratch issue conventions without requiring disjoint file ownership, and explains that separate worktrees prevent overlapping working-copy edits but not Git conflicts during integration.
- [ ] Each task agent's handoff includes relevant checks, commit/push, and opening or updating its own PR; human review and approval precede merging to `main`.
- [ ] An open task merges the latest `main` into its branch before its review checkpoint, resolves conflicts there, reruns relevant checks, and updates its PR.
- [ ] Guidance says to retain the Air task/worktree while its PR or follow-up is active and delete it only after merge and when no follow-up is needed.
- [ ] README and agent guidance consistently describe the supported PHP/Node/package-manager setup and link to the detailed workflow; the relevant SQLite/MySQL and Air setup decisions are recorded durably.
- [ ] The documented workflow is validated against Air's supported worktree setup behavior and the approved end-to-end setup/migration/test seam.
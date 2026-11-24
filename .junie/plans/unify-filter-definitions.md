---
sessionId: session-261120-172235-fpbd
---

# Requirements

### Goal
Create one canonical descriptor list per filter set and use it for both toolbar and drawer surfaces, while sharing field rendering across them.

### Scope
- Cover all existing filter definitions used by `IndexTableControls` and `Filters`, including drawer-only filter sets and pages with paired toolbar/drawer controls.
- Standardize selected counts, default-value markers, clear behavior, and reset-to-default behavior wherever applicable.
- Preserve different toolbar/drawer interaction contracts and responsive sizing; retain specialized domain displays and custom field behavior.
- Keep route/query semantics and unrelated table behavior unchanged.

# Technical Design

### Current Implementation
`IndexTableControls.jsx` defines toolbar filter metadata and renders filter options; `useSortFilterForm.js` initializes and submits the query-backed form from those definitions. `Filters.jsx` stages drawer edits and applies or clears them, while individual drawer components hand-build fields. This duplicates definitions and presentation—for example, `RiserStacks/Index.jsx` and `RiserStackFilters.jsx` both define search and ensemble filters. Singer status, voice part, and ensemble fields also recur across Singer, RSVP, and Learning Status filter compositions.

### Proposed Architecture
- Keep descriptors feature-owned, with one definition for each logical filter set (not one monolithic app-wide registry); use reusable descriptor factories for domain filters repeated across sets. Both the toolbar and drawer consume these descriptors.
- Add a shared field renderer for text, single/multiple choice, radio, and date-range fields, with presentation variants for compact toolbar and roomier drawer sizing. Retain `IndexTableControls` and `Filters` as separate wrappers so their existing submit, staging, Apply, and Clear flows remain intact.
- Put option labels, dynamic options, visibility conditions, defaults, and custom display adapters in descriptors. Preserve existing adapters such as `SingerStatusTag`, `VoicePartTag`, and `FeeStatus`; assess repeated bespoke displays (including event type, RSVP, learning status, and ensemble) for reusable adapters.
- Use renderer hooks for specialized fields or dependent behavior, including `TenantSelect` in mail-log filters and `EventFilters` date choices that also update sort state.
- Source singer-attendance event-type/date-range definitions once for both the page form and `SingerAttendanceFilters.jsx`.

### File Structure
- Add `resources/assets/js/components/FilterFieldRenderer.jsx` for shared field presentation and descriptor-level actions.
- Add feature-local descriptor modules alongside the existing owning page/filter files as each filter set is migrated; reuse domain descriptor/display helpers within the existing `resources/assets/js/components/` tree.
- Update `resources/assets/js/components/IndexTableControls.jsx`, `resources/assets/js/components/Filters.jsx`, `resources/assets/js/hooks/useSortFilterForm.js`, and existing feature filter components and index pages.

### Risks
Descriptor flexibility could become overly complex if it tries to encode every custom control. Keep the standard field types declarative, preserve explicit custom render/change hooks for exceptions, and test the toolbar and drawer interaction contracts independently.

# Testing

### Validation Approach
Update the existing filter tests as each surface migrates, keeping toolbar behavior and staged drawer behavior as separate contracts.

### Key Scenarios
- Verify shared fields render the same option adapters and selection semantics in both surfaces while retaining compact versus spacious sizing.
- Verify selected counts, default markers, clear, reset-to-default, and drawer Apply/Cancel behavior.
- Verify Riser Stacks ensemble options receive the shared styled rendering, and singer status/voice-part/fee displays remain intact.
- Verify attendance date serialization, event-date sort side effects, and custom tenant selection continue to work.

### Test Changes
Extend `Filters.test.jsx`, `SingerFilters.test.jsx`, `SingersIndexTableControls.test.jsx`, and `useSortFilterForm.test.js`; add focused coverage for the shared renderer and any migrated pages whose behavior is not covered by those tests.

# Delivery Steps

### ✓ Step 1: Build the shared descriptor and field-rendering foundation
The toolbar and drawer can render the same descriptor-driven fields through one renderer without changing their separate interaction flows.
- Add the shared field renderer and standard field types, including a compact toolbar variant and a roomier drawer variant.
- Read descriptor defaults and option metadata consistently in `useSortFilterForm.js`, `IndexTableControls.jsx`, and `Filters.jsx`.
- Centralize applicable selected counts, default markers, clear, and reset-to-default behavior.
- Migrate the paired Riser Stacks search and ensemble fields as the pilot; verify ensemble option styling in both surfaces and preserve toolbar/drawer behavior in focused tests.

### ✓ Step 2: Unify repeated singer-domain filters and attendance definitions
Singer-related filter sets reuse canonical status, voice-part, ensemble, fee, and attendance descriptors while preserving their domain displays.
- Extract reusable descriptors/adapters for repeated singer status, voice part, ensemble, and fee fields used by Singer, RSVP, Learning Status, and song-singer filters.
- Drive both singer toolbar and drawer fields from their shared descriptors, retaining `SingerStatusTag`, `VoicePartTag`, and `FeeStatus` presentation.
- Define singer-attendance event-type and date-range fields once for `Attendance/Index.jsx` and `SingerAttendanceFilters.jsx`.
- Update the existing singer and attendance filter tests for counts, defaults, reset, custom display, and date conversion.

### ✓ Step 3: Migrate standard filters across remaining pages
All remaining standard filter consumers use canonical descriptors and the shared renderer instead of independently assembled toolbar and drawer fields.
- Migrate the remaining `IndexTableControls` and `Filters` consumers across Events, Polls, Folders, Mailing Lists, Central pages, and other indexed features found in the repository inventory.
- Cover standard text, single/multiple choice, radio, and date-range filters, including drawer-only filter sets.
- Preserve each page's field visibility rules, defaults, URL keys, and query serialization while removing duplicate definitions.
- Add or update focused page tests where existing coverage does not protect the migrated behavior.

### ✓ Step 4: Integrate specialized filter controls and finalize parity
Specialized fields and option visuals use explicit shared adapters or hooks, with behavior preserved across the complete filter inventory.
- Adapt custom fields such as `TenantSelect` and event-date selections with sort side effects to the shared renderer extension points.
- Promote repeated bespoke option presentations to reusable adapters where appropriate, without replacing existing domain components.
- Confirm every existing toolbar and drawer filter consumer is migrated and test representative custom fields, responsive variants, reset semantics, and existing Apply/submit contracts.

### ✓ Step 5: Polish filter controls and communications filter behavior
Filter menus, action labels, rail controls, and tenant/email fields follow consistent responsive interaction and styling requirements.
- Close the entire Add Filter menu when its Close action is selected; use Select All consistently and make Apply/Cancel actions justify their space between them.
- Apply the requested compact desktop typography for attendance response and singer status options, purple ensemble options, and purple asterisk modified indicators.
- Standardize drawer and desktop-rail reset actions, remove redundant clear-all actions, and use shared `linkStyles` for purple underlined clickables. The desktop “Reset all filters” action uses the shared button component with secondary intent and clear weight, and is hidden when search is the only filter.
- Keep From/To controls hidden on mobile and absent from the rail until they have non-default values. On Communications desktop, render To as a searchable select directly in the pill with a “To:” prefix and its own clear action; keep From in a pill popover. Central Mail Logs keeps its To pill popover and text-input From pill, without duplicate labels or padding.
- Refine the Communications To pill: use smaller regular-weight X/chevron icons and the shared pill border; show one selected mailing list as plain inline text or multiple selections as “To: N selected”; list selected items by list name at the top of the open dropdown with remove actions, and reduce the heading and icon size for unselected options. Portal its open menu above sticky table headers and match standard filter pills in icon colors, padding, font size/color, and height.
- Keep the Communications To pill at auto width with tighter single-item icon spacing and selected dropdown text matching the unselected list-name size; use the drawer/mobile selector in Add Filter while retaining the desktop pill.
- Give open mailing-list selects a 250px minimum menu width, truncate a single selected To label at about 20 characters, keep the Add Filter selector within panel padding with spacing above its footer, and widen Add Filter panels slightly across filter types.
- Keep selected mailing-list chips content-sized; let names use the chip's available width and truncate when needed, wrap chips as needed, and constrain the selector and chips to the drawer width.
- Use a searchable tenant selector in both Add Filter and the filter rail, reusing the existing drawer selector behavior.
- Ensure searchable selects opened from Add Filter are not clipped by the menu’s overflow container.
- Add focused coverage for menu closing, labels/actions, modified markers, mail-log rail visibility and controls, overflow behavior, and tenant selection.
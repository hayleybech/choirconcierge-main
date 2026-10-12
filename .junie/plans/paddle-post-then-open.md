---
sessionId: session-261003-213351-yx2x
---

# Requirements

### Overview & Goals
Replace the current passive `paddle_button`/`data-override` behavior with the Spark-compatible Paddle Classic lifecycle: submit the selected plan to Laravel, receive a Cashier-generated checkout link, explicitly open the Paddle modal, and handle the checkout success callback.

### Scope
#### In scope
- Add an authenticated, tenant-authorized subscribe request for the selected plan.
- Return the generated Cashier Paddle pay link as JSON for the React page.
- Open Paddle Classic with `Paddle.Checkout.open({ override: ... })` after the response.
- Preserve Spark callback behavior: update pending UI state, clear the checkout URL through browser history, and submit the Paddle checkout ID to a replacement pending-checkout endpoint.
- Add Pest feature coverage for authorization, plan eligibility, duplicate subscriptions, successful link creation, and pending-checkout handling.

#### Out of scope
- Replacing Paddle Classic or upgrading Paddle.js.
- Changing existing swap, pause, cancel, webhook, or plan catalog behavior except where required to avoid conflicting subscribe behavior.
- Implementing a full subscription webhook redesign.

### Acceptance Criteria
- Clicking `Subscribe` sends the selected plan to the application instead of relying on automatic Paddle button discovery.
- An eligible, authorized tenant receives a non-empty Paddle link and the browser calls `Paddle.Checkout.open` with that link.
- Invalid, ineligible, unauthorized, or already-subscribed requests are rejected using the application’s existing billing conventions.
- Paddle success handling records/submits the returned `checkout.id`, marks the page as pending, and removes stale checkout URL state.
- Closing the modal clears the checkout URL state without falsely marking the subscription successful.

# Technical Design

### Current Implementation
- `app/Http/Controllers/BillingController.php::index()` currently creates a `payLink` for every plan during the billing page GET.
- `resources/assets/js/Pages/Tenants/Billing.js` renders the subscribe action as an anchor with `paddle_button` and `data-override`; it has no explicit Paddle click handler.
- `routes/web.php` has no application subscribe or pending-checkout route.
- `Tenant` uses `Laravel\Paddle\Billable`, and `composer.json` pins `laravel/cashier-paddle` to `1.12.0`.
- The historical Spark implementation authorized the request, returned `response.data.link`, then called `Paddle.Checkout.open` with `override`, `disableLogout`, `successCallback`, and `closeCallback`.

### Key Decisions
- Preserve the selected **post-then-open** boundary: server-side validation/link generation, client-side Paddle modal lifecycle.
- Keep authorization and tenant resolution in `BillingController`, using the existing `authorizeBilling()` and `tenant()` conventions.
- Use a JSON response for the subscribe action so `Billing.js` can explicitly invoke Paddle rather than depending on DOM scanning.
- Treat the success callback as a pending-checkout handoff, not final subscription confirmation; Cashier/Paddle webhook processing remains the source of subscription state.

### Proposed Changes
- Add a subscribe action to `BillingController` that:
  - authorizes the current tenant;
  - validates/resolves the requested plan against `config('spark.billables.tenant.plans')`;
  - applies `planEligibility()`;
  - rejects an existing `default` subscription rather than silently creating a duplicate;
  - calls `newSubscription('default', $planId)->returnTo(...)->create()`;
  - returns the generated link in the response shape consumed by the React page.
- Add a pending-checkout action/route that accepts the Paddle checkout identifier, applies the same tenant authorization, and delegates to the installed Cashier/Paddle-compatible persistence or callback mechanism identified during implementation. It must not mark a subscription active solely from the browser callback.
- Update `Billing.js` so only the subscribe action uses an explicit click handler. It should prevent the default anchor behavior, POST the selected plan using the existing Inertia/axios request convention, call `window.Paddle.Checkout.open({ override: response.data.link, disableLogout: true, ... })`, and handle success/close state transitions. Existing swap links remain unchanged.
- Remove the subscribe dependency on `data-override`/automatic `paddle_button` binding; retain normal loading/error feedback and prevent duplicate clicks while the link request is pending.
- Move pay-link generation out of `index()` for unsubscribed plans, or leave the prop only where needed during transition, so checkout links are created for the plan actually selected.

### Contracts
```text
POST organisation.billing.subscribe
input: { plan: integer }
success: { link: string }

POST organisation.billing.pending-checkout
input: { checkout_id: string }
success: { ... acknowledged/pending state ... }
```

The exact route names should follow the existing `organisation.billing.*` naming convention found in the billing routes. Validation should use the existing plan IDs and tenant context rather than accepting arbitrary Paddle identifiers.

### File Structure
- Modify `app/Http/Controllers/BillingController.php` for subscribe and pending-checkout actions.
- Modify the billing route declaration file where the existing `organisation.billing` routes are defined; `routes/web.php` is the central route entry point shown in the current project.
- Modify `resources/assets/js/Pages/Tenants/Billing.js` for the explicit POST/open/callback lifecycle.
- Extend `tests/Feature/BillingControllerTest.php` with Pest request-level coverage.
- Inspect the installed Cashier package/configuration before selecting the pending-checkout persistence call; do not introduce a new dependency or schema without confirming it is required.

### Risks
- Cashier 1.12’s Classic Paddle APIs may differ from newer Paddle examples; implementation must use the installed package contract.
- Browser callbacks are not authoritative and can be replayed; authorization, input validation, idempotency, and webhook reconciliation must remain server-side.
- Inertia form submission and JSON/redirect response expectations must be kept distinct so the subscribe action does not trigger a full page redirect.

# Testing

### Validation Approach
Use Pest feature tests in `tests/Feature/BillingControllerTest.php`, matching existing tenant setup, role authorization, and Cashier mocks. Verify HTTP contracts and server-side decisions; frontend Paddle invocation should be validated through the explicit component/request contract and manual browser confirmation after implementation.

### Key Scenarios
- Authorized billing user receives a JSON pay link for an eligible plan.
- Admin or Accounts Team member can subscribe; unauthorized users and demo tenant are rejected.
- Ineligible plan returns the existing validation error.
- Existing subscription cannot create a second initial subscription.
- Missing or unknown plan input is rejected.
- Pending-checkout request accepts a valid checkout ID only for an authorized tenant and is safe to repeat according to the chosen persistence contract.
- Controller-generated link includes the configured return URL.

### Frontend Checks
- Subscribe click prevents the `#!` navigation and posts the selected plan.
- Successful response invokes `Paddle.Checkout.open` with `override` and `disableLogout`.
- Paddle success posts `checkout.id`, updates pending state, and clears the URL state.
- Paddle close clears URL state without sending a false success request.
- Swap behavior remains unchanged.

# Delivery Steps

### ✓ Step 1: Add server-side subscribe contract
The billing backend exposes an authorized subscribe request that returns a Cashier-generated Paddle link for the selected plan.

- Add the subscribe action to `BillingController` using existing tenant authorization and plan eligibility logic.
- Validate the submitted plan against `config('spark.billables.tenant.plans')`.
- Reject duplicate initial subscriptions and return a JSON `{ link }` response on success.
- Add the named subscribe route using the existing `organisation.billing.*` route conventions.
- Extend `tests/Feature/BillingControllerTest.php` for success, authorization, invalid-plan, eligibility, and duplicate-subscription branches.

### ✓ Step 2: Implement explicit Paddle checkout lifecycle
The billing page posts the selected plan and explicitly opens the Paddle Classic modal with the returned link.

- Replace the subscribe anchor’s automatic `paddle_button`/`data-override` reliance in `resources/assets/js/Pages/Tenants/Billing.js` with a click handler.
- Add request-in-progress and error handling so repeated clicks cannot create multiple checkout links.
- Call `window.Paddle.Checkout.open` with the server response as `override` and preserve Spark’s `disableLogout` behavior.
- Keep swap links and existing plan display behavior unchanged.

### ✓ Step 3: Add success callback handoff
Completed Paddle checkouts are handed back to Laravel as pending checkout records without treating the browser callback as final subscription confirmation.

- Add the pending-checkout route and controller action with tenant authorization and checkout ID validation.
- Implement the installed Cashier-compatible persistence/acknowledgement behavior after confirming the `laravel/cashier-paddle` 1.12.0 API.
- Wire `successCallback` in `Billing.js` to submit `response.checkout.id`, update pending UI state, and clear URL state.
- Wire `closeCallback` to clear stale checkout URL state without recording success.
- Add Pest coverage for valid, unauthorized, malformed, and repeat pending-checkout requests.

### ✓ Step 4: Harden subscription mutation actions
Ensure swap, pause, unpause, and cancel use safe mutation methods and reject invalid or unavailable subscription operations consistently.

- Change billing mutation routes from `GET` to `POST`.
- Validate swap plans against configured plans and preserve eligibility checks.
- Handle missing subscriptions before pause, unpause, cancel, and swap calls.
- Correct the trial swap error message and add focused Pest coverage for these branches.
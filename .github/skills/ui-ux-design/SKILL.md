---
name: ui-ux-sneat-laravel
description: "UI/UX guidance for Laravel projects that use the Sneat Bootstrap 5 admin template with Blade. Use this skill whenever the user asks to create, review, improve, or refactor any admin UI in this project — Blade pages, forms, tables, dashboards, modals, filters, badges, navigation, layouts, or styling — or mentions UX, usability, accessibility, responsive design, spacing, or \"make it look better\", even if they don't say \"UI/UX\" or \"Sneat\". Always reuse Sneat's existing layout, classes, and vendor libraries instead of inventing custom CSS."
---

# UI/UX Skill — Laravel + Sneat (Bootstrap 5)

Build admin interfaces that are clear, consistent, accessible, and fast to use, using the Sneat template and Laravel Blade conventions already in the project.

## Golden Rules

1. **Reuse Sneat first.** Use Sneat/Bootstrap 5 classes and components before writing any custom CSS. If something looks custom, check whether Sneat already provides it.
2. **Match the existing project.** Inspect an existing page in `resources/views/` before writing a new one, and copy its structure, naming, and section names.
3. **One file per page.** Prefer self-contained, single-file Blade templates. Keep page-specific CSS and JS inside that same file via the layout's style/script sections (or `@push`), not in separate CSS/JS files or extra partials, unless the same markup is reused in 3+ places.
4. **Never edit vendor or template core files.** Do not modify `public/assets/vendor/*` or Sneat's core CSS/JS. Override with minimal, scoped rules in the page's style section.
5. **No new UI libraries** unless asked. Use what Sneat already bundles (e.g., DataTables, Select2, Flatpickr, SweetAlert2, ApexCharts, Boxicons) where available in the project.

## Blade Page Skeleton

Check the project's `resources/views/layouts/` for the exact layout name and section names (they differ between Sneat versions), then follow this pattern:

```blade
@extends('layouts/contentNavbarLayout')

@section('title', 'Page Title')

@section('vendor-style')
  {{-- vendor CSS needed by this page only --}}
@endsection

@section('page-style')
  <style>
    /* small, scoped rules for this page only */
  </style>
@endsection

@section('content')
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Section /</span> Page Title</h4>

  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Title</h5>
      <a href="{{ route('items.create') }}" class="btn btn-primary">
        <i class="bx bx-plus me-1"></i> Add Item
      </a>
    </div>
    <div class="card-body">
      {{-- content --}}
    </div>
  </div>
@endsection

@section('vendor-script')
  {{-- vendor JS needed by this page only --}}
@endsection

@section('page-script')
  <script>
    // page-specific JS here
  </script>
@endsection
```

- Load a vendor library only on pages that use it.
- Use `{{ asset('...') }}` for assets and `route('name')` for links; never hard-code URLs.

## Core Principles

### Hierarchy
- One primary action per page or card: `btn btn-primary`. Secondary actions: `btn btn-label-secondary` or `btn btn-outline-secondary`.
- Page title in a heading; card titles in `card-header`. Put filters above the table, actions top-right.
- Group related fields in cards or sections; separate unrelated content with spacing utilities (`mb-3`, `mb-4`, `gap-2`).

### Consistency
- Same action, same look, same wording on every page. Reuse the same button, badge, and icon for the same meaning.
- Use Bootstrap spacing utilities (`p-*`, `m-*`, `gap-*`) instead of custom pixel values.
- Use Boxicons (`bx bx-*`) or whichever icon set the project already uses. Do not mix icon sets.

### Feedback
- After every action, show feedback: Laravel flash message, inline validation error, or a toast/SweetAlert if the project uses it.
- Buttons use verbs: "Save changes", "Submit request", "Approve" — not "OK" or "Submit".
- Never rely on color alone; pair badges and alerts with text or an icon.

### Forgiveness
- Confirm destructive actions (delete, reject, cancel) with a confirmation modal or SweetAlert; use `btn-danger` only for these.
- Preserve input on validation errors with `old()`.
- Use sensible defaults, helper text (`form-text`), and placeholders that show examples, not labels.

## Forms (Laravel + Bootstrap 5)

```blade
<form method="POST" action="{{ route('items.store') }}" novalidate>
  @csrf
  <div class="mb-3">
    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
    <input type="text" id="name" name="name"
           class="form-control @error('name') is-invalid @enderror"
           value="{{ old('name') }}" required>
    @error('name')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary">Save</button>
    <a href="{{ route('items.index') }}" class="btn btn-label-secondary">Cancel</a>
  </div>
</form>
```

- Always include `@csrf`; use `@method('PUT')` / `@method('DELETE')` for non-POST forms.
- Visible `<label for>` on every field (never placeholder-only); mark required fields consistently.
- Show errors per field with `@error` + `invalid-feedback`; optionally show a summary alert at the top for long forms.
- Use the right input type (`number`, `date`, `email`, `select`) and set `min`, `max`, `step`, `autocomplete` where useful.
- Single-column by default; use `row` + `col-md-6` for short related pairs (e.g., date range, quantity + unit).
- Prevent double submits: disable the submit button and show a spinner on submit.
- Selects with many options: use Select2 (if the project bundles it).

## Tables and Lists

- Wrap tables in `table-responsive`; use `table` (and `table-hover` for clickable rows).
- Right-align numbers, left-align text; keep headers short.
- Provide search, filter, and pagination for anything beyond a few rows. For server-side lists use Laravel pagination (`{{ $items->links() }}`) and ensure Bootstrap 5 pagination views are enabled.
- Empty state with a next action:

```blade
@forelse ($items as $item)
  <tr>...</tr>
@empty
  <tr>
    <td colspan="5" class="text-center text-muted py-5">
      No data yet. <a href="{{ route('items.create') }}">Create the first one</a>.
    </td>
  </tr>
@endforelse
```

- Status badges use Sneat's label style with text, mapped consistently across the app:
  - Pending / Draft: `badge bg-label-warning`
  - Approved / Success: `badge bg-label-success`
  - Rejected / Failed: `badge bg-label-danger`
  - Info / In progress: `badge bg-label-info`
  - Neutral / Inactive: `badge bg-label-secondary`
- Put row actions in the last column, consistent order (view, edit, delete); use icon buttons with `title` and `aria-label`, or a dropdown for 3+ actions.
- Hide authorization-restricted actions with `@can` / `@canany` instead of showing disabled buttons.
- Avoid N+1 queries when rendering lists: eager-load relations in the controller (`with()`).

## Modals and Dialogs

- Use Bootstrap modals only for short, focused tasks or confirmations.
- Title states the task; buttons state the outcome ("Delete request", not "Yes").
- Keep dismiss behavior (X, Esc, Cancel). Do not stack modals.
- For delete confirmations, use a real `<form method="POST">` with `@csrf @method('DELETE')`, never a plain link.

## Feedback Messages

- Flash messages after redirects (`session('success')`, `session('error')`) rendered as `alert alert-success|danger alert-dismissible` in one shared place (layout or partial), not repeated on each page.
- Errors that need action must not auto-dismiss. Success toasts may auto-dismiss.
- Say what happened and what to do next.

## Cards, Dashboards, and Layout

- Use `card` for grouping; avoid nesting cards inside cards.
- Use the Bootstrap grid (`row`, `col-12 col-md-6 col-xl-3`) mobile-first; stats cards should stack on small screens.
- Keep dashboard cards scannable: big number, short label, optional trend or icon (`avatar` with `bg-label-*`).
- Do not overload a page: put secondary info behind tabs (`nav-tabs`), accordions, or a details page.

## Navigation

- Add new menu items in the project's menu source (e.g., `resources/menu/*.json` or the sidebar partial) following the existing structure; highlight the active route.
- Use breadcrumbs or the `text-muted fw-light` heading pattern for context on deep pages.
- Keep labels short and noun-based.

## Accessibility (non-negotiable)

- Use semantic HTML: `button` for actions, `a` for navigation, `label` for inputs, headings in order.
- Every interactive element is keyboard reachable with a visible focus style; never remove outlines without a replacement.
- Icon-only buttons need `aria-label` (and `title`); decorative icons get `aria-hidden="true"`.
- Meaningful images have `alt`; decorative images use `alt=""`.
- Errors and status updates should be announced (`role="alert"` on alerts).
- Contrast: 4.5:1 for normal text. Do not put light text on `bg-label-*` backgrounds that reduce contrast; use Sneat's paired text color.

## Responsive Design

- Mobile-first with Bootstrap breakpoints (`sm`, `md`, `lg`, `xl`); test at about 360, 768, 1024, and 1440 px.
- Wide tables go in `table-responsive`; consider hiding low-priority columns with `d-none d-md-table-cell`.
- Touch targets at least 44×44 px on mobile (use `btn` sizes, not tiny icon-only links).
- Sidebar collapse and navbar behavior come from Sneat; do not re-implement.

## Writing UI Copy

- Concise, specific, plain language. Lead with the action or outcome.
- Errors: what went wrong + how to fix it ("Quantity must be at least 1", not "Invalid input").
- Empty states: explain what belongs here + a next step.
- Keep language consistent across the app (don't mix languages within one screen unless the app is bilingual).

## Reviewing an Existing Page

Respond in this order:

1. **Top issues** blocking the main task (usability, accessibility, broken validation or feedback).
2. **Consistency issues** versus Sneat and the rest of the project (wrong classes, custom CSS that duplicates Sneat, inconsistent badges and buttons).
3. **Quick wins** with the most improvement for the least change.
4. **Concrete fixes**: show the exact Blade/HTML change, not just advice.

Explain the "why" in one sentence per issue.

## Delivery Checklist

- [ ] Follows an existing project page's layout, sections, and naming
- [ ] Uses Sneat/Bootstrap classes; custom CSS is minimal, scoped, and in the same file
- [ ] One clear primary action; hierarchy is obvious at a glance
- [ ] Forms: `@csrf`, `old()`, `@error` per field, visible labels, double-submit protection
- [ ] Tables: responsive wrapper, `@forelse` empty state, pagination, consistent status badges
- [ ] Destructive actions confirmed and sent via POST with `@method('DELETE')`
- [ ] Flash messages and validation feedback visible and specific
- [ ] Authorization respected in the UI (`@can`) and not only hidden visually
- [ ] Keyboard accessible, labeled icon buttons, adequate contrast
- [ ] Works at mobile, tablet, and desktop widths
- [ ] No N+1 queries introduced by the view
- [ ] No vendor or template core files modified
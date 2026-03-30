# Workspace Instructions (WorkflowSystem)

This file provides a high-level “template” for how to work in this repository as an AI assistant.

## 🧭 What this repo is

A small Laravel 12 app that supports:

- Users submitting leave and mission requests.
- Approvers reviewing, approving, and rejecting requests.

## 🎯 Goals for AI assistance

When asked to make changes or implement features, focus on:

- Keeping authorization boundaries clear (users see only their own requests; approvers see pending requests).
- Using Laravel conventions (controllers, policies/middleware, form requests, Blade components).
- Keeping the UI consistent with existing Bootstrap styling.

## ✅ Common patterns

- Controllers returning views with compacted data arrays.
- Status fields are strings: `pending`, `approved`, `rejected`.
- Approver/department admin access is enforced via `EnsureUserIsApprover` middleware (it allows `role = 'approver'` or `role = 'admin'`).

## 🧪 How to run and test

- `composer install`
- `php artisan migrate`
- `php artisan serve`
- `php artisan test`

## 🚩 Anti-patterns (avoid)

- Modifying database schema without adding a migration.
- Bypassing authorization checks, especially around approver actions.
- Introducing new build tooling (Webpack/Vite) unless required.

## 🗂 Key files to inspect for changes

- `routes/web.php` (routing + middleware)
- `app/Http/Controllers/*` (request handling)
- `resources/views/*` (UI templates)
- `app/Models/*` (data/relationships)

---

If you need a more specific helper (e.g., for tests, API endpoints, or frontend components), ask for it explicitly and mention where it should live (controller, view, route, etc.).

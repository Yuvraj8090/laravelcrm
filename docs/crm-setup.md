# Laravel CRM Setup

## Stack

- Laravel 12 skeleton in this repo
- PHP 8.2+
- MySQL intended for normal local and production usage
- Blade templates
- No paid APIs
- No paid packages

## CRM Entry Point

Open the CRM at:

```text
/crm/login
```

## Demo Accounts

- Admin: `crm.admin@example.com`
- Manager: `crm.manager@example.com`
- Sales Rep: `crm.sales@example.com`
- Password for all demo users: `password123`

## Database Setup

1. Point `.env` to your MySQL database
2. Run:

```bash
php artisan migrate:fresh --seed
```

## Main CRM Areas

- Dashboard: `/crm/dashboard`
- Companies: `/crm/companies`
- Contacts: `/crm/contacts`
- Leads: `/crm/leads`
- Deals: `/crm/deals`
- Tasks: `/crm/tasks`
- Emails: `/crm/communications`
- Pipelines: `/crm/pipelines`
- Quotes: `/crm/quotes`
- Invoices: `/crm/invoices`
- Theme Settings: `/crm/settings/theme`

## Theme System

Available themes:

- Corporate
- Dark
- Minimal
- High Contrast
- Brand Customizable

Theme assets live in:

- `public/crm-assets/base.css`
- `public/crm-assets/themes/*.css`

Brand customization is stored per-user in:

- `users.crm_theme`
- `users.crm_theme_settings`

## Low-Cost Notes

- Uses only built-in Laravel auth/session features
- Uses Blade instead of a JavaScript SPA
- Stores communication logs locally in MySQL
- Avoids third-party billing, email, and analytics services

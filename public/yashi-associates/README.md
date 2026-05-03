# Yashi Associates

Offline-ready real estate management website with:

- Premium cinematic landing page
- Dark/light theme toggle with persistence
- Role-based dashboard for admin, owner, and team users
- CRUD-style management for properties, users, teams, phases, and issues
- localStorage persistence for demo data

## Run locally

### Python

```bash
cd public/yashi-associates
python3 serve.py
```

Open:

```text
http://127.0.0.1:8080
```

### Quick alternative

If you already have Python installed, this also works:

```bash
cd public/yashi-associates
python3 -m http.server 8080
```

## Sample logins

- Admin: `admin@yashi.local` / `password123`
- Owner: `owner@yashi.local` / `password123`
- Team: `team@yashi.local` / `password123`

## Reset demo data

Open browser dev tools and clear:

- `yashi-associates-db-v1`
- `yashi-associates-session`
- `yashi-associates-theme`

Then refresh the page.

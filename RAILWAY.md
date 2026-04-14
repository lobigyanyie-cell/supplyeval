# Deploy on Railway (PHP + MySQL only)

No Java required. Evaluation scoring runs in PHP when **evaluation service URL** is empty (default).

## 1. Push code to GitHub

Railway deploys from a Git repository.

## 2. Create a Railway project

1. Go to [railway.app](https://railway.app) → **New Project** → **Deploy from GitHub** → select this repo.
2. Railway detects the root **`Dockerfile`** and builds **PHP 8.2 CLI** with the **built-in web server** (no Apache — avoids MPM / `AH00534` issues on Railway).

## 3. Add MySQL

1. In the project → **New** → **Database** → **MySQL**.
2. Open your **web service** → **Variables** → **Add Reference** (or **Connect**) and link the MySQL plugin so these are injected (names may vary slightly):

   - `MYSQL_HOST`, `MYSQL_PORT`, `MYSQL_USER`, `MYSQL_PASSWORD`, `MYSQL_DATABASE`

   The app reads these automatically via `Database.php` (no need to duplicate as `DB_*` unless you prefer).

## 4. Create tables (one-time)

Use Railway’s **MySQL** tab (query console) or the [Railway CLI](https://docs.railway.app/develop/cli) + `mysql` client.

Import **`sql/schema_railway.sql`** into the database Railway created (same name as `MYSQL_DATABASE`, often `railway`):

```bash
# Example with CLI (get host/user/pass from Railway MySQL variables)
mysql -h $MYSQLHOST -u $MYSQLUSER -p$MYSQLPASSWORD $MYSQLDATABASE < sql/schema_railway.sql
```

Or paste the file contents into the SQL console.

Default login after import: **`admin@saas.com`** / **`admin123`** — change this in production.

**Already had a database before evaluation workflow?** Run the one-time migration (adds `evaluations.status` + `evaluation_workflow_events`):

```bash
php public/migrate_evaluation_workflow.php
```

Or execute **`sql/migrate_evaluations_workflow.sql`** in the MySQL console (skip if `status` already exists).

**Missing `companies.plan` (plan tiers)?** Run once:

```bash
php public/migrate_company_plan.php
```

Or execute **`sql/migrate_company_plan.sql`** in the MySQL console (skip if the column already exists).

## 5. Open the app

Your public URL will look like:

`https://<your-service>.up.railway.app/saas/login`

Use the **`/saas/...`** path — the Docker image maps that prefix to `public/`.

## 6. Environment (optional)

| Variable | Purpose |
|----------|---------|
| `MYSQL_*` | Injected when MySQL is linked — **required** for DB. |
| `SAAS_BASE_PATH` | Leave unset to default `/saas` (matches the image). |
| `EVALUATION_SERVICE_URL` | Leave **unset** for PHP-only scoring. |

## Troubleshooting

- **`AH00534: More than one MPM loaded`** — Fixed by **not using Apache** in the Dockerfile (PHP `-S` + `public/router.php`). Pull latest `main` and redeploy with **Clear build cache**.
- **Database connection errors** — Confirm MySQL is **linked** to the web service and `schema_railway.sql` was run on **`MYSQL_DATABASE`**.
- **404 on `/`** — Open **`/saas/`** or **`/saas/login`**.
- **502** — Check **Deploy Logs**; ensure the container starts and MySQL is reachable.

## Cost

See [Railway pricing](https://railway.app/pricing). Free tier / credits change over time.

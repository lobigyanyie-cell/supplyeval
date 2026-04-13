# Deploy on Railway (PHP + MySQL only)

No Java required. Evaluation scoring runs in PHP when **evaluation service URL** is empty (default).

## 1. Push code to GitHub

Railway deploys from a Git repository.

## 2. Create a Railway project

1. Go to [railway.app](https://railway.app) → **New Project** → **Deploy from GitHub** → select this repo.
2. Railway detects the root **`Dockerfile`** and builds **PHP 8.2 CLI** with the **built-in web server** (no Apache — avoids MPM / `AH00534` issues on Railway).

## 3. Add MySQL

1. In the project → **New** → **Database** → **MySQL**.
2. Open your **web service** → **Variables** → **Add Reference** (or **Connect**) and link the MySQL plugin. Easiest: reference **`MYSQL_URL`** once (full DSN). Railway also exposes:

   - `MYSQLHOST`, `MYSQLPORT`, `MYSQLUSER`, `MYSQLPASSWORD`, `MYSQLDATABASE`, `MYSQL_URL`

   The app reads `MYSQL_URL` first, then `DATABASE_URL` if it starts with `mysql://`, then `DB_*` / `MYSQL_*` / `MYSQLHOST` style vars (`Database.php`).

## 4. Create tables (one-time)

Use Railway’s **MySQL** tab (query console) or the [Railway CLI](https://docs.railway.app/develop/cli) + `mysql` client.

Import **`sql/schema_railway.sql`** (or your full dump such as **`sql/supplier_saas.sql`**) into the **same database** Railway uses — the name is the path in **`MYSQL_URL`** (often **`railway`**), i.e. **`MYSQLDATABASE`**. Importing only on your laptop does not populate Railway’s MySQL.

```bash
# Example with CLI (get host/user/pass from Railway MySQL variables)
mysql -h $MYSQLHOST -u $MYSQLUSER -p$MYSQLPASSWORD $MYSQLDATABASE < sql/schema_railway.sql
```

Or paste the file contents into the SQL console.

Default login after import: **`admin@saas.com`** / **`admin123`** — change this in production.

## 5. Open the app

Your public URL will look like:

`https://<your-service>.up.railway.app/saas/login`

Use the **`/saas/...`** path — the Docker image maps that prefix to `public/`.

## 6. Environment (optional)

| Variable | Purpose |
|----------|---------|
| `MYSQLHOST`, `MYSQLPORT`, … | Injected when MySQL is linked — **required** for DB. |
| `SAAS_BASE_PATH` | Leave unset to default `/saas` (matches the image). |
| `EVALUATION_SERVICE_URL` | Leave **unset** for PHP-only scoring. |

## Troubleshooting

- **`AH00534: More than one MPM loaded`** — Fixed by **not using Apache** in the Dockerfile (PHP `-S` + `public/router.php`). Pull latest `main` and redeploy with **Clear build cache**.
- **Database connection errors** — Confirm MySQL is **linked** to the web service (so `MYSQLHOST` etc. are set). Run `schema_railway.sql` on the linked database (`MYSQLDATABASE`). If you see `SQLSTATE[HY000] [2002] No such file or directory`, the app was likely using `localhost` without Railway’s host: fix by redeploying after linking MySQL or setting `MYSQLHOST` from the DB service.
- **404 on `/`** — Open **`/saas/`** or **`/saas/login`**.
- **502** — Check **Deploy Logs**; ensure the container starts and MySQL is reachable.

## Cost

See [Railway pricing](https://railway.app/pricing). Free tier / credits change over time.

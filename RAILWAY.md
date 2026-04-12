# Deploy on Railway (PHP + MySQL only)

No Java required. Evaluation scoring runs in PHP when **evaluation service URL** is empty (default).

## 1. Push code to GitHub

Railway deploys from a Git repository.

## 2. Create a Railway project

1. Go to [railway.app](https://railway.app) → **New Project** → **Deploy from GitHub** → select this repo.
2. Railway detects the root **`Dockerfile`** and builds **PHP 8.2 + Apache**.

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

- **`AH00534: More than one MPM loaded`** — The Dockerfile removes `mpm_event` / `mpm_worker` and keeps **prefork** only. Redeploy from latest `main`. In Railway: **Redeploy** → enable **Clear build cache** if the error persists (old layers may be cached).
- **Database connection errors** — Confirm MySQL is **linked** to the web service and `schema_railway.sql` was run on **`MYSQL_DATABASE`**.
- **404 on `/`** — Open **`/saas/`** or **`/saas/login`**.
- **502** — Check **Deploy Logs**; ensure the container starts and MySQL is reachable.

## Cost

See [Railway pricing](https://railway.app/pricing). Free tier / credits change over time.

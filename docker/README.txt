Docker stack (see ../docker-compose.yml)

Build and run:
  docker compose up --build -d

URLs:
  Web UI:  http://localhost:8080/saas/login
  Java:    http://localhost:8081/api/v1/health
  MySQL:   localhost:3306  user root / password root

Environment variables for the PHP container are set in docker-compose.yml (DB_*, SAAS_BASE_PATH, EVALUATION_SERVICE_URL).

Existing database without the settings table: run
  docker compose exec web php public/migrate_settings.php

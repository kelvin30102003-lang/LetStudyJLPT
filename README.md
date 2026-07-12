# LetStudyJLPT
jlpt-learining-website

## Architecture

- [Software Architecture Document](docs/software-architecture.md)

## Development Start

This project targets Laravel 12 on PHP 8.4.

Local machine requirements:

- PHP 8.4 and Composer 2, or Docker with Docker Compose.
- MySQL 8, Redis, Meilisearch, and S3-compatible storage are provided by `docker-compose.yml` for development.

With Docker:

```bash
docker compose up -d
docker compose exec app composer install
docker compose exec app cp .env.example .env
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
```

Application URL: `http://localhost:8080`

Local services:

- Meilisearch: `http://localhost:7700`
- MinIO API: `http://localhost:9000`
- MinIO Console: `http://localhost:9001`
- Mailpit: `http://localhost:8025`

Quality checks:

```bash
composer format
composer analyse
composer test
```

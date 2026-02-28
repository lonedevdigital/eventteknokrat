# Deploy di Coolify (Docker Compose)

## 1) File yang dipakai
- `docker-compose.yml`
- `Dockerfile`
- `docker/entrypoint.sh`
- `docker/apache/000-default.conf`
- `.env.coolify.example`

## 2) Setup di Coolify
1. Buat resource baru: **Docker Compose**.
2. Pilih repository project ini.
3. Compose file path: `docker-compose.yml`.
4. Tambahkan environment variable dari contoh `.env.coolify.example`.

## 3) Environment minimal yang wajib
- `APP_KEY` (generate dulu: `php artisan key:generate --show`)
- `APP_URL`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`
- `DB_ROOT_PASSWORD`

## 4) Optional
- `RUN_MIGRATIONS=true` untuk auto migrate saat container start.
- `REDIS_PASSWORD` jika ingin Redis pakai password.
- `APP_PORT` jika test local compose (default `8080`).

## 5) Catatan penting
- Upload file Laravel disimpan persisten di volume `laravel_storage`.
- Database disimpan persisten di volume `mysql_data`.
- Redis data disimpan persisten di volume `redis_data`.
- Jika sudah punya database eksternal, nonaktifkan service `mysql` pada compose dan set `DB_HOST` ke host eksternal.


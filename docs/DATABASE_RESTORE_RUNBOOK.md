# Database Restore Runbook

Panduan memulihkan `db_teknisi` dari file dump otomatis (`backups/pre-destructive-*.dump`,
dibuat guardrail sebelum perintah destruktif) atau dump manual. Lihat juga
AGENTS.md aturan 8 (Database Safety Rules).

## Buat dump manual

```bash
PGPASSWORD='<db-password>' pg_dump -h 127.0.0.1 -p 5432 -U teknisi_user -d db_teknisi \
  -Fc -f backups/manual-$(date +%Y%m%d-%H%M%S)-db_teknisi.dump
```

## Restore ke database utama

```bash
# 1. Hentikan tulis dulu: pastikan tidak ada sesi lain sedang migrasi/seed.
# 2. Restore (data lama di dalam DB akan diganti):
PGPASSWORD='<db-password>' pg_restore -h 127.0.0.1 -p 5432 -U teknisi_user \
  -d db_teknisi -c backups/<nama-file>.dump

# 3. Validasi:
php artisan migrate:status
php artisan tinker --execute="echo 'users: '.\App\Models\User::count().PHP_EOL;"
php artisan test
```

## Uji restore ke DB bayangan (tanpa menyentuh data aktif)

```bash
sudo -u postgres psql -c "CREATE DATABASE db_teknisi_shadow OWNER teknisi_user;"
PGPASSWORD='<db-password>' pg_restore -h 127.0.0.1 -p 5432 -U teknisi_user \
  -d db_teknisi_shadow -c backups/<nama-file>.dump
# Bila valid, hapus lagi: sudo -u postgres psql -c "DROP DATABASE db_teknisi_shadow;"
```

## Checklist pasca-restore

- [ ] `users` > 0 dan akun admin bisa login di browser
- [ ] `php artisan test` hijau
- [ ] Umumkan ke semua sesi bahwa restore selesai (skema/data kembali ke titik dump)

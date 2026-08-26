# Aturan Kerja Multi-Sesi

Repo ini dikerjakan oleh lebih dari satu sesi AI/developer secara paralel.
Sejak konsolidasi, setiap sesi punya **folder kerja (worktree) sendiri** —
ikuti aturan berikut supaya tidak saling menimpa kerjaan.

## 1. Satu sesi = satu folder = satu branch

| Folder | Branch | Topik |
|---|---|---|
| `/var/www/dbteknisi` | `main` | **Integrasi + yang dilayani website.** Jangan kerja fitur di sini |
| `/var/www/dbteknisi-mkt` | `feature/div-marketing` | Lead, partner |
| `/var/www/dbteknisi-tek` | `feature/div-teknisi` | Dashboard/jadwal teknisi |
| `/var/www/dbteknisi-user` | `feature/user-management-avatar` | User, role, menu, avatar |
| `/var/www/dbteknisi-mon` | `feature/div-mon` | Monitoring (progress per customer) |

Worktree baru juga butuh: `composer install`, symlink `.env`, dan
`ln -s /var/www/dbteknisi/public/build /var/www/dbteknisi-<nama>/public/build`.

Rencana pembagian berikutnya (buat worktree-nya saat mulai dikerjakan):
`feature/div-cus` (Customer), `feature/div-trash` (Trash), `feature/div-panel` (Admin Panel).

Butuh folder untuk topik baru?

```bash
git worktree add /var/www/dbteknisi-<nama> feature/<branch>
ln -sf /var/www/dbteknisi/.env /var/www/dbteknisi-<nama>/.env
```

**DILARANG pindah branch di dalam worktree milik sesi lain.**
Satu worktree = satu branch sampai fitur selesai.

## 2. Alur kerja (satu arah)

```
fitur (di worktree divisi)  →  merge ke main  →  semua worktree git pull
```

- Sebelum mulai: `git pull origin <branch-mu>`
- Sebelum merge ke `main`: pastikan `php artisan test` lulus
- Setelah `main` berubah: kembali `git pull` di worktree masing-masing
- Jangan cherry-pick antar branch divisi — kalau butuh perubahan bersama,
  merge `main` ke branch-mu

## 3. Commit kecil tapi sering

Commit + push = backup. Pesan commit menjelaskan fiturnya.

## 4. Pindah branch hanya dengan tree bersih

Commit dulu atau `git stash push -m "pesan jelas"`.
**Dilarang keras `git reset --hard` selama sesi lain masih aktif.**

## 5. Website dilayani dari `main`

Folder utama (`/var/www/dbteknisi`) harus selalu berada di branch `main`.
Setelah merge ke main: `git pull`, lalu `npm run build` jika ada perubahan CSS/JS.

## 6. Jangan commit

- Folder `backups/` (sudah di-gitignore)
- Kredensial / `.env`
- File hasil eksperimen tanpa persetujuan

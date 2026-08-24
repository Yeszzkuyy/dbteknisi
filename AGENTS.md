# Aturan Kerja Multi-Sesi

Repo ini sering dikerjakan oleh lebih dari satu sesi AI/developer secara paralel.
Ikuti aturan berikut supaya tidak saling menimpa kerjaan.

## 1. Satu sesi = satu branch

| Topik kerja | Branch |
|---|---|
| Marketing (lead, partner) | `feature/div-marketing` |
| Sales | `feature/div-sales` |
| Teknisi | `feature/div-teknisi` |
| User, role, menu, avatar | `feature/user-management-avatar` |
| UI/UX global (layout, tema, tampilan) | `feature/ui-ux` |

Jangan pernah mengerjakan topik di luar branch milikmu. Jika butuh file dari
branch lain, cherry-pick commit spesifik — jangan pindah-pindah branch.

## 2. Sebelum mulai & sebelum push

```bash
git checkout <branch-mu> && git pull
```

## 3. Commit kecil tapi sering

Jangan biarkan WIP menggantung lama tanpa commit. Commit + push = backup.
Pesan commit menjelaskan fitur, bukan "checkpoint" terus-menerus.

## 4. Pindah branch hanya dengan tree bersih

Sebelum `git checkout` branch lain: commit dulu, atau `git stash -u`.
**Dilarang `git reset --hard` selama sesi lain masih aktif.**

## 5. Integrasi lewat merge/PR ke `main`

Perubahan antar branch disatukan via PR di GitHub, bukan cherry-pick manual
berulang-ulang. Sebelum PR, pastikan test lulus: `php artisan test`.

## 6. Jangan commit

- Folder `backups/` (sudah di-gitignore)
- Kredensial / `.env`
- File hasil eksperimen tanpa persetujuan

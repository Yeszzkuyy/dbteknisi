# Aturan Kerja Multi-Sesi

Repo ini dikerjakan oleh lebih dari satu sesi AI/developer secara paralel.
Sejak konsolidasi, setiap sesi punya **folder kerja (worktree) sendiri** —
ikuti aturan berikut supaya tidak saling menimpa kerjaan.

## 1. Satu sesi = satu folder = satu branch

| Folder | Branch | Topik |
|---|---|---|
| `/var/www/3dyapp` | `main` | **Integrasi + yang dilayani website.** Jangan kerja fitur di sini |
| `/var/www/3dyapp-mkt` | `feature/div-marketing` | Lead, partner |
| `/var/www/3dyapp-tek` | `feature/div-teknisi` | Dashboard/jadwal teknisi |
| `/var/www/3dyapp-user` | `feature/user-management-avatar` | User, role, menu, avatar |
| `/var/www/3dyapp-mon` | `feature/div-mon` | Monitoring (progress per customer) |
| `/var/www/3dyapp-cus` | `feature/div-cus` | Customer |
| `/var/www/3dyapp-dash` | `feature/general-dashboard` | Dashboard umum |
| `/var/www/3dyapp-management` | `feature/management` | Management (Activity Log, assign lead) |
| `/var/www/3dyapp-sec` | `feature/security-hardening` | Security hardening |

Worktree baru juga butuh: `composer install`, symlink `.env`, dan
`ln -s /var/www/3dyapp/public/build /var/www/3dyapp-<nama>/public/build`.

Rencana pembagian berikutnya (buat worktree-nya saat mulai dikerjakan):
`feature/div-trash` (Trash), `feature/div-panel` (Admin Panel).

Butuh folder untuk topik baru?

```bash
git worktree add /var/www/3dyapp-<nama> feature/<branch>
ln -sf /var/www/3dyapp/.env /var/www/3dyapp-<nama>/.env
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

Folder utama (`/var/www/3dyapp`) harus selalu berada di branch `main`.
Setelah merge ke main: `git pull`, lalu `npm run build` jika ada perubahan CSS/JS.

## 6. Jangan commit

- Folder `backups/` (sudah di-gitignore)
- Kredensial / `.env`
- File hasil eksperimen tanpa persetujuan

## 7. Tombol (konvensi UI)

Setiap aksi punya warna bawaan — pakai pola ini konsisten di semua view
(acuan: `leads/create.blade.php`, komponen `x-icon-button`):

| Aksi | Pola standar |
|---|---|
| Simpan / Tambah / Submit utama | `bg-accent-600 hover:bg-accent-500 text-white` + shine + scale (lihat efek standar di bawah) |
| Batal (teks, di form) | `bg-accent-500 hover:bg-accent-600 text-white` + shine + scale |
| Kembali / Batal (ghost) | `border border-slate-300 text-slate-700 hover:bg-white dark:border-slate-600 dark:text-slate-200` |
| Tombol ikon (filter/reset/back/add/import/...) | `<x-icon-button>` — jangan tulis ulang, cukup `as`/`icon`/`title` |
| Lihat / detail | `bg-indigo-50 hover:bg-indigo-100 text-indigo-700` |
| Edit | `bg-blue-100 hover:bg-blue-200 text-blue-700` |
| Hapus | `bg-red-100 hover:bg-red-200 text-red-700` |
| Aksi positif (convert/approve/assign) | `bg-green-100 hover:bg-green-200 text-green-700` |

Efek standar tombol teks: `group relative overflow-hidden` + span shine
(`pointer-events-none absolute inset-0 -translate-x-full -skew-x-12 bg-gradient-to-r
from-transparent via-white/50 to-transparent transition-transform duration-700 ease-out
group-hover:translate-x-full`) + `transition-all duration-300 hover:scale-105
hover:shadow-lg active:scale-95` (tombol utama tambah `hover:shadow-accent-500/40 hover:brightness-110`).

**Larangan:** jangan pakai `bg-slate-*`, `bg-gray-*`, atau `bg-white` sebagai
background tombol — CSS dark-mode global mengoverride kelas tersebut sehingga
tombol menyatu dengan background.

Setiap menambah **kelas Tailwind baru**: jalankan ulang `npm run build` setelah
merge ke main, kalau tidak kelasnya tidak akan ter-compile.

## 8. Database Safety Rules

1. NEVER run:
   - php artisan migrate:fresh
   - php artisan migrate:refresh
   - php artisan db:wipe
   - DROP DATABASE
   - DROP TABLE

2. These commands are destructive and require explicit user approval.

3. Before running any destructive database command:
   - Explain exactly what will be deleted.
   - Ask for confirmation.
   - Do not execute until the user explicitly says yes.

4. Normal development migrations are allowed:
   - php artisan migrate
   - php artisan migrate:status
   - php artisan make:migration

5. Never assume that resetting the database is acceptable just because
   migrations or tests are failing.

6. If a migration problem occurs, investigate the cause first instead
   of using migrate:fresh as a shortcut.

## 9. Format commit & branch (wajib, biar `git log` gampang dibaca)

Format commit: `<tipe>(<divisi>/<menu>): <perubahan singkat>`

| Bagian | Pilihan |
|---|---|
| `<tipe>` | `feat` (fitur baru), `fix` (perbaikan), `style` (tampilan), `test`, `chore`, `docs` |
| `<divisi>` | `mkt \| tek \| user \| mon \| cus \| dash \| mng \| sec \| sales \| auth \| trash \| panel \| i18n \| sys` |
| `<menu>` | nama menu huruf kecil, misal `leads`, `jadwal`, `avatar`, `monitoring`. Boleh dikosongkan untuk `i18n`/`sys` |

Contoh benar:

```text
feat(mkt/leads): tambah filter status + tombol WA per baris
fix(tek/jadwal): kalender full Inggris + locale aware
style(auth/login): kecilkan card, ilustrasi dulu di mobile
feat(i18n): terjemahkan modul customer ke Inggris
chore(sys): rotate laravel.log + hapus .bak lama
```

Larangan commit:

- Tanpa scope: `update`, `fix bug`, `wip`, `tes`, `asdasd`
- Satu commit campur 2 divisi — pecah jadi 2 commit
- Satu commit > 15 file tanpa alasan — pecah per menu

Nama branch: `feature/<divisi>-<topik-singkat>` (contoh:
`feature/mkt-filter-lead`, `feature/tek-jadwal-inggris`).
Satu-satunya branch i18n adalah `feature/i18n-english` —
dilarang membuat `feature/i18n-<modul>` baru.

## 10. Kebersihan storage & worktree (anti-30GB)

Kenapa bengkak: tiap worktree menduplikat `vendor/` (~390MB) +
`node_modules/` (~620MB) ≈ 1GB. 30 worktree ≈ 30GB.

1. Maks **9 worktree aktif** sesuai tabel §1 (+ `trash` + `panel` saat
   dikerjakan). Lebih dari itu: hapus dulu sebelum bikin baru.
2. **Dilarang** folder `*.bak` di `/var/www/` — backup cukup via
   `git push`, bukan copy folder.
3. Worktree non-aktif > 7 hari: hapus `vendor/` + `node_modules/`-nya
   saja (hemat ~1GB/worktree, bisa `composer install` /
   `npm install` lagi). Jangan hapus branch-nya.
4. `laravel.log` > 10MB wajib truncate (`: > storage/logs/laravel.log`).
   Dilarang commit file log.
5. Setiap selesai hapus worktree: wajib `git worktree prune`.
6. Cek bulanan: `git worktree list` +
   `git branch --merged main` — yang sudah merge langsung hapus.

Cek cepat:

```bash
git worktree list --verbose
du -sh /var/www/3dyapp-*/vendor /var/www/3dyapp-*/node_modules 2>/dev/null | sort -rh | head
ls -lh storage/logs/laravel.log
```

## 11. Siklus hidup branch (kapan bikin, kapan hapus)

1. Bikin branch hanya dari `main` yang sudah `git pull` terbaru.
2. Branch eksperimen (login/sidebar/profile/notif): maksimal **1 aktif**,
   umur max **7 hari**.
3. Sudah merge ke `main` → hapus max 1 minggu:
   `git branch -d <branch> && git push origin --delete <branch>`.
4. Worktree `ahead` (punya commit belum di-merge): cek isi dulu
   (`git log main..<branch> --oneline`), merge yang relevan,
   discard sisanya — baru hapus worktree-nya.
5. Dilarang bikin worktree baru tanpa entry di tabel §1.

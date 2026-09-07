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

Worktree baru juga butuh: `composer install`, symlink `.env`, dan
`ln -s /var/www/3dyapp/public/build /var/www/3dyapp-<nama>/public/build`.

Rencana pembagian berikutnya (buat worktree-nya saat mulai dikerjakan):
`feature/div-cus` (Customer), `feature/div-trash` (Trash), `feature/div-panel` (Admin Panel).

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

## 7. Warna tombol (konvensi UI)

Setiap aksi punya warna bawaan — pakai kelas ini konsisten di semua view:

| Aksi | Kelas standar |
|---|---|
| Simpan / Tambah / Submit utama | `bg-blue-600 hover:bg-blue-700 text-white` |
| Lihat / detail | `bg-indigo-50 hover:bg-indigo-100 text-indigo-700` |
| Edit | `bg-blue-100 hover:bg-blue-200 text-blue-700` |
| Hapus | `bg-red-100 hover:bg-red-200 text-red-700` |
| Aksi positif (convert/approve) | `bg-green-100 hover:bg-green-200 text-green-700` |
| Batal / Kembali | `border border-slate-300 text-slate-700 hover:bg-white dark:border-slate-600 dark:text-slate-200` |

**Larangan:** jangan pakai `bg-slate-*`, `bg-gray-*`, atau `bg-white` sebagai
background tombol — CSS dark-mode global mengoverride kelas tersebut sehingga
tombol menyatu dengan background.

Setiap menambah **kelas Tailwind baru**: jalankan ulang `npm run build` setelah
merge ke main, kalau tidak kelasnya tidak akan ter-compile.

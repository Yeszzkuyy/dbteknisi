# SECURITY HARDENING — 3DY App

> Dokumentasi perubahan keamanan yang sudah diterapkan.
> Basis: branch `feature/security-hardening`, di-merge ke `main`.

## PHASE 1 — Authorization & Access Control

### Prinsip
```
AUTHENTICATION → ROLE → PERMISSION → RESOURCE AUTHORIZATION
```
- Semua halaman sensitif sudah di bawah `auth` middleware (`routes/web.php`).
- Isolasi vertikal antar divisi ditegakkan middleware `permission:view-*|manage-*` (sudah ada sebelumnya, diuji `DivisionAccessTest`/`RoleMenuTest`).
- `Gate::before` super-admin bypass (`AppServiceProvider`) tetap dipertahankan.

### Policy yang diperbaiki
| Policy | Perubahan |
|---|---|
| `CustomerPolicy` | Sebelumnya `hasRole('Super Admin'||'Admin')` (salah arah). Kini permission-based: view=`view-customer`/`view-sales`/`manage-sales`; create/update/delete=`manage-sales`; restore/forceDelete=`manage-admin`. |
| `ProjectPolicy` | Sebelumnya cuma Super Admin/Admin (akan mengunci teknisi). Kini: view=`view-teknisi`/`manage-teknisi`/`view-sales`; create/update/delete=`manage-teknisi`; restore/forceDelete=`manage-admin`. Sesama teknisi boleh melihat project milik orang lain (keputusan bisnis). |
| `LeadPolicy` | Sudah benar (permission-based), kini di-enforce. |
| `MonitoringPolicy` | Sudah benar, dipertahankan. |

### Controller yang kini enforce policy (`$this->authorize(...)`)
- `ProjectController` — viewAny/view/create/update/delete
- `ProjectDocumentController` — view/update (index, store, preview, download, destroy)
- `ProjectTaskController`, `ProjectSupportController` — update
- `CustomerController` — viewAny/view/create/update/delete
- `CustomerContactController` — update
- `LeadController` — viewAny/view/create/update/delete + attachment view/update

### IDOR yang ditutup
- `ProjectDocumentController::preview/download` — sebelumnya tanpa authorize sama sekali → siapa pun dengan `view-teknisi`/`view-sales` bisa ambil dokumen project mana pun via ganti ID URL. Kini `$this->authorize('view', $document->project)`.
- Lampiran lead: `showAttachment/downloadAttachment` kini `authorize('view', $lead)`; `destroyAttachment` → `authorize('update', $lead)` (cek ownership `lead_id` tetap).
- Dokumen project di view lead: `previewDocument/downloadDocument` kini `authorize('view', $lead)` + cek `customer_id` (tetap).

---

## PHASE 2 — Private File Storage

### Pemisahan disk
| Tipe file | Disk | Akses |
|---|---|---|
| Avatar user | `public` | via `/storage` (asset) — non-sensitif |
| Asset non-sensitif | `public` | via `/storage` |
| Lampiran Lead (BOQ) | `private` | via controller `leads.attachments.*` |
| Dokumen Project | `private` | via controller `project-documents.*` |
| Bukti Pembayaran | `private` | via controller `admin.payments.proof` |

### Konfigurasi
- Disk baru `private` di `config/filesystems.php` → `storage/app/private`. Tidak ada symlink ke `public`, jadi **tidak bisa diakses lewat URL `/storage`**.

### Upload (nama fisik acak, nama asli tetap di DB)
- `LeadController::saveAttachments/syncAttachments` → `storeAs("leads/{id}", UUID.ext, 'private')`, `file_name` asli tetap di DB.
- `ProjectDocumentController::store` → `storeAs("documents/{id}", UUID.ext, 'private')` + validasi `mimes:` ditambah (mencegah upload file berbahaya).
- `AdminController::paymentsStore` → `storeAs("payments", UUID.ext, 'private')`.

### Download/preview via controller (auth + authorize)
- `leads.attachments.show/download` — authorize view lead + cek ownership.
- `project-documents.preview/download` — authorize view project.
- `admin.payments.proof` — route baru (di bawah `permission:view-admin|manage-admin`), stream file via `Storage::disk('private')->response()`.
- Semua view diganti dari `asset('storage/...')`/`Storage::url()` → `route(...)`:
  - `admin/payments/{show,index}`, `admin/invoices/show`, `customers/show` → `admin.payments.proof`
  - `project_documents/{show,preview}` → `project-documents.preview/download`

### Migrasi file existing (JANGAN langsung hapus)
Script: `php artisan files:migrate-private [--purge-public]`
- Menyalin `LeadDocument`, `ProjectDocument`, `Payment.proof_file` dari disk `public` → `private` (path relatif sama, record DB tidak berubah).
- Default: **tidak menghapus** file public. Jalankan tanpa flag → aman, cek hasil.
- Setelah dipastikan semua tersalin, jalankan `php artisan files:migrate-private --purge-public` untuk menghapus dari public (hapus akses langsung via `/storage`).
- Catatan: file yang tidak ditemukan membuat command return FAILURE dan mencegah purge — periksa daftarnya dulu.

### Test
- `tests/Feature/SecurityAccessTest.php` (6 test baru):
  - marketing tidak bisa download/preview dokumen project (403)
  - teknisi bisa download dokumen project (read, keputusan bisnis)
  - sales bisa download tapi tidak hapus dokumen
  - teknisi tetap bisa upload dokumen (fitur tidak rusak)
  - bukti pembayaran: admin bisa lihat, marketing 403
  - super-admin bisa download dokumen apa pun (bypass)
- `LeadAttachmentTest` di-update ke disk `private`.
- Total: **92 test PASS**.

---

## Catatan operasional setelah merge
```bash
# 1. Salin file public → private (tanpa hapus dulu)
php artisan files:migrate-private

# 2. Cek hasil, lalu hapus dari public
php artisan files:migrate-private --purge-public
```

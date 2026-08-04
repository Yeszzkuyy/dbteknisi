# Dark Mode System

Project ini menggunakan **dua sistem dark mode yang berjalan bersamaan**:

1. **Tailwind `dark:` variants** — digunakan di semua file Blade.
2. **CSS custom properties + `!important` override** — didefinisikan di `<style>` inline `resources/views/layouts/app.blade.php:19-50`.

Kedua sistem harus selaras. `!important` override **mematikan** Tailwind `dark:` untuk properti tertentu, jadi menambah `dark:bg-slate-700` saja **tidak cukup** jika kelas dasarnya sudah masuk daftar override.

---

## CSS Custom Properties

### Deklarasi

```css
:root {                                         /* Light mode */
  --sidebar-bg: #fff;
  --sidebar-border: #e2e8f0;                    /* border-slate-200 */
  --card-bg: #fff;                              /* bg-white */
  --card-border: #e2e8f0;                       /* border-slate-200 */
  --card-bg-hover: #f8fafc;                     /* hover:bg-slate-50 */
  --text-primary: #1e293b;                      /* text-slate-800 */
  --text-secondary: #64748b;                    /* text-slate-500 */
  --text-muted: #94a3b8;                        /* text-slate-400 */
  --input-bg: #f1f5f9;                          /* bg-slate-100 */
  --input-border: #cbd5e1;                      /* border-slate-300 */
  --input-text: #1e293b;                        /* text-slate-800 */
}

.dark {                                          /* Dark mode */
  --sidebar-bg: #1e293b;
  --sidebar-border: #334155;                     /* border-slate-600 */
  --card-bg: #1e293b;                            /* bg-slate-800 */
  --card-border: #334155;                        /* border-slate-600 */
  --card-bg-hover: #2d3a4e;                     /* sedikit lebih terang dari card-bg */
  --text-primary: #f1f5f9;                       /* text-slate-100 */
  --text-secondary: #cbd5e1;                     /* text-slate-300 */
  --text-muted: #64748b;                         /* text-slate-500 */
  --input-bg: #243244;                          /* sedikit lebih terang dari --card-bg */
  --input-border: #475569;                       /* border-slate-500 */
  --input-text: #f1f5f9;                         /* text-slate-100 */
}
```

### Variabel & Padanan Tailwind

| Variabel        | Light value | Dark value  | Padanan Tailwind |
|-----------------|-------------|-------------|------------------|
| `--sidebar-bg`  | `#fff`      | `#1e293b`   | `bg-white` / `bg-slate-800` |
| `--sidebar-border` | `#e2e8f0` | `#334155`  | `border-slate-200` / `border-slate-600` |
| `--card-bg`     | `#fff`      | `#1e293b`   | `bg-white` / `bg-slate-800` |
| `--card-border` | `#e2e8f0`   | `#334155`   | `border-slate-200` / `border-slate-600` |
| `--card-bg-hover` | `#f8fafc` | `#2d3a4e`  | `hover:bg-slate-50` (light) / hover variant (dark) |
| `--text-primary` | `#1e293b`  | `#f1f5f9`   | `text-slate-800` / `text-slate-100` |
| `--text-secondary` | `#64748b` | `#cbd5e1` | `text-slate-500` / `text-slate-300` |
| `--text-muted`  | `#94a3b8`   | `#64748b`   | `text-slate-400` / `text-slate-500` |
| `--input-bg`    | `#f1f5f9`   | `#243244`   | `bg-slate-100` (light) / sedikit lebih terang dari `--card-bg` |
| `--input-border` | `#cbd5e1` | `#475569`  | `border-slate-300` / `border-slate-500` |
| `--input-text`  | `#1e293b`   | `#f1f5f9`   | `text-slate-800` / `text-slate-100` |

---

## !important Override Rules (app.blade.php)

Aturan berikut **mengoverride Tailwind dark: variants** untuk kelas tertentu:

```css
/* Background — static */
.dark .bg-white              { background-color: var(--card-bg) !important; }
.dark .bg-slate-50           { background-color: var(--card-bg) !important; }

/* Background — tombol sekunder & elemen abu (Kembali/Batal, pill, track) */
.dark .bg-slate-100          { background-color: #243244 !important; }
.dark .bg-slate-200          { background-color: #334155 !important; }
.dark .bg-slate-300          { background-color: #475569 !important; }
.dark .bg-gray-300           { background-color: #475569 !important; }

/* Background — hover */
.dark .hover\:bg-slate-50:hover { background-color: var(--card-bg-hover) !important; }
.dark .hover\:bg-gray-50:hover  { background-color: var(--card-bg-hover) !important; }
.dark .hover\:bg-slate-100:hover { background-color: #243244 !important; }
.dark .hover\:bg-slate-200:hover { background-color: #334155 !important; }
.dark .hover\:bg-slate-300:hover { background-color: #475569 !important; }

/* Border */
.dark .border-slate-200      { border-color: var(--card-border) !important; }
.dark .border-slate-300      { border-color: var(--input-border) !important; }

/* Text — primary */
.dark .text-slate-800,
.dark .text-gray-900,
.dark .text-slate-700        { color: var(--text-primary) !important; }

/* Text — secondary */
.dark .text-slate-600,
.dark .text-gray-600         { color: var(--text-secondary) !important; }

/* Text — muted */
.dark .text-slate-500,
.dark .text-gray-500         { color: var(--text-muted) !important; }

/* Text — table header (override text-slate-500/600 khusus th) */
.dark th.text-slate-500,
.dark th.text-slate-600      { color: var(--text-secondary) !important; }

/* Shadow */
.dark .shadow-sm             { box-shadow: 0 1px 3px 0 rgba(0,0,0,.3) !important; }
```

### Catatan penting

- Semua aturan di atas pakai `!important` — **lebih tinggi dari Tailwind `dark:` biasa**.
- Menambah `dark:bg-slate-700` pada elemen yang juga punya kelas `bg-white` atau `bg-slate-50` **tidak akan berefek** karena `!important` override menang.
- Satu-satunya cara agar perubahan dark mode dihormati adalah: **tambah aturan `!important` baru di blok `<style>` ini**, dengan selektor yang sama spesifik atau lebih spesifik dari aturan di atas.

---

## Form Input (input, textarea, select)

Semua input teks, textarea, dan select di **layout app** (bukan halaman auth yang standalone) diberi styling seragam via aturan elemen global:

```css
/* Background, border, dan teks — menang atas base @tailwindcss/forms (#fff) */
input:not([type=checkbox]):not([type=radio]):not([type=file]):not([type=color]):not([type=range]):not([type=hidden]),
select, textarea {
  background-color: var(--input-bg) !important;
  border-color: var(--input-border) !important;
  color: var(--input-text) !important;
}

/* Focus: pertahankan border biru yang sebelumnya via focus:border-blue-500 */
input:focus, select:focus, textarea:focus { border-color: #3b82f6 !important; }

/* Placeholder — reuse --text-muted */
input::placeholder, textarea::placeholder { color: var(--text-muted) !important; }
```

Catatan penting:

- **Jangan menambah `bg-white`/`border-slate-300` di blade untuk input** — aturan global `!important` di atas sudah mengatur semuanya; kelas blade seperti `focus:ring-blue-500` (ring) tetap jalan karena box-shadow tidak disentuh.
- **Tidak perlu `dark:` variants** untuk background/border input — variabel otomatis berubah lewat `.dark`.
- **Pengecualian**: checkbox, radio, file, color, range, hidden tidak kena (selektor `:not`), jadi styling checkbox `text-blue-600` tetap terlihat.
- **Halaman auth** (login/register/forgot/reset) tidak memakai `layouts/app.blade.php`, jadi tidak terpengaruh aturan ini.
- Error state input: jangan pakai `border-red-*` sebagai satu-satunya indikator — akan kalah oleh override di atas; tampilkan error sebagai teks pesan.

---

## Aturan Menambah Komponen/Tabel Baru

**1. Gunakan kelas Tailwind standar** yang sudah ada override-nya (lihat daftar di atas):
   - `bg-white` / `bg-slate-50` → otomatis gelap via `--card-bg`
   - `bg-slate-100` / `bg-slate-200` / `bg-slate-300` / `bg-gray-300` → otomatis gelap (tombol sekunder Kembali/Batal)
   - `border-slate-200` → otomatis gelap via `--card-border`
   - `border-slate-300` → otomatis gelap via `--input-border`
   - `text-slate-800` / `text-slate-700` → otomatis gelap via `--text-primary`
   - `text-slate-600` → otomatis gelap via `--text-secondary`
   - `text-slate-500` → otomatis gelap via `--text-muted`

**2. Jangan bergantung pada `dark:` variants** untuk properti yang sudah di-override. Contoh:
   ```html
   <!-- ✅ BENER — bg-slate-50 sudah di-override -->
   <div class="bg-slate-50 ...">

   <!-- ❌ MUBASIR — dark:bg-slate-700 kalah sama !important override bg-slate-50 -->
   <div class="bg-slate-50 dark:bg-slate-700 ...">
   ```

**3. Untuk properti yang TIDAK ada override-nya**, Tailwind `dark:` variants masih jalan:
   - `dark:text-white`, `dark:text-slate-200`
   - `dark:bg-slate-700`, `dark:bg-slate-600`
   - `dark:divide-slate-600`, `dark:divide-slate-700`
   - dll.

**4. Menambah override baru**: jika ada kelas baru yang butuh dark mode, tambahkan aturan `!important` di blok `<style>` `app.blade.php`, ikuti pola yang sudah ada:
   ```css
   .dark .KELAS-BARU { PROPERTY: var(--VARIABEL) !important; }
   ```

**5. Tabel — header (`<thead>`)**: pakai `bg-slate-50` agar background ikut `--card-bg` di dark mode. Teks `<th>` pakai `text-slate-500` atau `text-slate-600`, sudah di-override khusus untuk `th` dengan `--text-secondary` (lebih terang dari `text-slate-500` biasa).

**6. Rebuild CSS** setelah menambah kelas baru (terutama kelas tanpa override) agar Tailwind JIT mengenerate CSS-nya:
   ```bash
   npm run build
   ```

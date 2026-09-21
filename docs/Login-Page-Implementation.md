# Implementasi Halaman Login (Login Page)

Dokumen ini menjelaskan **file apa saja yang diubah/ditambah** dan **apa isi
perubahannya** untuk menghasilkan tampilan Login Page Tridaya App seperti
sekarang: layout *full-bleed* 2 kolom (form putih + panel ilustrasi navy
indigo), kurva lembut di tepi panel ilustrasi, karakter + 6 ikon yang
dihubungkan garis tipis ala rasi bintang, kinetic grid interaktif, dan
efek glow.

Dokumen ini juga memuat kode lengkapnya, sehingga tampilan bisa direkonstruksi
dari nol.

- **Branch kerja:** `feature/login-redesign-v2`
- **Worktree:** `/var/www/3dyapp-login`
- **Referensi desain:** `docs/Deskripsidesain.md` + gambar
  `public/images/Asset_LoginPage/Login Page UI for a CRM Website and mobile app.jpg`

---

## 1. Daftar File yang Diubah / Ditambah

| File | Status | Peran |
|---|---|---|
| `resources/views/auth/login.blade.php` | **diubah besar** | Seluruh tampilan login: CSS + markup + Alpine.js (parallax, show password, submit state) |
| `resources/js/kinetic-grid.js` | **ditambah** (sesi lain) | Canvas kinetic grid interaktif sebagai background panel ilustrasi |
| `vite.config.js` | **diubah** (sesi lain) | Daftarkan `kinetic-grid.js` sebagai entry Vite |
| `public/images/Asset_LoginPage/character2.svg` | **ditambah** | Karakter utama (siluet, PNG tertanam) |
| `public/images/Asset_LoginPage/icon1.svg` … `icon6.svg` | **ditambah** | 6 ikon bulat berkilau |
| `public/images/Asset_LoginPage/character.svg`, `mengambang.svg` | sudah ada | Aset pendukung (tidak dipakai di versi final) |

> Tidak ada perubahan pada: route, controller, request validation, model,
> migration, atau konfigurasi auth. Perubahan murni presentasi.

### Riwayat commit (urut)

| Commit | Isi |
|---|---|
| `ea7be80` | Kinetic grid interaktif (canvas vanilla, tema indigo) + daftar entry Vite |
| `91ee0ca` | Ilustrasi interaktif di atas kinetic grid (karakter, ikon, garis, parallax) |
| `78415ee` | Login *full-bleed* tanpa card; panel form dibatasi |
| `01efc7d` | Polish connector + tekstur halus panel form; split 45% : 55% |
| `9ff43ce` | Garis konektor tipis statis; `icon1` dipindah ke tangan karakter |
| `0948b57` | Garis ala rasi bintang, glow background, ikon dirapatkan |
| `519917e` | Glow ikon sesuai palet referensi; posisi ikon dirapikan agar tidak tumpang tindih |
| `c1e15f4` | Hapus lingkaran biru bulat (dianggap tidak pas) |

---

## 2. Struktur Halaman

```
<main class="login-page">            ← wrapper penuh + Alpine state (parallax)
  <div class="login-shell">          ← grid 45% : 55%
    <section class="login-form-panel">   ← KIRI: form (putih)
      <div class="login-form-inner">     ← max-width 24rem, center
        .login-brand        → logo + nama aplikasi
        .login-header       → judul + subjudul
        <x-auth-session-status>
        <form class="login-form">
          .login-field  → Email
          .login-field  → Password (+ toggle mata, caps lock warning)
          .login-remember
          .login-submit → tombol Sign In (state loading)
        .login-register     → link "Create an account"
    <aside class="login-brand-panel">    ← KANAN: ilustrasi (navy indigo)
      <canvas id="login-kinetic">        ← kinetic grid
      .login-brand-content               ← tagline
      .login-visual
        .login-visual-halo
        .login-parallax > svg.login-connectors   ← 6 garis + titik ujung
        .login-parallax > .login-character       ← karakter + icon1 di tangan
        .login-parallax > .login-icon-2..6       ← 5 ikon mengelilingi
```

### Layout inti

```css
.login-shell {
    display: grid;
    width: 100%;
    min-height: 100dvh;
    grid-template-columns: 45% 55%;   /* form : ilustrasi */
    overflow: hidden;
    background: #ffffff;
}
```

Tidak ada card dan tidak ada gradient pastel di background halaman —
halaman benar-benar *full-bleed*: kiri putih, kanan panel navy.

---

## 3. Panel Kiri — Form

### Token warna

```css
:root {
    --login-blue: #2563eb;
    --login-ink: #111827;
    --login-muted: #6b7280;
    --login-border: #d1d5db;
    --login-paper: #f8fafc;
}
```

Font memakai **Plus Jakarta Sans** (di-load dari `fonts.bunny.net`).

### Tekstur halus

Panel form diberi tekstur samar (titik + tint) lewat pseudo-element, agar
putihnya tidak terasa kosong:

```css
.login-form-panel::before {
    position: absolute;
    z-index: 0;
    inset: 0;
    background-image:
        radial-gradient(circle at 12% 18%, rgba(99, 102, 241, 0.12) 0 1px, transparent 1.5px),
        radial-gradient(circle at 82% 76%, rgba(236, 72, 153, 0.09) 0 1px, transparent 1.5px),
        linear-gradient(135deg, rgba(224, 231, 255, 0.24), transparent 34%, rgba(251, 207, 232, 0.14));
    background-size: 32px 32px, 46px 46px, auto;
    content: '';
    opacity: 0.48;
    pointer-events: none;
}
```

### Lebar form

```css
.login-form-inner {
    position: relative;
    z-index: 1;
    width: min(100%, 24rem);
    margin: 0 auto;
}
```

### Elemen form

- Input: `.login-input` (border 1px, radius `0.55rem`, min-height `3rem`,
  focus ring biru).
- Password: `.login-input-password` + tombol `.login-password-toggle`
  (ikon mata show/hide via Alpine `showPassword`).
- Peringatan Caps Lock: Alpine `capsLock` → `<p x-show="capsLock">`.
- Ingat saya: `.login-remember` (checkbox `accent-color` biru).
- Tombol: `.login-submit` — biru solid, full width, dengan state loading
  (`isSubmitting`) yang menampilkan spinner + teks "Signing in...".
- Bawah: "Not registered yet? **Create an account**".

Error validasi dirender oleh komponen `<x-input-error>` dan status sesi oleh
`<x-auth-session-status>`.

---

## 4. Panel Kanan — Ilustrasi

### Panel + kurva

```css
.login-brand-panel {
    position: relative;
    display: flex;
    min-width: 0;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    background: linear-gradient(150deg, #221a8f 0%, #2f2ac4 46%, #4f46e5 100%);
    /* kurva lembut di tepi kiri panel */
    border-radius: 28% 0 0 28% / 50% 0 0 50%;
}
```

Lapisan gelap tipis di atas panel untuk kedalaman:

```css
.login-brand-panel::after {
    position: absolute;
    z-index: 1;
    inset: 0;
    background: linear-gradient(120deg, rgba(15, 23, 42, 0.3) 0%, transparent 38%, rgba(15, 23, 42, 0.14) 100%);
    content: '';
    pointer-events: none;
}
```

### Tagline

```css
.login-brand-content {
    position: absolute;
    z-index: 3;
    top: clamp(1.75rem, 5.5%, 3rem);
    left: 50%;
    width: min(70%, 21rem);
    transform: translateX(-50%);
    color: #ffffff;
    text-align: center;
}
```

### Kanvas kinetic grid

```html
<canvas id="login-kinetic" class="login-brand-kinetic" aria-hidden="true"></canvas>
```

```css
.login-brand-kinetic {
    position: absolute;
    z-index: 0;
    inset: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
}
```

Canvas ini digerakkan `resources/js/kinetic-grid.js` (lihat bagian 7).

### Kotak komposisi ilustrasi

Semua elemen ilustrasi (garis, karakter, ikon) hidup di dalam satu kotak
`.login-visual`. **Koordinat garis SVG dan posisi ikon memakai ruang yang
sama** (persen dari kotak ini) — ini kunci agar garis tepat menyentuh ikon.

```css
.login-visual {
    position: absolute;
    z-index: 2;
    top: 22%;
    bottom: 3%;
    left: 50%;
    width: min(86%, 46rem);
    transform: translateX(-50%);
}

/* cahaya latar yang menerangi panel */
.login-visual::after {
    position: absolute;
    z-index: 0;
    inset: -16%;
    background: radial-gradient(circle, rgba(129, 140, 248, 0.6) 0%, rgba(99, 102, 241, 0.26) 40%, transparent 72%);
    filter: blur(38px);
    content: '';
    pointer-events: none;
}

.login-visual-halo {
    position: absolute;
    z-index: 0;
    top: 46%;
    left: 50%;
    width: 88%;
    aspect-ratio: 1;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(199, 210, 254, 0.52), rgba(129, 140, 248, 0.2) 45%, transparent 70%);
    filter: blur(18px);
    transform: translate(-50%, -50%);
    animation: halo-breathe 9s ease-in-out infinite;
}
```

### Garis konektor (ala rasi bintang)

Garis digambar dengan satu `<svg>` yang menutupi seluruh kotak
(`preserveAspectRatio="none"` → koordinat `viewBox 0 0 100 100` sama dengan
persen kotak, jadi 1:1 dengan posisi ikon).

```html
<svg class="login-connectors" viewBox="0 0 100 100" preserveAspectRatio="none" fill="none">
    <defs>
        <filter id="login-connector-glow" x="-80%" y="-80%" width="260%" height="260%">
            <feGaussianBlur stdDeviation="2" result="blur" />
            <feMerge>
                <feMergeNode in="blur" />
                <feMergeNode in="SourceGraphic" />
            </feMerge>
        </filter>
    </defs>

    <!-- 6 garis dari pusat (52,52) ke tiap ikon -->
    <path class="connector-line" d="M52 52C50 46 47 39 45 32" />
    <path class="connector-line" d="M52 52C44 46 34 45 25 42" />
    <path class="connector-line" d="M52 52C40 64 32 70 24 76" />
    <path class="connector-line" d="M52 52C60 40 65 34 69 28" />
    <path class="connector-line" d="M52 52C63 47 70 45 80 44" />
    <path class="connector-line" d="M52 52C47 61 47 66 46 72" />

    <!-- titik ujung, warnanya disamakan dengan glow ikon -->
    <circle cx="45" cy="32" r="1.3" fill="#BAE6FD" />
    <circle cx="25" cy="42" r="1.1" fill="#93C5FD" />
    <circle cx="24" cy="76" r="1.1" fill="#FB923C" />
    <circle cx="69" cy="28" r="1.1" fill="#F87171" />
    <circle cx="80" cy="44" r="1.1" fill="#FDE047" />
    <circle cx="46" cy="72" r="1.1" fill="#FDBA74" />
</svg>
```

```css
.connector-line {
    fill: none;
    stroke: #e6ecff;
    stroke-width: 0.35;      /* setipis benang */
    stroke-linecap: round;
    filter: url(#login-connector-glow);
    opacity: 0.9;
    /* sengaja TANPA stroke-dasharray → garis menyambung, tidak putus-putus */
}
```

### Karakter & ikon di tangan

```html
<div class="login-character">
    <img src="{{ asset('images/Asset_LoginPage/character2.svg') }}" alt="" fetchpriority="high" decoding="async">
    <img class="login-hand-icon" src="{{ asset('images/Asset_LoginPage/icon1.svg') }}" alt="" decoding="async">
</div>
```

```css
.login-character {
    position: absolute;
    z-index: 2;
    top: 52%;
    left: 52%;
    width: 31%;
    transform: translate(-50%, -50%);
    animation: character-float 7.5s ease-in-out infinite;
}

.login-hand-icon {
    position: absolute;
    z-index: 3;
    top: 10%;      /* relatif terhadap kotak karakter */
    left: 27%;
    width: 22%;    /* icon1 di tangan — jangan terlalu besar */
    transform: translate(-50%, -50%);
    filter: drop-shadow(0 0 8px rgba(186, 230, 253, 0.9)) drop-shadow(0 8px 12px rgba(15, 23, 42, 0.3));
    animation: hand-icon-float 6s ease-in-out infinite;
}
```

### Ikon mengelilingi karakter

```css
.login-icon {
    position: absolute;
    z-index: 3;
    display: grid;
    width: clamp(2.4rem, 11%, 5rem);
    aspect-ratio: 1;
    place-items: center;
    transform: translate(-50%, -50%);   /* dasar agar tetap terpusat saat animasi mati */
    animation: icon-float var(--float-duration, 6s) ease-in-out var(--float-delay, 0s) infinite;
}

.login-icon::before {                   /* pendar glow di belakang ikon */
    position: absolute;
    z-index: -1;
    inset: 4%;
    border-radius: 50%;
    background: radial-gradient(circle, var(--icon-glow, rgba(165, 180, 252, 0.85)), transparent 70%);
    filter: blur(14px);
    opacity: 1;
    content: '';
    animation: icon-pulse 4s ease-in-out var(--float-delay, 0s) infinite;
}

.login-icon img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    filter: drop-shadow(0 0 5px rgba(224, 231, 255, 0.72)) drop-shadow(0 8px 12px rgba(15, 23, 42, 0.32));
}
```

Posisi + warna glow tiap ikon (warna mengikuti palet referensi: biru muda,
biru, oranye-merah, merah, kuning, oranye):

```css
.login-icon-2 { top: 42%; left: 25%; --icon-glow: rgba(96, 165, 250, 0.95); --float-duration: 7.2s; --float-delay: -3.4s; }
.login-icon-3 { top: 76%; left: 24%; --icon-glow: rgba(249, 115, 22, 0.92); --float-duration: 5.8s; --float-delay: -2.3s; }
.login-icon-4 { top: 28%; left: 69%; --icon-glow: rgba(239, 68, 68, 0.92); --float-duration: 6.8s; --float-delay: -4.1s; }
.login-icon-5 { top: 44%; left: 80%; --icon-glow: rgba(250, 204, 21, 0.95); --float-duration: 7.6s; --float-delay: -1.8s; }
.login-icon-6 { top: 72%; left: 46%; --icon-glow: rgba(251, 146, 60, 0.95); --float-duration: 6.1s; --float-delay: -4.8s; }
```

### Peta koordinat (penting untuk maintenance)

Semua koordinat di bawah memakai ruang **persen dari `.login-visual`**.

| Elemen | Posisi (top, left) | Warna glow |
|---|---|---|
| Pusat garis | 52, 52 | — |
| `icon1` (di tangan) | 10, 27 *(relatif kotak karakter)* | `#BAE6FD` biru muda |
| `icon2` | 42, 25 | `#60A5FA` biru |
| `icon3` | 76, 24 | `#F97316` oranye-merah |
| `icon4` | 28, 69 | `#EF4444` merah |
| `icon5` | 44, 80 | `#FACC15` kuning |
| `icon6` | 72, 46 | `#FB923C` oranye |

Jarak antar-ikon sudah dijaga agar tidak tumpang tindih (jarak terdekat
≥ 19% dari lebar kotak). Kalau menggeser ikon, **wajib** ikut menyesuaikan
titik akhir `path` dan `circle` di SVG konektor, kalau tidak garis akan
meleset dari ikon.

### Animasi

```css
@keyframes character-float {
    0%, 100% { transform: translate(-50%, -50%) rotate(-1deg); }
    50% { transform: translate(-50%, calc(-50% - 0.85rem)) rotate(1deg); }
}

@keyframes icon-float {
    0%, 100% { transform: translate(-50%, -50%) rotate(-2deg); }
    50% { transform: translate(-50%, calc(-50% - 0.55rem)) rotate(3deg); }
}

@keyframes icon-pulse {
    0%, 100% { opacity: 0.55; }
    50% { opacity: 1; }
}

@keyframes halo-breathe {
    0%, 100% { opacity: 0.7; }
    50% { opacity: 1; }
}

@keyframes hand-icon-float {
    0%, 100% { transform: translate(-50%, -50%) rotate(-3deg); }
    50% { transform: translate(-50%, calc(-50% - 0.5rem)) rotate(4deg); }
}

@keyframes login-fade-in {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
```

### Parallax mengikuti kursor (Alpine.js)

Panel ilustrasi menggerakkan 3 lapisan dengan kecepatan berbeda saat kursor
bergerak. Dinonaktifkan di perangkat sentuh/layar kecil dan saat
`prefers-reduced-motion`.

```css
.login-parallax {
    position: absolute;
    inset: 0;
    pointer-events: none;
    transform: translate3d(var(--parallax-x, 0px), var(--parallax-y, 0px), 0);
    transition: transform 800ms cubic-bezier(0.16, 1, 0.3, 1);
    will-change: transform;
}
```

```html
<main class="login-page" x-data="{ /* raf, reducedMotion, compact, move(), reset() */ }">
<aside class="login-brand-panel" x-on:pointermove="move($event)" x-on:pointerleave="reset()">
```

Lapisan dan faktor geraknya: `connectors` (-4,-3), `character` (-8,-6),
`icons` (12,9).

---

## 5. Responsive & Aksesibilitas

```css
/* ≤ 1023px: satu kolom, ilustrasi di atas, form di bawah */
@media (max-width: 1023px) {
    .login-shell { display: flex; flex-direction: column; min-height: 100dvh; }
    .login-form-panel { order: 2; padding: 2rem clamp(1.25rem, 7vw, 3rem) 2.25rem; }
    .login-brand-panel { order: 1; height: clamp(13rem, 46vw, 17rem); border-radius: 0 0 2.5rem 2.5rem; }
    .login-brand-content { display: none; }
    .login-visual { top: 8%; bottom: 8%; width: min(88%, 22rem); }
    .login-character { width: 34%; }
}

@media (max-width: 420px) {
    .login-brand-panel { height: 12.5rem; }
    .login-header { margin-top: 2.75rem; }
}

/* Hormati preferensi pengguna */
@media (prefers-reduced-motion: reduce) {
    .login-character,
    .login-icon,
    .login-icon::before,
    .login-hand-icon,
    .login-visual-halo,
    .login-form-inner { animation: none !important; }

    .login-parallax { transition: none; }
}
```

Catatan aksesibilitas:

- `.login-visual` dan isinya `aria-hidden="true"` (dekoratif).
- Tombol toggle password punya `aria-label`, `aria-pressed`, `aria-controls`.
- Input error memakai `aria-invalid` + `<x-input-error>`.
- Ikon memakai `transform: translate(-50%, -50%)` sebagai nilai dasar,
  sehingga tetap terpusat walau animasi dimatikan (reduced-motion).

---

## 6. Markup Lengkap `<body>`

```html
<body>
    <main
        class="login-page"
        x-data="{
            raf: null,
            reducedMotion: false,
            compact: false,
            init() {
                this.reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                this.compact = window.matchMedia('(pointer: coarse), (max-width: 1023px)').matches;
            },
            move(event) {
                if (this.reducedMotion || this.compact) return;
                const x = (event.clientX / window.innerWidth - 0.5) * 2;
                const y = (event.clientY / window.innerHeight - 0.5) * 2;
                cancelAnimationFrame(this.raf);
                this.raf = requestAnimationFrame(() => {
                    [[this.$refs.connectors, -4, -3], [this.$refs.character, -8, -6], [this.$refs.icons, 12, 9]]
                        .forEach(([element, fx, fy]) => {
                            if (!element) return;
                            element.style.setProperty('--parallax-x', (x * fx) + 'px');
                            element.style.setProperty('--parallax-y', (y * fy) + 'px');
                        });
                });
            },
            reset() {
                if (this.reducedMotion || this.compact) return;
                cancelAnimationFrame(this.raf);
                [this.$refs.connectors, this.$refs.character, this.$refs.icons].forEach((element) => {
                    if (!element) return;
                    element.style.setProperty('--parallax-x', '0px');
                    element.style.setProperty('--parallax-y', '0px');
                });
            }
        }"
    >
        <div class="login-shell">
            <section class="login-form-panel" aria-labelledby="login-title">
                <div class="login-form-inner">
                    <div class="login-brand">
                        <div x-data="{ logoFailed: false }" class="login-brand-mark">
                            <img
                                x-show="!logoFailed"
                                x-on:error="logoFailed = true"
                                src="{{ asset('images/logo/logo-lightmode.png') }}"
                                alt="Tridaya App"
                            >
                            <span x-show="logoFailed" x-cloak class="login-brand-mark login-brand-fallback">T</span>
                        </div>
                        <span class="text-xl font-semibold tracking-tight text-gray-900">Tridaya App</span>
                    </div>

                    <div class="login-header">
                        <h1 id="login-title">Sign in to your account</h1>
                        <p>Access your workspace and keep your work connected.</p>
                    </div>

                    <x-auth-session-status
                        class="login-status rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-700"
                        :status="session('status')"
                    />

                    <form
                        method="POST"
                        action="{{ route('login') }}"
                        x-data="{ isSubmitting: false }"
                        x-on:submit="isSubmitting = true"
                        class="login-form"
                    >
                        @csrf

                        <div class="login-field">
                            <label for="email" class="login-label-row">
                                <span class="login-label">Email address</span>
                            </label>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="you@company.com"
                                required
                                autofocus
                                autocomplete="username"
                                aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                                class="login-input"
                            >
                            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                        </div>

                        <div x-data="{ showPassword: false, capsLock: false }" class="login-field">
                            <div class="login-label-row">
                                <label for="password" class="login-label">Password</label>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="login-link">Forgot password?</a>
                                @endif
                            </div>
                            <div class="login-input-wrap">
                                <input
                                    id="password"
                                    :type="showPassword ? 'text' : 'password'"
                                    name="password"
                                    placeholder="Enter your password"
                                    required
                                    autocomplete="current-password"
                                    aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                                    x-on:keyup="capsLock = $event.getModifierState('CapsLock')"
                                    x-on:keydown="capsLock = $event.getModifierState('CapsLock')"
                                    class="login-input login-input-password"
                                >
                                <button
                                    type="button"
                                    x-on:click="showPassword = !showPassword"
                                    :aria-label="showPassword ? 'Hide password' : 'Show password'"
                                    :aria-pressed="showPassword"
                                    aria-controls="password"
                                    class="login-password-toggle"
                                >
                                    <svg x-show="!showPassword" class="h-4 w-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg x-show="showPassword" x-cloak class="h-4 w-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                                    </svg>
                                </button>
                            </div>
                            <p x-show="capsLock" x-cloak role="alert" class="mt-2 rounded-md bg-amber-50 px-3 py-2 text-xs font-medium text-amber-800">Caps Lock is on</p>
                            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
                        </div>

                        <label for="remember" class="login-remember">
                            <input id="remember" type="checkbox" name="remember">
                            Remember me
                        </label>

                        <button
                            type="submit"
                            x-bind:disabled="isSubmitting"
                            x-bind:aria-busy="isSubmitting"
                            class="login-submit"
                        >
                            <span x-show="!isSubmitting">Sign In</span>
                            <span x-show="isSubmitting" x-cloak class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4 animate-spin" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                                Signing in...
                            </span>
                            <svg x-show="!isSubmitting" class="h-4 w-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                    </form>

                    @if (Route::has('register'))
                        <p class="login-register">
                            Not registered yet?
                            <a href="{{ route('register') }}" class="login-link">Create an account</a>
                        </p>
                    @endif
                </div>
            </section>

            <aside
                class="login-brand-panel"
                aria-label="Tridaya App"
                x-on:pointermove="move($event)"
                x-on:pointerleave="reset()"
            >
                <canvas id="login-kinetic" class="login-brand-kinetic" aria-hidden="true"></canvas>

                <div class="login-brand-content">
                    <h2>Connecting People, Empowering Business</h2>
                    <p>Technology and communication that keeps your team connected.</p>
                </div>

                <div class="login-visual" aria-hidden="true">
                    <div class="login-visual-halo"></div>

                    <div x-ref="connectors" class="login-parallax">
                        <svg class="login-connectors" viewBox="0 0 100 100" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <defs>
                                <filter id="login-connector-glow" x="-80%" y="-80%" width="260%" height="260%">
                                    <feGaussianBlur stdDeviation="2" result="blur" />
                                    <feMerge>
                                        <feMergeNode in="blur" />
                                        <feMergeNode in="SourceGraphic" />
                                    </feMerge>
                                </filter>
                            </defs>

                            <path class="connector-line" d="M52 52C50 46 47 39 45 32" />
                            <path class="connector-line" d="M52 52C44 46 34 45 25 42" />
                            <path class="connector-line" d="M52 52C40 64 32 70 24 76" />
                            <path class="connector-line" d="M52 52C60 40 65 34 69 28" />
                            <path class="connector-line" d="M52 52C63 47 70 45 80 44" />
                            <path class="connector-line" d="M52 52C47 61 47 66 46 72" />

                            <circle cx="45" cy="32" r="1.3" fill="#BAE6FD" />
                            <circle cx="25" cy="42" r="1.1" fill="#93C5FD" />
                            <circle cx="24" cy="76" r="1.1" fill="#FB923C" />
                            <circle cx="69" cy="28" r="1.1" fill="#F87171" />
                            <circle cx="80" cy="44" r="1.1" fill="#FDE047" />
                            <circle cx="46" cy="72" r="1.1" fill="#FDBA74" />
                        </svg>
                    </div>

                    <div x-ref="character" class="login-parallax">
                        <div class="login-character">
                            <img src="{{ asset('images/Asset_LoginPage/character2.svg') }}" alt="" fetchpriority="high" decoding="async">
                            <img class="login-hand-icon" src="{{ asset('images/Asset_LoginPage/icon1.svg') }}" alt="" decoding="async">
                        </div>
                    </div>

                    <div x-ref="icons" class="login-parallax">
                        <div class="login-icon login-icon-2"><img src="{{ asset('images/Asset_LoginPage/icon2.svg') }}" alt="" decoding="async"></div>
                        <div class="login-icon login-icon-3"><img src="{{ asset('images/Asset_LoginPage/icon3.svg') }}" alt="" decoding="async"></div>
                        <div class="login-icon login-icon-4"><img src="{{ asset('images/Asset_LoginPage/icon4.svg') }}" alt="" decoding="async"></div>
                        <div class="login-icon login-icon-5"><img src="{{ asset('images/Asset_LoginPage/icon5.svg') }}" alt="" decoding="async"></div>
                        <div class="login-icon login-icon-6"><img src="{{ asset('images/Asset_LoginPage/icon6.svg') }}" alt="" decoding="async"></div>
                    </div>
                </div>
            </aside>
        </div>
    </main>
</body>
```

---

## 7. Kinetic Grid (background interaktif)

`resources/js/kinetic-grid.js` — canvas vanilla tanpa dependensi. Titik-titik
grid terdistorsi mengikuti kursor dan membentuk ripple. Tema yang dipakai di
login adalah **indigo** (diambil dari `globalColor` elemen).

Konstanta utama:

```js
const CELL_SIZE = 55;
const INFLUENCE_RADIUS = 260;
const MAX_WARP = 24;
const DOT_SPACING = 28;
const LERP_SPEED = 0.08;

const THEMES = {
    indigo: {
        lineActive: { r: 129, g: 140, b: 248, a: 0.9 },
        nodeActive: { r: 165, g: 180, b: 252, a: 1.0 },
        glow: '129,140,248',
        ripple: '165,180,252',
    },
    /* default, monochrome */
};
```

Inisialisasi mencari elemen dengan id `login-kinetic`:

```js
const target = document.getElementById('login-kinetic');
```

### Vite

Agar ikut ter-bundle, entry ditambahkan di `vite.config.js`:

```js
laravel({
    input: [
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/js/teknisi-calendar.js',
        'resources/js/kinetic-grid.js',
    ],
    refresh: true,
}),
```

Dan di-load dari Blade:

```blade
@vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/kinetic-grid.js'])
```

> `kinetic-grid.js` dan `vite.config.js` dikerjakan oleh sesi lain
> (`feature/login-kinetic-grid`). Jangan diubah tanpa koordinasi — cukup
> pastikan entry Vite-nya ada.

---

## 8. Aset Gambar

Semua di `public/images/Asset_LoginPage/`:

| File | Isi | Dipakai |
|---|---|---|
| `character2.svg` | Karakter utama (siluet gelap, PNG tertanam) | ya |
| `icon1.svg` | Ikon chat bubble — diletakkan di tangan karakter | ya |
| `icon2.svg` | Ikon paper plane | ya |
| `icon3.svg` | Ikon megaphone | ya |
| `icon4.svg` | Ikon target/bullseye | ya |
| `icon5.svg` | Ikon bar chart | ya |
| `icon6.svg` | Ikon dokumen/folder | ya |
| `character.svg` | Versi karakter lain | tidak |
| `mengambang.svg` | Elemen mengambang | tidak |

Catatan: `icon*.svg` berisi PNG tertanam (base64) dengan filter `feColorMatrix`
— jadi ikon tampil sebagai artwork putih, dan **warnanya datang dari glow CSS**
(`--icon-glow`), bukan dari file SVG.

---

## 9. Cara Build & Verifikasi

```bash
# di worktree branch
cd /var/www/3dyapp-login

php artisan view:cache     # kompilasi Blade
npm run build              # compile Tailwind + JS (wajib bila ada kelas/JS baru)
php artisan test           # target: 213/213 lulus

git add resources/views/auth/login.blade.php
git commit -m "feat(auth): ..."
git push origin feature/login-redesign-v2
```

Setelah itu, merge ke `main` (alur satu arah sesuai `AGENTS.md`):

```bash
# di /var/www/3dyapp
git fetch origin
git merge --ff-only feature/login-redesign-v2
npm run build
php artisan view:cache
php artisan test
git push origin main
```

**Wajib `npm run build` setiap menambah kelas Tailwind baru** — kalau tidak,
kelasnya tidak ter-compile.

---

## 10. Catatan & Batasan

- Versi final **tidak memakai** card putih di tengah maupun background
  gradient pastel lavender→pink seperti di `docs/Deskripsidesain.md`.
  Itu keputusan desain yang disengaja: full-bleed 2 kolom.
- Lingkaran biru gradient (elemen "lingkaran besar" di referensi) pernah
  dicoba di commit `519917e` lalu **dihapus** di `c1e15f4` karena terlihat
  tidak pas. Kalau ingin dicoba lagi, tambahkan `.login-circle` di dalam
  `.login-visual` dengan `z-index: 1`.
- Karakter di referensi adalah pria 3D berwarna; aset yang tersedia hanya
  siluet gelap. Untuk tampilan 3D berwarna perlu aset baru.
- `login.blade.php` mendefinisikan style di dalam `<style>` (bukan Tailwind
  utility) supaya komposisi ilustrasi presisi dan tidak bergantung purge.
  Kelas Tailwind yang dipakai di markup (mis. `text-xl`, `mt-1.5`) tetap
  ikut ter-compile karena ada di file Blade.

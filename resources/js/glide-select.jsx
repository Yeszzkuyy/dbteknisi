import { createRoot } from 'react-dom/client';
import GlideSelect from './components/GlideSelect.jsx';
import './components/GlideSelect.css';

// Island entry (multi-mount): setiap [data-glide-mount] me-mount satu
// GlideSelect yang tersinkron ke hidden input di .glide-select-root yang sama.
// Opsi/nilai awal/placeholder dibaca dari data-* yang dirender Blade.

function themeProps() {
    const dark = document.documentElement.classList.contains('dark');
    let accent = '#2563eb';
    try {
        const c = window.getAppearanceColors?.();
        if (c?.accent600) accent = c.accent600;
    } catch {}
    if (accent === '#2563eb') {
        const raw = getComputedStyle(document.documentElement).getPropertyValue('--accent-600').trim();
        if (raw) accent = `rgb(${raw.replace(/\s+/g, ', ')})`;
    }
    // ponytail: warna dibaca sekali saat mount; submit/filter selalu full reload
    return dark
        ? { surfaceColor: '#0f172a', highlightColor: '#334155', textColor: '#f1f5f9', accentColor: accent }
        : { surfaceColor: '#f1f5f9', highlightColor: '#e2e8f0', textColor: '#334155', accentColor: accent };
}

function mountAll() {
    document.querySelectorAll('[data-glide-mount]:not([data-mounted])').forEach((node) => {
        node.dataset.mounted = 'true';
        const scope = node.closest('.glide-select-root');
        const hidden = scope?.querySelector('input[type="hidden"]');
        let options = [];
        try {
            options = JSON.parse(node.dataset.options ?? '[]');
        } catch {}
        if (!options.length) return;
        createRoot(node).render(
            <GlideSelect
                options={options}
                defaultValue={node.dataset.value ?? ''}
                placeholder={node.dataset.placeholder || 'Pilih…'}
                showTags={node.dataset.tags === '1'}
                size="md"
                radius={10}
                ariaLabel={node.dataset.label || 'Pilih'}
                className={node.dataset.full === '1' ? 'glide-select--full' : ''}
                onChange={(value) => {
                    if (hidden) {
                        hidden.value = value;
                        hidden.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                    // Pilihan berubah = error required basi; sembunyikan.
                    scope?.querySelector('.glide-select-error')?.setAttribute('hidden', '');
                    if (node.dataset.autosubmit === '1') scope?.closest('form')?.submit();
                }}
                {...themeProps()}
            />
        );
    });
    wireSubmitGuard();
    wireRequiredValidation();
}

const SUBMIT_BTN = 'button[type="submit"], input[type="submit"]';
const SPIN_SVG = '<svg class="gs-submit-spin" width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="3" opacity=".3"/><path d="M21 12a9 9 0 0 0-9-9" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>';

function setSubmitLocked(form, locked) {
    form.querySelectorAll(SUBMIT_BTN).forEach((b) => {
        b.disabled = locked;
        b.style.opacity = locked ? '.65' : '';
        const spin = b.querySelector(':scope > .gs-submit-spin');
        if (locked && !spin) b.insertAdjacentHTML('afterbegin', SPIN_SVG);
        if (!locked) spin?.remove();
    });
}

// Anti-duplikat: kunci tombol saat submit benar-benar jalan.
// Dibuka lagi bila digagalkan (native 'invalid', atau required di bawah).
// Didaftarkan duluan agar wireRequiredValidation bisa membukanya lagi.
function wireSubmitGuard() {
    document.querySelectorAll('form:not([data-submit-guarded])').forEach((form) => {
        if (!form.querySelector('[data-glide-mount]')) return;
        form.dataset.submitGuarded = 'true';
        form.addEventListener('invalid', () => setSubmitLocked(form, false), true);
        form.addEventListener('submit', () => setSubmitLocked(form, true));
    });
}

// Opsi A: hidden input lolos validasi browser, jadi cegat submit manual.
// Native required lain tetap jalan duluan (browser memblokir sebelum event submit).
function wireRequiredValidation() {
    document.querySelectorAll('form:not([data-glide-validated])').forEach((form) => {
        const nodes = [...form.querySelectorAll('[data-glide-mount][data-required="1"]')];
        if (!nodes.length) return;
        form.dataset.glideValidated = 'true';
        form.addEventListener('submit', (e) => {
            let firstBad = null;
            nodes.forEach((node) => {
                const scope = node.closest('.glide-select-root');
                const hidden = scope?.querySelector('input[type="hidden"]');
                const err = scope?.querySelector('.glide-select-error');
                if (hidden && !hidden.value) {
                    e.preventDefault();
                    setSubmitLocked(form, false);
                    if (err) {
                        if (node.dataset.requiredMessage) err.textContent = node.dataset.requiredMessage;
                        err.removeAttribute('hidden');
                    }
                    if (!firstBad) firstBad = node;
                } else if (err) {
                    err.setAttribute('hidden', '');
                }
            });
            if (firstBad) {
                const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                firstBad.scrollIntoView({ block: 'nearest', behavior: reduce ? 'auto' : 'smooth' });
                firstBad.querySelector('.glide-select__trigger')?.focus({ preventScroll: true });
            }
        });
    });
}

if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', mountAll);
else mountAll();

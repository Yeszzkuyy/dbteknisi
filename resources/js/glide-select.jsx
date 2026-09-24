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
                    if (node.dataset.autosubmit === '1') scope?.closest('form')?.submit();
                }}
                {...themeProps()}
            />
        );
    });
}

if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', mountAll);
else mountAll();

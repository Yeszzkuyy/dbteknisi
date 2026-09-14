import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/**/*.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
            },
            backgroundImage: {
                // Grain/noise SVG (feTurbulence) di-encode base64 — tanpa file eksternal.
                // Ubah opacity di dalam rect (0.045 = ~4.5%) atau baseFrequency (0.6–1.2) bila perlu.
                noise: "url(\"data:image/svg+xml;base64,PHN2ZyB4bWxucz0naHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmcnIHdpZHRoPScxNjAnIGhlaWdodD0nMTYwJyB2aWV3Qm94PScwIDAgMTYwIDE2MCc+PGZpbHRlciBpZD0nbic+PGZlVHVyYnVsZW5jZSB0eXBlPSdmcmFjdGFsTm9pc2UnIGJhc2VGcmVxdWVuY3k9JzAuODUnIG51bU9jdGF2ZXM9JzMnIHN0aXRjaFRpbGVzPSdzdGl0Y2gnLz48ZmVDb2xvck1hdHJpeCB0eXBlPSdzYXR1cmF0ZScgdmFsdWVzPScwJy8+PC9maWx0ZXI+PHJlY3Qgd2lkdGg9JzE2MCcgaGVpZ2h0PScxNjAnIGZpbHRlcj0ndXJsKCNuKScgb3BhY2l0eT0nMC4wNDUnLz48L3N2Zz4K\")",
            },
        },
    },

    plugins: [forms],
};

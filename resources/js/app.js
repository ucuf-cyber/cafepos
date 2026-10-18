import Alpine from 'alpinejs';
import flatpickr from 'flatpickr';
import { Indonesian } from 'flatpickr/dist/l10n/id.js';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.flatpickr = flatpickr;
window.flatpickrLocale = Indonesian;
window.Chart = Chart;

// Dark mode init
const stored = localStorage.getItem('darkMode');
const prefers = window.matchMedia('(prefers-color-scheme: dark)').matches;
if (stored === 'true' || (stored === null && prefers)) {
    document.documentElement.classList.add('dark');
}
window.toggleDarkMode = () => {
    document.documentElement.classList.toggle('dark');
    localStorage.setItem('darkMode', document.documentElement.classList.contains('dark'));
};

// Toast helper
window.toast = (msg, type = 'success') => {
    const colors = {
        success: 'bg-emerald-600',
        error: 'bg-red-500',
        info: 'bg-blue-500',
    };
    const el = document.createElement('div');
    el.className = `toast ${colors[type]} text-white px-5 py-3 rounded-xl shadow-2xl font-medium`;
    el.innerHTML = msg;
    document.body.appendChild(el);
    setTimeout(() => { el.style.opacity = '0'; el.style.transition = 'opacity 300ms'; }, 3000);
    setTimeout(() => el.remove(), 3400);
};

// Rupiah formatter
window.rupiah = (v) => 'Rp ' + Math.round(v).toLocaleString('id-ID');

Alpine.start();

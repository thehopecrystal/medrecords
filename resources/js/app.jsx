import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';
import './bootstrap';

createInertiaApp({
    title: (title) => title ? `${title} — MedRecords` : 'MedRecords',
    progress: { color: '#1E3A5F', delay: 150 },
    resolve: (name) => {
        const pages = import.meta.glob('./pages/**/*.jsx', { eager: true });
        return pages[`./pages/${name}.jsx`];
    },
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },
});

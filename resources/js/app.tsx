import '../css/app.css';

import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createRoot } from 'react-dom/client';
import { initializeTheme } from './hooks/use-appearance';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

const pages = {
    ...import.meta.glob('./pages/**/*.tsx'),
    ...import.meta.glob('./pages/**/*.jsx'),
}

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => {
        const tsxPage = `./pages/${name}.tsx`
        const jsxPage = `./pages/${name}.jsx`

        if (pages[tsxPage]) return pages[tsxPage]()
        if (pages[jsxPage]) return pages[jsxPage]()

        throw new Error(`Page not found: ${name}`)
    },
    setup({ el, App, props }) {
        const root = createRoot(el)
        root.render(<App {...props} />)
    },
    progress: {
        color: '#4B5563',
    },
})

// This will set light / dark mode on load...
initializeTheme();


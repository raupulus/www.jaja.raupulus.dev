import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/collaborators.css',
                'resources/css/contributors.css',
                'resources/css/cookies.css',
                'resources/css/footer.css',
                'resources/css/frontend.css',
                'resources/css/general_stats.css',
                'resources/css/home.css',
                'resources/css/page.css',
                'resources/css/pages.css',
                'resources/css/projects.css',
                'resources/css/social_icons.css',
                'resources/css/suggestions_form.css',
                'resources/js/alerts.js',
                'resources/js/app.js',
                'resources/js/cookies.js',
                'resources/js/navbar.js',
                'resources/js/suggestions_forms.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});

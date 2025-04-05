<?= $this->extend('layouts/default') ?>

<!------------------------------------------->

<?php $this->section('content') ?>

<!-- Alpinejs app container -->
<div id="app" x-data></div>

<!-- Pinecone Routers -->
<div id="router" x-data="router()">
    <?= ltrim(renderRouter(App\Pages\Router::$router)) ?>
</div>

<?php $this->endSection() ?>

<!------------------------------------------->

<?php $this->section('script') ?>

<script>
    // Script for main layout
    let base_url = `<?= base_url() ?>`

    // Alpine data function
    document.addEventListener('alpine:init', () => {

        // Setup Pinecone Router settings
        window.PineconeRouter.settings.basePath = '/';
        window.PineconeRouter.settings.includeQuery = false;
        window.PineconeRouter.settings.templateTargetId = 'app';

        // Setup Pinecone Router events
        document.addEventListener('pinecone-start', () => {});
        document.addEventListener('pinecone-end', () => {});
        document.addEventListener('fetch-error', (err) => {
            console.error(err)
        });

        // Global Alpine store named core
        // You can create your own desired store
        Alpine.store('core', {
            currentPage: 'home',
            sessionToken: null,
        })

        // Alpine router component
        // Define any Pinecone router handler here
        Alpine.data("router", () => ({
            async init() {
                Alpine.store('core').sessionToken = localStorage.getItem('session_token')
            },

            // Example, define handler for check login session
            isLoggedIn(context) {

            }
        }))
    })

    // Handle toggle dark mode
    function themeToggle() {
        return {
            theme: 'light',
            icon: 'bi-moon-fill',
            init() {
                const saved = localStorage.getItem('theme');
                this.theme = saved ? saved : 'light';
                this.setTheme(this.theme);
            },
            toggle() {
                this.theme = this.theme === 'dark' ? 'light' : 'dark';
                this.setTheme(this.theme);
            },
            setTheme(value) {
                document.documentElement.setAttribute('data-bs-theme', value);
                localStorage.setItem('theme', value);
                this.icon = value === 'dark' ? 'bi-sun-fill' : 'bi-moon-fill';
            }
        }
    }
</script>

<?php $this->endSection() ?>
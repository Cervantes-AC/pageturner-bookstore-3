import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('app', () => ({
    pageLoaded: false,
    isDesktop: false,
    adminDrawerOpen: false,

    init() {
        this.pageLoaded = true;
        this.isDesktop = window.innerWidth >= 1024;
        this.adminDrawerOpen = this.isDesktop && localStorage.getItem('adminDrawerOpen') === 'true';

        window.addEventListener('resize', () => {
            const wasDesktop = this.isDesktop;
            this.isDesktop = window.innerWidth >= 1024;
            if (wasDesktop && !this.isDesktop) {
                this.adminDrawerOpen = false;
            } else if (!wasDesktop && this.isDesktop) {
                this.adminDrawerOpen = localStorage.getItem('adminDrawerOpen') === 'true';
            }
        });

        this.$watch('adminDrawerOpen', (val) => {
            localStorage.setItem('adminDrawerOpen', val);
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                this.$dispatch('open-search');
            }
            if (e.key === 'g' && !e.ctrlKey && !e.metaKey) {
                const handler = (e2) => {
                    document.removeEventListener('keydown', handler);
                    if (e2.key === 'h') window.location.href = '/';
                    if (e2.key === 'b') window.location.href = '/books';
                    if (e2.key === 'c') window.location.href = '/cart';
                };
                document.addEventListener('keydown', handler);
                setTimeout(() => document.removeEventListener('keydown', handler), 1000);
            }
        });

        // Intersection Observer for scroll reveal
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

        document.querySelectorAll('.reveal-on-scroll').forEach(el => observer.observe(el));
    },

    toggleSidebar() {
        this.adminDrawerOpen = !this.adminDrawerOpen;
    },

    closeSidebar() {
        this.adminDrawerOpen = false;
    },
}));

Alpine.data('toastManager', () => ({
    toasts: [],

    init() {
        // Listen for toast events dispatched from anywhere
        window.addEventListener('toast', (e) => {
            this.addToast(e.detail.message, e.detail.type || 'info');
        });

        // Auto-dismiss after 5 seconds
        this.$watch('toasts', () => {
            if (this.toasts.length > 0) {
                setTimeout(() => {
                    if (this.toasts.length > 0) {
                        this.toasts.shift();
                    }
                }, 5000);
            }
        });
    },

    addToast(message, type = 'info') {
        this.toasts.push({ message, type });
        // Limit to 5 toasts at a time
        if (this.toasts.length > 5) {
            this.toasts.shift();
        }
    },

    removeToast(index) {
        this.toasts.splice(index, 1);
    },
}));

Alpine.start();

// Expose toast helper globally
window.showToast = (message, type = 'info') => {
    window.dispatchEvent(new CustomEvent('toast', { detail: { message, type } }));
};

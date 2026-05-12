import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('adminSidebar', () => ({
    mobileMenuOpen: false,
    adminDrawerOpen: false,
    isDesktop: false,

    init() {
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
    },

    toggleSidebar() {
        this.adminDrawerOpen = !this.adminDrawerOpen;
    },

    closeSidebar() {
        this.adminDrawerOpen = false;
    },
}));

Alpine.start();

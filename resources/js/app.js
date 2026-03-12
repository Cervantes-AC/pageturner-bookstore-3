import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Toast notification helper
window.showToast = function(message, type = 'info', description = '') {
    window.dispatchEvent(new CustomEvent('toast', {
        detail: { message, type, description }
    }));
};


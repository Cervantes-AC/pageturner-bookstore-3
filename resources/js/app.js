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

// Form submission with toast notifications
document.addEventListener('DOMContentLoaded', function() {
    // Add toast notifications for form submissions
    const forms = document.querySelectorAll('form[data-toast-success], form[data-toast-error]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const successMessage = form.dataset.toastSuccess;
            const errorMessage = form.dataset.toastError;
            
            if (successMessage) {
                // Store message in sessionStorage to show after redirect
                sessionStorage.setItem('pendingToast', JSON.stringify({
                    message: successMessage,
                    type: 'success'
                }));
            }
        });
    });
    
    // Check for pending toast after page load
    const pendingToast = sessionStorage.getItem('pendingToast');
    if (pendingToast) {
        const toast = JSON.parse(pendingToast);
        setTimeout(() => {
            window.showToast(toast.message, toast.type, toast.description || '');
        }, 100);
        sessionStorage.removeItem('pendingToast');
    }
});


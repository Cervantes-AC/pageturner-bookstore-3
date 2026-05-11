import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

// ── Intersection Observer for scroll-reveal animations ──────
document.addEventListener('DOMContentLoaded', () => {
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.1, rootMargin: '0px 0px -50px 0px' }
        );

        document.querySelectorAll('.reveal, .reveal-up, .reveal-left, .reveal-right, .reveal-scale').forEach((el) => {
            observer.observe(el);
        });
    } else {
        document.querySelectorAll('.reveal, .reveal-up, .reveal-left, .reveal-right, .reveal-scale').forEach((el) => {
            el.classList.add('is-visible');
        });
    }
});

// ── Toast notification manager ──────────────────────────────
function toastManager() {
    return {
        toasts: [],
        nextId: 1,

        init() {
            const types = ['success', 'error', 'info', 'warning'];
            types.forEach((type) => {
                const session = document.querySelector(`[data-toast-${type}]`);
                if (session) {
                    this.addToast({
                        type,
                        message: session.getAttribute(`data-toast-${type}`),
                    });
                }
            });
        },

        addToast(data) {
            const toast = {
                id: this.nextId++,
                type: data.type || 'info',
                message: data.message || '',
                description: data.description || '',
                visible: true,
            };
            this.toasts.push(toast);
            setTimeout(() => this.removeToast(toast.id), 5000);
        },

        removeToast(id) {
            const toast = this.toasts.find((t) => t.id === id);
            if (toast) {
                toast.visible = false;
                setTimeout(() => {
                    this.toasts = this.toasts.filter((t) => t.id !== id);
                }, 300);
            }
        },
    };
}

// ── Debounced search component ──────────────────────────────
function searchManager() {
    return {
        query: '',
        results: [],
        loading: false,
        open: false,
        selectedIndex: -1,
        debounceTimer: null,

        async search() {
            if (this.query.length < 2) {
                this.results = [];
                this.open = false;
                return;
            }
            this.loading = true;
            this.open = true;
            try {
                const res = await fetch(`/api/books?search=${encodeURIComponent(this.query)}&limit=5`);
                const data = await res.json();
                this.results = data.data || [];
            } catch {
                this.results = [];
            } finally {
                this.loading = false;
            }
        },

        debouncedSearch() {
            clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => this.search(), 300);
        },

        select(index) {
            const book = this.results[index];
            if (book) window.location.href = `/books/${book.id}`;
        },

        keydown(e) {
            if (!this.open) return;
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                this.selectedIndex = Math.min(this.selectedIndex + 1, this.results.length - 1);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                this.selectedIndex = Math.max(this.selectedIndex - 1, 0);
            } else if (e.key === 'Enter' && this.selectedIndex >= 0) {
                e.preventDefault();
                this.select(this.selectedIndex);
            } else if (e.key === 'Escape') {
                this.open = false;
            }
        },
    };
}

// ── Theme / Dark mode toggle ────────────────────────────────
function themeManager() {
    return {
        dark: localStorage.getItem('theme') === 'dark' ||
              (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches),

        init() {
            this.apply();
        },

        toggle() {
            this.dark = !this.dark;
            this.apply();
        },

        apply() {
            if (this.dark) {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            }
        },
    };
}

// ── Notification bell ────────────────────────────────────────
function notificationBell() {
    return {
        isOpen: false,
        notifications: [],
        unreadCount: 0,
        pollingInterval: null,
        initialized: false,

        init() {
            this.fetchNotifications();
            this.pollingInterval = setInterval(() => {
                this.fetchNotifications();
            }, 30000);
        },

        toggleDropdown() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.fetchNotifications();
            }
        },

        async fetchNotifications() {
            try {
                const response = await fetch('/notifications/unread');
                const data = await response.json();

                const oldCount = this.unreadCount;
                this.notifications = data.notifications;
                this.unreadCount = data.count;

                if (this.initialized && this.unreadCount > oldCount) {
                    const newCount = this.unreadCount - oldCount;
                    window.showToast(
                        `You have ${newCount} new notification${newCount > 1 ? 's' : ''}`,
                        'info'
                    );
                }

                this.initialized = true;
            } catch (error) {
                console.error('Failed to fetch notifications:', error);
            }
        },

        async markAsRead(notificationId) {
            try {
                await fetch(`/notifications/${notificationId}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                });

                const notification = this.notifications.find(n => n.id === notificationId);
                if (notification && !notification.read_at) {
                    notification.read_at = new Date().toISOString();
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                }

                if (notification && notification.data && notification.data.order_id) {
                    window.location.href = `/orders/${notification.data.order_id}`;
                }
            } catch (error) {
                console.error('Failed to mark notification as read:', error);
            }
        },

        async markAllAsRead() {
            try {
                await fetch('/notifications/read-all', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                });

                this.notifications.forEach(n => n.read_at = new Date().toISOString());
                this.unreadCount = 0;

                window.showToast('All notifications marked as read', 'success');
            } catch (error) {
                console.error('Failed to mark all as read:', error);
            }
        },

        getNotificationTitle(notification) {
            if (notification.type.includes('OrderStatus')) return 'Order Status Update';
            if (notification.type.includes('NewOrder')) return 'New Order Received';
            if (notification.type.includes('Review')) return 'New Review';
            return 'Notification';
        },

        getNotificationMessage(notification) {
            const data = notification.data;
            if (notification.type.includes('OrderStatus')) {
                if (data.old_status) {
                    return `Order #${data.order_id} status changed from ${data.old_status} to ${data.status}`;
                }
                return `Order #${data.order_id} - Status: ${data.status}`;
            } else if (notification.type.includes('NewOrder')) {
                return `New order #${data.order_id} - $${parseFloat(data.total_amount).toFixed(2)}`;
            } else if (notification.type.includes('Review')) {
                return data.message || 'You have a new review';
            }
            return 'You have a new notification';
        },

        formatTime(timestamp) {
            const date = new Date(timestamp);
            const now = new Date();
            const diff = Math.floor((now - date) / 1000);

            if (diff < 60) return 'Just now';
            if (diff < 3600) return `${Math.floor(diff / 60)}m ago`;
            if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`;
            if (diff < 604800) return `${Math.floor(diff / 86400)}d ago`;
            return date.toLocaleDateString();
        }
    }
}

// ── Counter animation for KPI stats ─────────────────────────
function counter(el, target, duration = 2000) {
    const start = 0;
    const startTime = performance.now();

    function update(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3);
        const current = Math.floor(start + (target - start) * eased);
        el.textContent = current.toLocaleString();
        if (progress < 1) requestAnimationFrame(update);
    }
    requestAnimationFrame(update);
}

// ── Register global Alpine components ───────────────────────
Alpine.data('toastManager', toastManager);
Alpine.data('searchManager', searchManager);
Alpine.data('themeManager', themeManager);
Alpine.data('notificationBell', notificationBell);

Alpine.start();

// ── Global helper ──────────────────────────────────────────
window.showToast = function (message, type = 'info', description = '') {
    window.dispatchEvent(
        new CustomEvent('toast', { detail: { message, type, description } })
    );
};

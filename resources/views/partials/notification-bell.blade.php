<div x-data="notificationBell()" 
     x-init="init()"
     class="relative">
    
    <!-- Bell Icon Button -->
    <button @click="toggleDropdown()" 
            class="relative p-2 text-gray-600 hover:text-primary-600 hover:bg-gray-100 rounded-lg transition-colors">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>
        
        <!-- Badge -->
        <span x-show="unreadCount > 0" 
              x-text="unreadCount > 99 ? '99+' : unreadCount"
              class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full min-w-[20px] h-5 flex items-center justify-center px-1.5 shadow-lg animate-pulse">
        </span>
    </button>
    
    <!-- Dropdown -->
    <div x-show="isOpen" 
         @click.away="isOpen = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 mt-2 w-96 bg-white rounded-xl shadow-2xl border border-gray-200 z-50 overflow-hidden"
         style="display: none;">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-primary-600 to-primary-700 px-4 py-3 flex items-center justify-between">
            <h3 class="text-white font-semibold">Notifications</h3>
            <button @click="markAllAsRead()" 
                    x-show="unreadCount > 0"
                    class="text-xs text-primary-100 hover:text-white transition-colors">
                Mark all read
            </button>
        </div>
        
        <!-- Notifications List -->
        <div class="max-h-96 overflow-y-auto">
            <template x-if="notifications.length === 0">
                <div class="p-8 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <p class="text-sm">No notifications yet</p>
                </div>
            </template>
            
            <template x-for="notification in notifications" :key="notification.id">
                <div @click="markAsRead(notification.id)" 
                     :class="notification.read_at ? 'bg-white' : 'bg-blue-50'"
                     class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors">
                    
                    <div class="flex items-start space-x-3">
                        <!-- Icon based on notification type -->
                        <div class="flex-shrink-0 mt-1">
                            <template x-if="notification.type.includes('OrderStatus')">
                                <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                    </svg>
                                </div>
                            </template>
                            <template x-if="notification.type.includes('NewOrder')">
                                <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </template>
                            <template x-if="notification.type.includes('Review')">
                                <div class="w-10 h-10 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                </div>
                            </template>
                        </div>
                        
                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900" x-text="getNotificationTitle(notification)"></p>
                            <p class="text-xs text-gray-600 mt-1" x-text="getNotificationMessage(notification)"></p>
                            <p class="text-xs text-gray-400 mt-1" x-text="formatTime(notification.created_at)"></p>
                        </div>
                        
                        <!-- Unread indicator -->
                        <div x-show="!notification.read_at" class="flex-shrink-0">
                            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
        
        <!-- Footer -->
        <div class="bg-gray-50 px-4 py-3 text-center border-t border-gray-200">
            <a href="{{ route('notifications.index') }}" 
               class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                View all notifications
            </a>
        </div>
    </div>
</div>

<script>
function notificationBell() {
    return {
        isOpen: false,
        notifications: [],
        unreadCount: 0,
        pollingInterval: null,
        
        init() {
            this.fetchNotifications();
            // Poll every 30 seconds for new notifications
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
                const response = await fetch('{{ route('notifications.unread') }}');
                const data = await response.json();
                
                // Check if there are new notifications
                const oldCount = this.unreadCount;
                this.notifications = data.notifications;
                this.unreadCount = data.count;
                
                // Show toast for new notifications
                if (this.unreadCount > oldCount && oldCount > 0) {
                    const newNotifications = this.unreadCount - oldCount;
                    window.showToast(
                        `You have ${newNotifications} new notification${newNotifications > 1 ? 's' : ''}`,
                        'info'
                    );
                }
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
                
                // Update local state
                const notification = this.notifications.find(n => n.id === notificationId);
                if (notification && !notification.read_at) {
                    notification.read_at = new Date().toISOString();
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                }
                
                // Redirect to order if it's an order notification
                if (notification.data.order_id) {
                    window.location.href = `/orders/${notification.data.order_id}`;
                }
            } catch (error) {
                console.error('Failed to mark notification as read:', error);
            }
        },
        
        async markAllAsRead() {
            try {
                await fetch('{{ route('notifications.readAll') }}', {
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
            if (notification.type.includes('OrderStatus')) {
                return 'Order Status Update';
            } else if (notification.type.includes('NewOrder')) {
                return 'New Order Received';
            } else if (notification.type.includes('Review')) {
                return 'New Review';
            }
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
            const diff = Math.floor((now - date) / 1000); // seconds
            
            if (diff < 60) return 'Just now';
            if (diff < 3600) return `${Math.floor(diff / 60)}m ago`;
            if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`;
            if (diff < 604800) return `${Math.floor(diff / 86400)}d ago`;
            
            return date.toLocaleDateString();
        }
    }
}
</script>

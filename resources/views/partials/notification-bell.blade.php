<div x-data="notificationBell()"
     x-init="init()"
     class="relative">

    {{-- Bell Icon Button --}}
    <button @click="toggleDropdown()"
            class="relative p-2 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-all duration-200 group"
            title="Notifications">
        <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>
        {{-- Badge --}}
        <span x-show="unreadCount > 0"
              x-text="unreadCount > 99 ? '99+' : unreadCount"
              class="absolute -top-0.5 -right-0.5 bg-red-500 text-white text-[10px] font-bold rounded-full min-w-[16px] h-4 flex items-center justify-center px-1 shadow-lg animate-scale-in">
        </span>
    </button>

    {{-- Dropdown --}}
    <div x-show="isOpen"
         @click.outside="isOpen = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
         class="absolute right-0 mt-2 w-96 bg-white rounded-xl shadow-soft border border-gray-200 z-50 overflow-hidden"
         style="display: none;">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-primary-600 to-primary-700 px-4 py-3 flex items-center justify-between">
            <h3 class="text-white font-semibold text-sm">Notifications</h3>
            <button @click="markAllAsRead()"
                    x-show="unreadCount > 0"
                    class="text-xs text-primary-100 hover:text-white transition-colors">
                Mark all read
            </button>
        </div>

        {{-- Notifications List --}}
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
                     :class="notification.read_at ? 'bg-white' : 'bg-blue-50/60'"
                     class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors">

                    <div class="flex items-start space-x-3">
                        {{-- Icon based on notification type --}}
                        <div class="flex-shrink-0 mt-1">
                            <template x-if="notification.type.includes('OrderStatus')">
                                <div class="w-9 h-9 bg-gradient-to-br from-primary-500 to-primary-600 rounded-full flex items-center justify-center shadow-sm">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                    </svg>
                                </div>
                            </template>
                            <template x-if="notification.type.includes('NewOrder')">
                                <div class="w-9 h-9 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-full flex items-center justify-center shadow-sm">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </template>
                            <template x-if="notification.type.includes('Review')">
                                <div class="w-9 h-9 bg-gradient-to-br from-amber-500 to-amber-600 rounded-full flex items-center justify-center shadow-sm">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                </div>
                            </template>
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900" x-text="getNotificationTitle(notification)"></p>
                            <p class="text-xs text-gray-600 mt-0.5" x-text="getNotificationMessage(notification)"></p>
                            <p class="text-xs text-gray-400 mt-1" x-text="formatTime(notification.created_at)"></p>
                        </div>

                        {{-- Unread indicator --}}
                        <div x-show="!notification.read_at" class="flex-shrink-0 pt-1">
                            <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse-soft"></div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Footer --}}
        <div class="bg-gray-50 px-4 py-3 text-center border-t border-gray-200">
            <a href="{{ route('notifications.index') }}"
               class="text-sm text-primary-600 hover:text-primary-700 font-medium transition-colors">
                View all notifications
            </a>
        </div>
    </div>
</div>

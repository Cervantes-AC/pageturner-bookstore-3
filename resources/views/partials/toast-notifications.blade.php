<div x-data="toastManager()" 
     x-init="init()"
     @toast.window="addToast($event.detail)"
     class="fixed top-4 right-4 z-50 space-y-3 max-w-sm w-full pointer-events-none">
    
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.visible"
             x-transition:enter="transform transition ease-out duration-300"
             x-transition:enter-start="translate-x-full opacity-0"
             x-transition:enter-end="translate-x-0 opacity-100"
             x-transition:leave="transform transition ease-in duration-200"
             x-transition:leave-start="translate-x-0 opacity-100"
             x-transition:leave-end="translate-x-full opacity-0"
             class="pointer-events-auto">
            
            <div :class="{
                'bg-gradient-to-r from-green-50 to-emerald-50 border-green-200 text-green-800': toast.type === 'success',
                'bg-gradient-to-r from-red-50 to-rose-50 border-red-200 text-red-800': toast.type === 'error',
                'bg-gradient-to-r from-blue-50 to-sky-50 border-blue-200 text-blue-800': toast.type === 'info',
                'bg-gradient-to-r from-yellow-50 to-amber-50 border-yellow-200 text-yellow-800': toast.type === 'warning'
            }" class="border rounded-xl shadow-lg p-4 flex items-start space-x-3 backdrop-blur-sm">
                
                <!-- Icon -->
                <div class="flex-shrink-0 mt-0.5">
                    <template x-if="toast.type === 'success'">
                        <svg class="w-5 h-5" :class="{'text-green-600': toast.type === 'success'}" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </template>
                    <template x-if="toast.type === 'error'">
                        <svg class="w-5 h-5" :class="{'text-red-600': toast.type === 'error'}" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </template>
                    <template x-if="toast.type === 'info'">
                        <svg class="w-5 h-5" :class="{'text-blue-600': toast.type === 'info'}" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </template>
                    <template x-if="toast.type === 'warning'">
                        <svg class="w-5 h-5" :class="{'text-yellow-600': toast.type === 'warning'}" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </template>
                </div>
                
                <!-- Content -->
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium" x-text="toast.message"></p>
                    <p x-show="toast.description" class="text-xs mt-1 opacity-90" x-text="toast.description"></p>
                </div>
                
                <!-- Close Button -->
                <button @click="removeToast(toast.id)" 
                        class="flex-shrink-0 text-current opacity-60 hover:opacity-100 transition-opacity">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>
    </template>
</div>

<script>
function toastManager() {
    return {
        toasts: [],
        nextId: 1,
        
        init() {
            // Check for flash messages on page load
            @if(session('success'))
                this.addToast({
                    type: 'success',
                    message: @json(session('success'))
                });
            @endif
            
            @if(session('error'))
                this.addToast({
                    type: 'error',
                    message: @json(session('error'))
                });
            @endif
            
            @if(session('info'))
                this.addToast({
                    type: 'info',
                    message: @json(session('info'))
                });
            @endif
            
            @if(session('warning'))
                this.addToast({
                    type: 'warning',
                    message: @json(session('warning'))
                });
            @endif
        },
        
        addToast(data) {
            const toast = {
                id: this.nextId++,
                type: data.type || 'info',
                message: data.message || '',
                description: data.description || '',
                visible: true
            };
            
            this.toasts.push(toast);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                this.removeToast(toast.id);
            }, 5000);
        },
        
        removeToast(id) {
            const index = this.toasts.findIndex(t => t.id === id);
            if (index !== -1) {
                this.toasts[index].visible = false;
                // Remove from array after animation
                setTimeout(() => {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                }, 300);
            }
        }
    }
}

// Global function to show toast from anywhere
window.showToast = function(message, type = 'info', description = '') {
    window.dispatchEvent(new CustomEvent('toast', {
        detail: { message, type, description }
    }));
};
</script>

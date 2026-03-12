@extends('layouts.app')

@section('title', 'Notifications')

@section('header')
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-900">Notifications</h1>
        @if($notifications->total() > 0)
            <form method="POST" action="{{ route('notifications.readAll') }}">
                @csrf
                <button type="submit" class="btn-secondary text-sm">
                    Mark all as read
                </button>
            </form>
        @endif
    </div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    @if($notifications->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            @foreach($notifications as $notification)
                <div class="border-b border-gray-100 last:border-b-0 {{ $notification->read_at ? 'bg-white' : 'bg-blue-50' }}">
                    <div class="p-6 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start space-x-4">
                            <!-- Icon -->
                            <div class="flex-shrink-0">
                                @if(str_contains($notification->type, 'OrderStatus'))
                                    <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-600 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                        </svg>
                                    </div>
                                @elseif(str_contains($notification->type, 'NewOrder'))
                                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                @elseif(str_contains($notification->type, 'Review'))
                                    <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    </div>
                                @else
                                    <div class="w-12 h-12 bg-gradient-to-br from-gray-500 to-gray-600 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3 class="text-base font-semibold text-gray-900">
                                            @if(str_contains($notification->type, 'OrderStatus'))
                                                Order Status Update
                                            @elseif(str_contains($notification->type, 'NewOrder'))
                                                New Order Received
                                            @elseif(str_contains($notification->type, 'Review'))
                                                New Review
                                            @else
                                                Notification
                                            @endif
                                        </h3>
                                        
                                        <p class="text-sm text-gray-600 mt-1">
                                            @if(str_contains($notification->type, 'OrderStatus'))
                                                @if(isset($notification->data['old_status']))
                                                    Order #{{ $notification->data['order_id'] }} status changed from 
                                                    <span class="font-medium">{{ ucfirst($notification->data['old_status']) }}</span> to 
                                                    <span class="font-medium">{{ ucfirst($notification->data['status']) }}</span>
                                                @else
                                                    Order #{{ $notification->data['order_id'] }} - Status: 
                                                    <span class="font-medium">{{ ucfirst($notification->data['status']) }}</span>
                                                @endif
                                            @elseif(str_contains($notification->type, 'NewOrder'))
                                                New order #{{ $notification->data['order_id'] }} - 
                                                ${{ number_format($notification->data['total_amount'], 2) }}
                                            @elseif(str_contains($notification->type, 'Review'))
                                                {{ $notification->data['message'] ?? 'You have a new review' }}
                                            @else
                                                You have a new notification
                                            @endif
                                        </p>
                                        
                                        <p class="text-xs text-gray-400 mt-2">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    
                                    @if(!$notification->read_at)
                                        <div class="ml-4">
                                            <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Action Button -->
                                @if(isset($notification->data['order_id']))
                                    <div class="mt-3">
                                        <a href="{{ route('orders.show', $notification->data['order_id']) }}" 
                                           class="inline-flex items-center text-sm text-primary-600 hover:text-primary-700 font-medium">
                                            View Order
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No notifications yet</h3>
            <p class="text-gray-600">When you receive notifications, they'll appear here.</p>
        </div>
    @endif
</div>
@endsection

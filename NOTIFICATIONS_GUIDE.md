# Popup Notifications System

This guide explains the comprehensive notification system implemented in PageTurner.

## Features

### 1. Toast Notifications (Popup)
- Auto-dismissing popup notifications in the top-right corner
- 4 types: success, error, info, warning
- Automatically shows for all flash messages
- Can be triggered manually from JavaScript
- 5-second auto-dismiss with manual close option

### 2. Real-time Order Status Notifications
- Notification bell icon in navigation bar
- Badge showing unread notification count
- Polls for new notifications every 30 seconds
- Dropdown showing recent notifications
- Click notification to view order details
- Mark individual or all notifications as read

### 3. Database Notifications
- All notifications stored in database
- Full notification history page
- Email notifications for order status changes
- Notifications for: order placement, status changes, new reviews

## Usage

### Backend - Flash Messages

In your controllers, use flash messages as usual:

```php
// Success notification
return redirect()->back()->with('success', 'Book added to cart!');

// Error notification
return redirect()->back()->with('error', 'Not enough stock available.');

// Info notification
return redirect()->back()->with('info', 'Your order is being processed.');

// Warning notification
return redirect()->back()->with('warning', 'Stock is running low.');
```

### Frontend - JavaScript

Trigger toast notifications from JavaScript:

```javascript
// Basic usage
window.showToast('Operation completed!', 'success');

// With description
window.showToast('Order placed', 'success', 'Your order #123 has been confirmed');

// Different types
window.showToast('Error occurred', 'error');
window.showToast('Please note', 'info');
window.showToast('Be careful', 'warning');
```

### Forms - Data Attributes

Add toast notifications to forms:

```html
<form action="/cart/add" method="POST" data-toast-success="Item added to cart!">
    @csrf
    <!-- form fields -->
    <button type="submit">Add to Cart</button>
</form>
```

## Order Status Notifications

When an order status changes, customers automatically receive:

1. **Toast Notification** - Popup in top-right corner
2. **Bell Notification** - Badge on notification bell
3. **Email Notification** - Sent to customer's email
4. **Database Record** - Stored for history

### Triggering Order Notifications

In `OrderController`:

```php
use App\Notifications\OrderStatusNotification;

// When updating order status
$oldStatus = $order->status;
$order->update(['status' => $request->status]);

// Notify customer
$order->user->notify(new OrderStatusNotification($order, $oldStatus));
```

## Notification Bell

The notification bell component:
- Shows unread count badge
- Polls every 30 seconds for new notifications
- Dropdown with recent notifications
- Click to mark as read and view details
- "Mark all as read" button

## Notification Types

### OrderStatusNotification
- Sent when order status changes
- Shows old and new status
- Links to order details
- Email + database notification

### NewOrderNotification
- Sent to admins when new order placed
- Shows order total and customer info
- Links to order management

### NewReviewNotification
- Sent when book receives new review
- Shows rating and reviewer
- Links to book page

## Routes

```php
// View all notifications
GET /notifications

// Get unread notifications (AJAX)
GET /notifications/unread

// Mark notification as read
POST /notifications/{id}/read

// Mark all as read
POST /notifications/read-all
```

## Customization

### Toast Duration

Edit `resources/views/partials/toast-notifications.blade.php`:

```javascript
// Change auto-dismiss time (default: 5000ms)
setTimeout(() => {
    this.removeToast(toast.id);
}, 5000); // Change this value
```

### Polling Interval

Edit `resources/views/partials/notification-bell.blade.php`:

```javascript
// Change polling interval (default: 30000ms = 30 seconds)
this.pollingInterval = setInterval(() => {
    this.fetchNotifications();
}, 30000); // Change this value
```

### Toast Position

Edit `resources/views/partials/toast-notifications.blade.php`:

```html
<!-- Change position classes -->
<div class="fixed top-4 right-4 z-50">
    <!-- Change to: -->
    <!-- top-4 left-4 (top-left) -->
    <!-- bottom-4 right-4 (bottom-right) -->
    <!-- bottom-4 left-4 (bottom-left) -->
</div>
```

## Testing

### Test Toast Notifications

```javascript
// Open browser console and run:
window.showToast('Test success', 'success');
window.showToast('Test error', 'error');
window.showToast('Test info', 'info');
window.showToast('Test warning', 'warning');
```

### Test Order Notifications

1. Place an order as a customer
2. Login as admin
3. Change order status
4. Customer will receive:
   - Toast notification (if online)
   - Bell notification
   - Email notification

### Test Notification Bell

1. Login as a user
2. Have another user/admin trigger a notification
3. Wait up to 30 seconds for polling
4. Bell badge should update
5. Click bell to see dropdown

## Files Modified/Created

### Created Files
- `resources/views/partials/toast-notifications.blade.php` - Toast component
- `resources/views/partials/notification-bell.blade.php` - Bell component
- `resources/views/notifications/index.blade.php` - Notifications page
- `app/Http/Controllers/NotificationController.php` - Controller

### Modified Files
- `resources/views/layouts/app.blade.php` - Added toast component
- `resources/views/layouts/guest.blade.php` - Added toast component
- `resources/views/partials/navigation.blade.php` - Added notification bell
- `resources/js/app.js` - Added toast helpers
- `routes/web.php` - Added notification routes
- `resources/views/cart/index.blade.php` - Added toast attributes

## Browser Compatibility

- Modern browsers (Chrome, Firefox, Safari, Edge)
- Requires JavaScript enabled
- Uses Alpine.js for reactivity
- CSS transitions for animations

## Performance

- Lightweight components
- Efficient polling (30s intervals)
- Auto-cleanup of dismissed toasts
- Minimal database queries
- Cached notification counts

## Security

- CSRF protection on all routes
- Authorization checks (users see only their notifications)
- XSS protection (escaped output)
- Rate limiting on notification endpoints

## Troubleshooting

### Toasts not showing
1. Check browser console for errors
2. Verify Alpine.js is loaded
3. Check if toast component is included in layout

### Bell not updating
1. Check network tab for API calls
2. Verify route `/notifications/unread` is accessible
3. Check browser console for errors

### Notifications not saving
1. Run migrations: `php artisan migrate`
2. Check `notifications` table exists
3. Verify notification channels in notification class

## Future Enhancements

- WebSocket support for instant notifications
- Push notifications (browser)
- Sound alerts
- Notification preferences
- Notification grouping
- Read receipts

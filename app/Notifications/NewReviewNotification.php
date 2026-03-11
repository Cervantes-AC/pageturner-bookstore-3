<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewReviewNotification extends Notification
{
    use Queueable;

    public $review;

    /**
     * Create a new notification instance.
     */
    public function __construct(Review $review)
    {
        $this->review = $review;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Review Submitted - ' . $this->review->book->title)
            ->greeting('New Review Alert!')
            ->line('A new review has been submitted for: **' . $this->review->book->title . '**')
            ->line('Reviewer: ' . $this->review->user->name)
            ->line('Rating: ' . str_repeat('⭐', $this->review->rating) . ' (' . $this->review->rating . '/5)')
            ->when($this->review->comment, function ($message) {
                return $message->line('Comment: "' . Str::limit($this->review->comment, 100) . '"');
            })
            ->action('View Book & Review', route('books.show', $this->review->book))
            ->line('Review submitted on: ' . $this->review->created_at->format('M d, Y \a\t g:i A'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'review_id' => $this->review->id,
            'book_title' => $this->review->book->title,
            'book_id' => $this->review->book->id,
            'reviewer_name' => $this->review->user->name,
            'rating' => $this->review->rating,
            'comment' => $this->review->comment,
        ];
    }
}
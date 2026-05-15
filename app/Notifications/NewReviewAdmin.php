<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewReviewAdmin extends Notification
{
    use Queueable;

    public function __construct(public Review $review) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Review Submitted')
            ->greeting('Hi Admin!')
            ->line('A new review has been submitted.')
            ->line('Book: ' . ($this->review->book->title ?? 'N/A'))
            ->line('Rating: ' . $this->review->rating . '/5')
            ->line('Comment: ' . ($this->review->comment ?? 'No comment'))
            ->line('By: ' . ($this->review->user->name ?? 'N/A'))
            ->action('View Book', url('/books/' . ($this->review->book_id)))
            ->line('Thank you.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'review_id' => $this->review->id,
            'book_id' => $this->review->book_id,
            'book_title' => $this->review->book->title ?? 'N/A',
            'rating' => $this->review->rating,
            'user_name' => $this->review->user->name ?? 'N/A',
            'message' => 'New ' . $this->review->rating . '-star review on "' . ($this->review->book->title ?? 'N/A') . '"',
        ];
    }
}

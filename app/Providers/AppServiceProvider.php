<?php

namespace App\Providers;

use App\Models\Book;
use App\Models\User;
use App\Notifications\CriticalAuditEventNotification;
use App\Observers\BookObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use OwenIt\Auditing\Models\Audit;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();
        $this->configureAuditChecksums();
        $this->registerModelObservers();

        // Configure mail transport to bypass SSL verification for local development
        if (config('mail.default') === 'smtp') {
            $this->app->afterResolving(\Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport::class, function ($transport) {
                $stream = $transport->getStream();
                if (method_exists($stream, 'setStreamOptions')) {
                    $stream->setStreamOptions([
                        'ssl' => [
                            'verify_peer'       => false,
                            'verify_peer_name'  => false,
                            'allow_self_signed' => true,
                        ],
                    ]);
                }
            });
        }
    }

    protected function registerModelObservers(): void
    {
        Book::observe(BookObserver::class);
    }

    protected function configureRateLimiting(): void
    {
        // Auth endpoints: 10 req/min, 2 per second burst protection
        RateLimiter::for('auth', function (Request $request) {
            return [
                Limit::perMinute(10)->by($request->ip()),
                Limit::perSecond(2)->by('auth-burst|' . $request->ip()),
            ];
        });

        // Public browsing: 30 req/min, 5 per second burst protection
        RateLimiter::for('public', function (Request $request) {
            return [
                Limit::perMinute(30)->by($request->ip())
                    ->response(fn($request, $headers) => response()->json([
                        'message'     => 'Rate limit exceeded. Public API allows 30 requests/minute.',
                        'retry_after' => $headers['Retry-After'] ?? 60,
                    ], 429, $headers)),
                Limit::perSecond(5)->by('public-burst|' . $request->ip()),
            ];
        });

        // Tiered API limits based on user role
        RateLimiter::for('api', function (Request $request) {
            $user = $request->user();

            if (!$user) {
                return [
                    Limit::perMinute(30)->by($request->ip()),
                    Limit::perSecond(5)->by('api-burst|' . $request->ip()),
                ];
            }

            if ($user->isAdmin()) {
                return [
                    Limit::perMinute(1000)->by('admin|' . $user->id),
                    Limit::perSecond(20)->by('admin-burst|' . $user->id),
                ];
            }

            if ($user->role === 'premium') {
                return [
                    Limit::perMinute(300)->by('premium|' . $user->id),
                    Limit::perSecond(10)->by('premium-burst|' . $user->id),
                ];
            }

            // Standard authenticated users: 60/min, 5/sec burst
            return [
                Limit::perMinute(60)->by('standard|' . $user->id),
                Limit::perSecond(5)->by('standard-burst|' . $user->id),
            ];
        });

        // Search endpoint: 30 req/min, with Redis-backed tiered limits
        RateLimiter::for('search', function (Request $request) {
            $user = $request->user();
            $tier = match (true) {
                $user?->isAdmin() => 'admin',
                $user?->role === 'premium' => 'premium',
                $user !== null => 'standard',
                default => 'public',
            };

            $limits = [
                'public' => 30,
                'standard' => 60,
                'premium' => 300,
                'admin' => 1000,
            ];

            return Limit::perMinute($limits[$tier])
                ->by($request->user()?->id ?: $request->ip());
        });
    }

    /**
     * Attach a SHA-256 checksum to every new audit record for tamper-proof storage.
     * Also send real-time email alerts for critical security events.
     */
    protected function configureAuditChecksums(): void
    {
        Audit::creating(function (Audit $audit) {
            // Compute tamper-proof checksum
            $payload = [
                'user_id'        => $audit->user_id,
                'event'          => $audit->event,
                'auditable_type' => $audit->auditable_type,
                'auditable_id'   => $audit->auditable_id,
                'old_values'     => $audit->old_values,
                'new_values'     => $audit->new_values,
                'ip_address'     => $audit->ip_address,
                'url'            => $audit->url,
            ];
            $audit->checksum = hash('sha256', json_encode($payload));
        });

        Audit::created(function (Audit $audit) {
            // Send real-time alert for critical events:
            // - role changes on User model
            // - any admin-performed deletion
            $isCritical = false;

            if ($audit->auditable_type === 'App\\Models\\User') {
                $changed = array_keys($audit->new_values ?? []);
                if (in_array('role', $changed) || in_array('two_factor_enabled', $changed)) {
                    $isCritical = true;
                }
            }

            if ($audit->event === 'deleted' && $audit->user_id) {
                $actor = User::find($audit->user_id);
                if ($actor?->isAdmin()) {
                    $isCritical = true;
                }
            }

            if ($isCritical) {
                try {
                    $admins = User::where('role', 'admin')->get();
                    foreach ($admins as $admin) {
                        $admin->notify(new CriticalAuditEventNotification($audit));
                    }
                } catch (\Exception $e) {
                    // Never let notification failure break the request
                    \Illuminate\Support\Facades\Log::warning('Critical audit notification failed: ' . $e->getMessage());
                }
            }
        });
    }
}

<?php

namespace App\Providers;

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

    protected function configureRateLimiting(): void
    {
        // Auth endpoints: 10 req/min for all users
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(10)
                ->by($request->ip())
                ->response(fn() => response()->json([
                    'message' => 'Too many authentication attempts. Please wait before trying again.',
                    'retry_after' => 60,
                ], 429));
        });

        // Public browsing: 30 req/min by IP
        RateLimiter::for('public', function (Request $request) {
            return Limit::perMinute(30)
                ->by($request->ip())
                ->response(fn() => response()->json([
                    'message' => 'Rate limit exceeded. Public API allows 30 requests/minute.',
                    'retry_after' => 60,
                ], 429));
        });

        // Tiered API limits based on user role
        RateLimiter::for('api', function (Request $request) {
            $user = $request->user();

            if (!$user) {
                return Limit::perMinute(30)->by($request->ip());
            }

            if ($user->isAdmin()) {
                return Limit::perMinute(1000)->by('admin|' . $user->id);
            }

            // Premium users (role = premium) get 300/min
            if ($user->role === 'premium') {
                return Limit::perMinute(300)->by('premium|' . $user->id);
            }

            // Standard authenticated users: 60/min
            return Limit::perMinute(60)->by('standard|' . $user->id);
        });
    }

    /**
     * Attach a SHA-256 checksum to every new audit record for tamper-proof storage.
     */
    protected function configureAuditChecksums(): void
    {
        Audit::creating(function (Audit $audit) {
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
    }
}

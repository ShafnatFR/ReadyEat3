# Sentry Integration Guide

## 🔍 Error Tracking with Sentry

### Installation

```bash
composer require sentry/sentry-laravel
```

### Configuration

1. **Publish Config:**
```bash
php artisan vendor:publish --provider="Sentry\Laravel\ServiceProvider"
```

2. **Set DSN in .env:**
```env
SENTRY_LARAVEL_DSN=https://your-dsn@sentry.io/project-id
SENTRY_TRACES_SAMPLE_RATE=0.2
```

3. **Configure `config/sentry.php`:**
```php
return [
    'dsn' => env('SENTRY_LARAVEL_DSN'),
    
    // Release tracking
    'release' => env('APP_VERSION', '1.0.0'),
    
    // Environment
    'environment' => env('APP_ENV', 'production'),
    
    // Sample rate for performance monitoring
    'traces_sample_rate' => (float) env('SENTRY_TRACES_SAMPLE_RATE', 0.2),
    
    // Breadcrumbs
    'breadcrumbs' => [
        'logs' => true,
        'cache' => true,
        'livewire' => true,
        'sql_queries' => true,
        'sql_bindings' => true,
        'queue_info' => true,
        'command_info' => true,
    ],
    
    // Send default PII (Personally Identifiable Information)
    'send_default_pii' => false,
    
    // Integrations
    'integrations' => [
        // Performance monitoring
        \Sentry\Integration\RequestIntegration::class,
        \Sentry\Integration\TransactionIntegration::class,
        
        // Error tracking
        \Sentry\Integration\ErrorListenerIntegration::class,
        \Sentry\Integration\ExceptionListenerIntegration::class,
    ],
];
```

### Usage Examples

#### 1. Automatic Error Tracking
Sentry automatically catches unhandled exceptions:
```php
// This will be automatically sent to Sentry
throw new \Exception('Something went wrong!');
```

#### 2. Manual Error Reporting
```php
use Sentry\Laravel\Integration;

try {
    // Risky operation
    $result = $this->performOperation();
} catch (\Exception $e) {
    // Report to Sentry with context
    app('sentry')->captureException($e, [
        'extra' => [
            'user_id' => Auth::id(),
            'order_id' => $orderId,
            'context' => 'checkout_process'
        ]
    ]);
    
    // Show user-friendly message
    return back()->with('error', 'Operation failed');
}
```

#### 3. Custom Messages
```php
app('sentry')->captureMessage('Custom event occurred', [
    'level' => \Sentry\Severity::warning(),
    'extra' => ['detail' => 'value']
]);
```

#### 4. User Context
```php
// Set user context (automatically done for authenticated users)
\Sentry\configureScope(function (\Sentry\State\Scope $scope): void {
    $scope->setUser([
        'id' => Auth::id(),
        'email' => Auth::user()->email,
        'username' => Auth::user()->name,
    ]);
});
```

#### 5. Tags for Filtering
```php
\Sentry\configureScope(function (\Sentry\State\Scope $scope): void {
    $scope->setTag('feature', 'checkout');
    $scope->setTag('payment_method', 'qris');
});
```

### Integration Points in ReadyEat3

#### OrderController
```php
// Already implemented in catch blocks:
catch (\Exception $e) {
    DB::rollBack();
    
    // Sentry will auto-capture this
    Log::error('Order creation failed', [
        'user_id' => Auth::id(),
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    
    return back()->with('error', 'Error message');
}
```

#### AdminController
```php
// Payment verification errors
try {
    $order->status = 'ready_for_pickup';
    $order->save();
} catch (\Exception $e) {
    // Auto-tracked by Sentry
    \Log::error('Order update failed', ['order_id' => $order->id]);
}
```

### Dashboard Setup

1. **Create Sentry Account:**
   - Visit: https://sentry.io
   - Create new project (Laravel)
   - Copy DSN

2. **View Errors:**
   - Issues → See all caught errors
   - Performance → Monitor response times
   - Releases → Track deployments

3. **Alerts:**
   - Set up email/Slack notifications
   - Configure error thresholds
   - Create custom rules

### Testing

```bash
# Test Sentry integration
php artisan sentry:test

# Expected output:
# [Sentry] DSN correctly configured.
# [Sentry] Generating test event...
# [Sentry] Sending test event...
# [Sentry] Event sent: [event ID]
```

### Performance Monitoring

```php
// Manually create transaction
$transaction = \Sentry\startTransaction([
    'name' => 'checkout_process',
    'op' => 'http.server'
]);

// Your code here
$this->processCheckout();

// Finish transaction
$transaction->finish();
```

### Best Practices

1. **Don't Log Sensitive Data:**
```php
// ❌ Bad
\Log::error('Payment failed', ['card_number' => $cardNumber]);

// ✅ Good
\Log::error('Payment failed', ['order_id' => $orderId]);
```

2. **Use Breadcrumbs:**
```php
\Sentry\addBreadcrumb([
    'category' => 'order',
    'message' => 'Order validation started',
    'level' => 'info'
]);
```

3. **Filter Sensitive Data:**
```php
// config/sentry.php
'before_send' => function (\Sentry\Event $event): ?\Sentry\Event {
    $event->setExtra(array_diff_key(
        $event->getExtra(),
        array_flip(['password', 'credit_card'])
    ));
    return $event;
},
```

### Cost Optimization

```env
# Production
SENTRY_TRACES_SAMPLE_RATE=0.2  # Sample 20% of transactions

# Staging
SENTRY_TRACES_SAMPLE_RATE=1.0  # Sample 100%

# Development  
SENTRY_LARAVEL_DSN=  # Disable Sentry
```

### Alternatives

If Sentry is too expensive:

1. **Bugsnag:** https://www.bugsnag.com
2. **Rollbar:** https://rollbar.com
3. **Flare (Laravel-specific):** https://flareapp.io
4. **Self-hosted:** Sentry can be self-hosted

### Monitoring Checklist

- [ ] Install Sentry package
- [ ] Configure DSN in .env
- [ ] Test with `php artisan sentry:test`
- [ ] Set up alerts
- [ ] Configure release tracking
- [ ] Add breadcrumbs to critical paths
- [ ] Filter sensitive data
- [ ] Monitor performance
- [ ] Set up team notifications

---

**Current Status:** Configuration ready, pending installation
**Priority:** P1 - High
**Estimated Setup Time:** 30 minutes

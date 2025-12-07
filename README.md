# ReadyEat3 - Food Pre-Order System

## 📖 Overview

ReadyEat3 is a modern food pre-order and pickup management system built with Laravel 11. The system allows customers to browse menus, place orders, upload payment proofs, and schedule pickups. Admins can manage orders, verify payments, track production, and manage customer pickups through a comprehensive dashboard.

## ✨ Key Features

### Customer Features
- 🍽️ Browse menu with category filtering
- 🛒 Shopping cart with real-time updates
- 📅 Schedule pickup dates
- 💳 QRIS payment integration
- 📸 Payment proof upload
- 📧 Email notifications (order confirmation & ready for pickup)
- ✅ Order tracking

### Admin Features
- 📊 Real-time dashboard with analytics
- 📈 Time-based filtering (daily/weekly/monthly/yearly)
- ✅ Order verification system
- 📦 Production planning & tracking
- 👥 Customer management
- 🚚 Pickup management
- 🍴 Menu/product management
- 📉 Sales reports & statistics

## 🛠️ Tech Stack

- **Framework:** Laravel 11.x
- **Frontend:** Blade Templates + Alpine.js + Tailwind CSS
- **Database:** MySQL
- **Charts:** Chart.js
- **Authentication:** Laravel Breeze
- **File Storage:** Laravel Storage (local/S3)
- **Email:** Laravel Mail (SMTP/Mailgun/SES)

## 📋 Requirements

- PHP >= 8.2
- Composer
- MySQL >= 8.0
- Node.js >= 18.x & NPM
- Web Server (Apache/Nginx)

## 🚀 Installation

### 1. Clone Repository
```bash
git clone https://github.com/yourusername/ReadyEat3.git
cd ReadyEat3
```

### 2. Install Dependencies
```bash
# PHP dependencies
composer install

# Node dependencies
npm install
```

### 3. Environment Setup
```bash
# Copy environment file
copy .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Configure Database
Edit `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=readyeat3
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Configure Email (Optional)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@readyeat.com"
MAIL_FROM_NAME="ReadyEat"
```

### 6. Run Migrations
```bash
# Run migrations
php artisan migrate

# Seed database (optional - adds demo data)
php artisan db:seed
```

### 7. Storage Setup
```bash
# Create storage link
php artisan storage:link
```

### 8. Build Assets
```bash
# Development
npm run dev

# Production
npm run build
```

### 9. Start Server
```bash
# Development server
php artisan serve

# Access at: http://localhost:8000
```

## 👤 Default Credentials

After seeding:

**Admin:**
- Email: admin@readyeat.com
- Password: password

**Customer:**
- Email: customer@readyeat.com
- Password: password

## 📚 Usage Guide

### For Customers

1. **Browse Menu:** Visit homepage and explore available dishes
2. **Add to Cart:** Click "Add to Cart" on desired items
3. **Checkout:** 
   - Select pickup date
   - Scan QRIS code
   - Upload payment proof
   - Add notes (optional)
4. **Track Order:** Check email for order confirmation
5. **Pickup:** Bring invoice code on scheduled date

### For Admins

1. **Login:** Navigate to `/admin/login`
2. **Dashboard:**
   - View statistics (revenue, orders, customers)
   - Filter by time period
   - See top products & customers
3. **Verification Tab:**
   - Review pending payments
   - Accept/reject orders
   - Add admin notes
4. **Production Tab:**
   - View production plan
   - Track quota utilization
5. **Pickup Tab:**
   - Monitor ready orders
   - Mark as completed
6. **Products Tab:**
   - Add/edit/delete menu items
   - Toggle availability
   - Set daily limits

## 🔧 Configuration

### Menu Management
```php
// Set daily quota for menu item
Menu::find(1)->update(['daily_limit' => 50]);

// Toggle availability
Menu::find(1)->update(['isAvailable' => false]);
```

### Order Statuses
- `payment_pending` - Waiting for payment verification
- `ready_for_pickup` - Payment approved, ready for pickup
- `picked_up` - Customer has picked up order
- `cancelled` - Order cancelled

## 🗄️ Database Management

### Backup Database
```bash
# Manual backup
php artisan db:backup

# Keep 14 days of backups
php artisan db:backup --keep=14

# Custom path
php artisan db:backup --path=my-backups
```

### Clean Dummy Data
```bash
# View statistics and confirm
php artisan db:clean-dummy

# Force delete without confirmation
php artisan db:clean-dummy --force

# Keep 50 recent orders
php artisan db:clean-dummy --force --keep-recent=50
```

### Schedule Automatic Backup
Edit `app/Console/Kernel.php`:
```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('db:backup')->daily()->at('02:00');
}
```

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test --filter CheckoutTest

# With coverage
php artisan test --coverage
```

## 📊 Performance

### Database Indexes
Automatically optimized with indexes on:
- `orders.pickup_date`
- `orders.status`
- `orders.customer_phone`
- `order_items.menu_id`
- `menus.isAvailable`
- Composite indexes for common queries

### Optimization Tips
```bash
# Cache config
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize
```

## 🔒 Security

- ✅ CSRF Protection
- ✅ SQL Injection Prevention (Eloquent ORM)
- ✅ XSS Protection (Blade escaping)
- ✅ File Upload Validation
- ✅ Rate Limiting
- ✅ Password Hashing (Bcrypt)
- ✅ Pessimistic Locking (Race Condition Prevention)

## 📁 Project Structure

```
ReadyEat3/
├── app/
│   ├── Console/Commands/      # Artisan commands (backup, cleanup)
│   ├── Http/Controllers/      # Application controllers
│   ├── Mail/                  # Email classes
│   ├── Models/                # Eloquent models
│   └── ...
├── database/
│   ├── migrations/            # Database migrations
│   └── seeders/               # Database seeders
├── resources/
│   ├── views/                 # Blade templates
│   │   ├── admin/            # Admin dashboard views
│   │   ├── auth/             # Authentication views
│   │   ├── checkout/         # Checkout flow
│   │   ├── emails/           # Email templates
│   │   └── menus/            # Menu listing
│   └── css/                   # Stylesheets
├── routes/
│   └── web.php               # Web routes
├── storage/
│   └── app/
│       ├── backups/          # Database backups
│       └── public/           # Public file storage
└── tests/
    └── Feature/              # Feature tests
```

## 🐛 Troubleshooting

### Common Issues

**Database connection error:**
```bash
# Check .env configuration
# Ensure MySQL is running
# Test connection: php artisan migrate:status
```

**Storage permission error:**
```bash
# Windows
icacls storage /grant Users:F /T
icacls bootstrap/cache /grant Users:F /T

# Linux/Mac
chmod -R 775 storage bootstrap/cache
```

**Email not sending:**
```bash
# Check .env MAIL_ configuration
# Test with: php artisan tinker
Mail::raw('Test', function($msg) {
    $msg->to('test@example.com')->subject('Test');
});
```

## 📝 API Endpoints (For Future Development)

Currently using web routes. For API development:

```php
// routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/menus', [MenuController::class, 'index']);
});
```

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 👨‍💻 Author

**Your Name**
- GitHub: [@yourusername](https://github.com/yourusername)
- Email: your.email@example.com

## 🙏 Acknowledgments

- Laravel Framework
- Tailwind CSS
- Alpine.js
- Chart.js
- All contributors

## 📞 Support

For support:
- 📧 Email: support@readyeat.com
- 📱 Phone: +62 812-3456-7890
- 💬 GitHub Issues: [Create an issue](https://github.com/yourusername/ReadyEat3/issues)

---

**Made with ❤️ using Laravel**

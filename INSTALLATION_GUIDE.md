# Complete Installation Guide - Lost & Found Laravel Application

This guide will walk you through setting up the complete Lost & Found application in your Laravel 11 project.

## 📋 Prerequisites

- PHP 8.2 or higher
- Composer
- MySQL or PostgreSQL database
- Laravel 11 installed
- Basic understanding of Laravel

## 🚀 Step-by-Step Installation

### Step 1: Copy Files to Your Laravel Project

```bash
# Navigate to your Laravel project root
cd /path/to/your-laravel-project

# Copy all Blade views
cp -r /path/to/downloaded/laravel/resources/views/* resources/views/

# Copy routes file (BACKUP YOUR EXISTING ROUTES FIRST!)
cp routes/web.php routes/web.backup.php  # Backup existing
cp /path/to/downloaded/laravel/routes/web.php routes/web.php
```

### Step 2: Create Database Tables

Create migration files:

```bash
php artisan make:migration create_lost_items_table
php artisan make:migration create_found_items_table
php artisan make:migration create_matches_table
php artisan make:migration create_returns_table
```

#### Lost Items Migration
Edit `database/migrations/XXXX_XX_XX_create_lost_items_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lost_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('category');
            $table->string('location');
            $table->date('lost_date');
            $table->string('contact_info');
            $table->string('image_url')->nullable();
            $table->enum('status', ['active', 'found', 'returned'])->default('active');
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('category');
            $table->index('status');
            $table->index('lost_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lost_items');
    }
};
```

#### Found Items Migration
Edit `database/migrations/XXXX_XX_XX_create_found_items_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('found_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('category');
            $table->string('location');
            $table->date('found_date');
            $table->string('contact_info');
            $table->string('image_url')->nullable();
            $table->enum('status', ['active', 'claimed', 'returned'])->default('active');
            $table->timestamps();
            
            $table->index('category');
            $table->index('status');
            $table->index('found_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('found_items');
    }
};
```

#### Matches Migration
Edit `database/migrations/XXXX_XX_XX_create_matches_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lost_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('found_item_id')->constrained()->onDelete('cascade');
            $table->integer('similarity_score')->default(0);
            $table->enum('status', ['pending', 'contacted', 'confirmed', 'rejected'])->default('pending');
            $table->timestamps();
            
            $table->index('similarity_score');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
```

#### Returns Migration
Edit `database/migrations/XXXX_XX_XX_create_returns_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->morphs('item'); // Creates item_type and item_id columns
            $table->date('return_date');
            $table->string('return_location');
            $table->string('return_method');
            $table->string('contact_info');
            $table->text('notes')->nullable();
            $table->string('proof_image')->nullable();
            $table->timestamps();
            
            $table->index('return_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('returns');
    }
};
```

Run migrations:

```bash
php artisan migrate
```

### Step 3: Create Models

```bash
php artisan make:model LostItem
php artisan make:model FoundItem
php artisan make:model Match
php artisan make:model ItemReturn
```

Copy the model code from `/laravel/app/Models/ExampleModels.php` into each respective model file.

### Step 4: Create Controllers

```bash
php artisan make:controller DashboardController
php artisan make:controller LostItemController --resource
php artisan make:controller FoundItemController --resource
php artisan make:controller MatchController
php artisan make:controller ReturnController
php artisan make:controller SearchController
```

Copy the controller code from `/laravel/app/Http/Controllers/ExampleControllers.php` into each respective controller file.

### Step 5: Set Up Authentication

If you haven't already set up authentication, use Laravel Breeze:

```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
php artisan migrate
npm install && npm run dev
```

### Step 6: Configure File Storage

Make sure the `public` disk is linked:

```bash
php artisan storage:link
```

Update `.env` file:

```env
FILESYSTEM_DISK=public
```

Update `config/filesystems.php` to ensure the public disk is properly configured:

```php
'public' => [
    'driver' => 'local',
    'root' => storage_path('app/public'),
    'url' => env('APP_URL').'/storage',
    'visibility' => 'public',
],
```

### Step 7: Update Routes

The routes file at `/laravel/routes/web.php` contains all the necessary routes. Make sure to:

1. Keep your authentication routes (from Breeze/UI)
2. Add the Lost & Found routes from the provided file
3. Ensure the `auth` middleware is applied

### Step 8: Seed Sample Data (Optional)

Create a seeder for testing:

```bash
php artisan make:seeder LostAndFoundSeeder
```

Edit `database/seeders/LostAndFoundSeeder.php`:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\LostItem;
use App\Models\FoundItem;
use App\Models\ItemReturn;

class LostAndFoundSeeder extends Seeder
{
    public function run(): void
    {
        // Create test user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create lost items
        LostItem::create([
            'user_id' => $user->id,
            'title' => 'Black Leather Wallet',
            'description' => 'Lost my black leather wallet with ID cards and credit cards inside.',
            'category' => 'wallet',
            'location' => 'Central Park, NY',
            'lost_date' => now()->subDays(5),
            'contact_info' => 'test@example.com',
            'image_url' => 'https://images.unsplash.com/photo-1627123339143-1c23c3a77c8b?w=400',
            'status' => 'active',
        ]);

        LostItem::create([
            'user_id' => $user->id,
            'title' => 'iPhone 14 Pro',
            'description' => 'Lost my iPhone 14 Pro in black color at the coffee shop.',
            'category' => 'electronics',
            'location' => 'Main Street Coffee, NY',
            'lost_date' => now()->subDays(3),
            'contact_info' => 'test@example.com',
            'image_url' => 'https://images.unsplash.com/photo-1592286927505-c0d0eb93f371?w=400',
            'status' => 'active',
        ]);

        // Create found items
        FoundItem::create([
            'user_id' => $user->id,
            'title' => 'Blue Backpack',
            'description' => 'Found a blue backpack with school materials and laptop inside.',
            'category' => 'bag',
            'location' => 'City Library, NY',
            'found_date' => now()->subDays(2),
            'contact_info' => 'test@example.com',
            'image_url' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=400',
            'status' => 'active',
        ]);

        // Create returned items
        $returnedItem = LostItem::create([
            'user_id' => $user->id,
            'title' => 'House Keys',
            'description' => 'Lost my house keys with blue keychain.',
            'category' => 'keys',
            'location' => 'Metro Station, NY',
            'lost_date' => now()->subDays(10),
            'contact_info' => 'test@example.com',
            'status' => 'returned',
        ]);

        ItemReturn::create([
            'user_id' => $user->id,
            'item_type' => LostItem::class,
            'item_id' => $returnedItem->id,
            'return_date' => now()->subDays(2),
            'return_location' => 'Metro Station, NY',
            'return_method' => 'In-person meetup',
            'contact_info' => 'test@example.com',
            'notes' => 'Met at the station entrance. Owner was very grateful!',
        ]);
    }
}
```

Run the seeder:

```bash
php artisan db:seed --class=LostAndFoundSeeder
```

### Step 9: Update User Model

Add relationships to your `app/Models/User.php`:

```php
public function lostItems()
{
    return $this->hasMany(LostItem::class);
}

public function foundItems()
{
    return $this->hasMany(FoundItem::class);
}

public function returns()
{
    return $this->hasMany(ItemReturn::class);
}
```

### Step 10: Test the Application

```bash
# Start the development server
php artisan serve

# Visit in your browser
http://localhost:8000

# Login with:
# Email: test@example.com
# Password: password
```

## 🎨 Customization

### Change Colors

Edit `resources/views/layouts/app.blade.php` and update the CSS variables:

```css
:root {
    --primary-color: #3b82f6;     /* Change to your brand color */
    --success-color: #10b981;
    --danger-color: #ef4444;
    --warning-color: #f59e0b;
}
```

### Add More Categories

Update the category dropdowns in:
- `resources/views/lost-items/create.blade.php`
- `resources/views/lost-items/edit.blade.php`
- `resources/views/found-items/create.blade.php`
- `resources/views/found-items/edit.blade.php`

### Configure File Upload Limits

Edit `php.ini`:

```ini
upload_max_filesize = 10M
post_max_size = 10M
```

Or in `.htaccess` (if using Apache):

```apache
php_value upload_max_filesize 10M
php_value post_max_size 10M
```

## 🔒 Security Checklist

- [ ] CSRF tokens are included in all forms (✅ Already included)
- [ ] User authorization is checked before editing/deleting items
- [ ] File uploads are validated (type, size)
- [ ] Database queries use prepared statements (✅ Eloquent does this)
- [ ] Sensitive data is not exposed in views
- [ ] Authentication is required for all protected routes (✅ Already configured)

## 📊 Optional: Add AI Matching

To implement AI-powered matching between lost and found items:

1. Install a text similarity package:

```bash
composer require phpml/phpml
```

2. Create a job for matching:

```bash
php artisan make:job FindMatches
```

3. Implement similarity algorithm in the job
4. Trigger job when items are created

## 🐛 Troubleshooting

### Images not displaying
```bash
php artisan storage:link
```

### Routes not working
```bash
php artisan route:clear
php artisan cache:clear
```

### Views not updating
```bash
php artisan view:clear
```

### Migration errors
```bash
php artisan migrate:fresh  # WARNING: This deletes all data!
```

## 📚 Additional Resources

- [Laravel Documentation](https://laravel.com/docs/11.x)
- [Bootstrap 5 Documentation](https://getbootstrap.com/docs/5.3/)
- [Bootstrap Icons](https://icons.getbootstrap.com/)

## ✅ You're Ready!

Your Lost & Found application is now fully set up and ready to use! 

### Quick Links After Setup:
- Dashboard: `/dashboard`
- Report Lost Item: `/lost-items/create`
- Report Found Item: `/found-items/create`
- View Matches: `/matches`
- Success Stories: `/returns`
- Search: `/search`

## 🆘 Need Help?

If you encounter any issues:
1. Check the Laravel logs: `storage/logs/laravel.log`
2. Enable debug mode in `.env`: `APP_DEBUG=true`
3. Clear all caches: `php artisan optimize:clear`

---

**Happy Coding! 🚀**

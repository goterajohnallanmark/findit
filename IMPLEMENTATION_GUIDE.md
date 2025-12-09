# FindIt API Implementation Guide

## ✅ Completed Steps:
1. Laravel Sanctum installed
2. Sanctum migrations run
3. API controllers created in `app/Http/Controllers/Api/`
4. API resources created in `app/Http/Resources/`

## 📝 Next Steps - Copy & Paste These Implementations:

### 1. Update User Model

Add to `app/Models/User.php`:

```php
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; // Add HasApiTokens
    
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'profile_photo_path',
    ];
}
```

### 2. API Resources

**UserResource.php:**
```php
public function toArray(Request $request): array
{
    return [
        'id' => $this->id,
        'name' => $this->name,
        'email' => $this->email,
        'phone_number' => $this->phone_number,
        'profile_photo_url' => $this->profile_photo_url,
        'created_at' => $this->created_at,
    ];
}
```

**LostItemResource.php:**
```php
public function toArray(Request $request): array
{
    return [
        'id' => $this->id,
        'title' => $this->title,
        'description' => $this->description,
        'category' => $this->category,
        'location' => $this->location,
        'date_lost' => $this->date_lost,
        'image_url' => $this->image_url ? asset('storage/' . $this->image_url) : null,
        'status' => $this->status ?? 'active',
        'user' => new UserResource($this->whenLoaded('user')),
        'matches_count' => $this->whenLoaded('matches', fn() => $this->matches->count()),
        'created_at' => $this->created_at,
    ];
}
```

**FoundItemResource.php:**
```php
public function toArray(Request $request): array
{
    return [
        'id' => $this->id,
        'title' => $this->title,
        'description' => $this->description,
        'category' => $this->category,
        'location' => $this->location,
        'date_found' => $this->date_found,
        'image_url' => $this->image_url ? asset('storage/' . $this->image_url) : null,
        'status' => $this->status ?? 'active',
        'user' => new UserResource($this->whenLoaded('user')),
        'created_at' => $this->created_at,
    ];
}
```

**MatchResource.php:**
```php
public function toArray(Request $request): array
{
    return [
        'id' => $this->id,
        'lost_item' => new LostItemResource($this->whenLoaded('lostItem')),
        'found_item' => new FoundItemResource($this->whenLoaded('foundItem')),
        'similarity_score' => $this->similarity_score,
        'status' => $this->status,
        'lost_user_viewed_at' => $this->lost_user_viewed_at,
        'found_user_viewed_at' => $this->found_user_viewed_at,
        'created_at' => $this->created_at,
    ];
}
```

### 3. Create routes/api.php

```php
<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\FoundItemController;
use App\Http\Controllers\Api\LostItemController;
use App\Http\Controllers\Api\MatchController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ReturnController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/stats/my', [DashboardController::class, 'myStats']);

    // Lost Items
    Route::get('/lost-items/my', [LostItemController::class, 'myItems']);
    Route::post('/lost-items/{id}/mark-found', [LostItemController::class, 'markFound']);
    Route::apiResource('lost-items', LostItemController::class);

    // Found Items
    Route::get('/found-items/my', [FoundItemController::class, 'myItems']);
    Route::post('/found-items/{id}/mark-claimed', [FoundItemController::class, 'markClaimed']);
    Route::apiResource('found-items', FoundItemController::class);

    // Matches
    Route::get('/matches/unviewed', [MatchController::class, 'unviewedCount']);
    Route::post('/matches/{id}/notify', [MatchController::class, 'notify']);
    Route::post('/matches/{id}/view', [MatchController::class, 'markAsViewed']);
    Route::apiResource('matches', MatchController::class)->only(['index', 'show']);

    // Returns
    Route::apiResource('returns', ReturnController::class);

    // Profile
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile', [ProfileController::class, 'update']);
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto']);
    Route::delete('/profile/photo', [ProfileController::class, 'deletePhoto']);
    Route::put('/password', [ProfileController::class, 'updatePassword']);

    // Categories & Metadata
    Route::get('/categories', function () {
        return response()->json([
            'categories' => [
                'wallet', 'phone', 'keys', 'bag', 'documents', 
                'electronics', 'jewelry', 'clothing', 'pet', 'other'
            ]
        ]);
    });
});
```

### 4. Test API Endpoints

```bash
# Register
POST http://localhost/api/register
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}

# Login
POST http://localhost/api/login
{
  "email": "john@example.com",
  "password": "password123"
}

# Get Lost Items (with token)
GET http://localhost/api/lost-items
Authorization: Bearer YOUR_TOKEN_HERE

# Create Lost Item
POST http://localhost/api/lost-items
Authorization: Bearer YOUR_TOKEN_HERE
Content-Type: multipart/form-data
{
  "title": "Black Wallet",
  "description": "Leather wallet with cards",
  "category": "wallet",
  "location": "Central Park",
  "date_lost": "2025-11-29",
  "image": <file>
}
```

### 5. Flutter Integration Example

```dart
// api_service.dart
class ApiService {
  static const String baseUrl = 'http://your-domain.com/api';
  String? _token;

  Future<Map<String, dynamic>> login(String email, String password) async {
    final response = await http.post(
      Uri.parse('$baseUrl/login'),
      headers: {'Content-Type': 'application/json'},
      body: json.encode({'email': email, 'password': password}),
    );
    
    final data = json.decode(response.body);
    _token = data['token'];
    return data;
  }

  Future<List> getLostItems() async {
    final response = await http.get(
      Uri.parse('$baseUrl/lost-items'),
      headers: {
        'Authorization': 'Bearer $_token',
        'Accept': 'application/json',
      },
    );
    
    return json.decode(response.body)['data'];
  }
}
```

## 🔧 Full Controller Implementations

Due to file size, I've created separate implementation files:
- See `API_CONTROLLERS_FULL.md` for complete controller code
- All controllers include validation, authorization, and error handling
- All endpoints return JSON with proper HTTP status codes

## 📱 Mobile App Features Supported:
✅ User registration & login
✅ Profile management with photos
✅ Post lost/found items with images
✅ AI-powered matching
✅ Real-time match notifications
✅ Unviewed matches badge
✅ Return tracking
✅ Search & filters
✅ Pagination


# Complete API Resource Implementations

Copy these implementations into their respective resource files in `app/Http/Resources/`

## UserResource.php

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'profile_photo_url' => $this->profile_photo_path 
                ? asset('storage/' . $this->profile_photo_path)
                : null,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
```

## LostItemResource.php

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LostItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->category,
            'location' => $this->location,
            'date_lost' => $this->date_lost,
            'status' => $this->status ?? 'active',
            'image_url' => $this->image_url 
                ? asset('storage/' . $this->image_url)
                : null,
            'user' => new UserResource($this->whenLoaded('user')),
            'matches_count' => $this->whenCounted('matches'),
            'matches' => MatchResource::collection($this->whenLoaded('matches')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
```

## FoundItemResource.php

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FoundItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->category,
            'location' => $this->location,
            'date_found' => $this->date_found,
            'status' => $this->status ?? 'active',
            'image_url' => $this->image_url 
                ? asset('storage/' . $this->image_url)
                : null,
            'user' => new UserResource($this->whenLoaded('user')),
            'matches_count' => $this->whenCounted('matches'),
            'matches' => MatchResource::collection($this->whenLoaded('matches')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
```

## MatchResource.php

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MatchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $userId = auth()->id();
        
        return [
            'id' => $this->id,
            'similarity_score' => $this->similarity_score,
            'status' => $this->status ?? 'pending',
            'lost_item' => new LostItemResource($this->whenLoaded('lostItem')),
            'found_item' => new FoundItemResource($this->whenLoaded('foundItem')),
            'is_viewed' => $this->isViewed($userId),
            'viewed_at' => $this->getViewedAt($userId),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    private function isViewed($userId): bool
    {
        if ($this->lostItem && $this->lostItem->user_id === $userId) {
            return $this->lost_user_viewed_at !== null;
        }
        if ($this->foundItem && $this->foundItem->user_id === $userId) {
            return $this->found_user_viewed_at !== null;
        }
        return false;
    }

    private function getViewedAt($userId): ?string
    {
        if ($this->lostItem && $this->lostItem->user_id === $userId) {
            return $this->lost_user_viewed_at?->format('Y-m-d H:i:s');
        }
        if ($this->foundItem && $this->foundItem->user_id === $userId) {
            return $this->found_user_viewed_at?->format('Y-m-d H:i:s');
        }
        return null;
    }
}
```

## ReturnRecordResource.php

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReturnRecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'match_id' => $this->match_id,
            'return_date' => $this->return_date,
            'notes' => $this->notes,
            'match' => new MatchResource($this->whenLoaded('match')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
```

---

# Flutter Integration Example

## Setup

```dart
// Add to pubspec.yaml
dependencies:
  http: ^1.1.0
  flutter_secure_storage: ^9.0.0
```

## API Service

```dart
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class ApiService {
  static const String baseUrl = 'http://localhost/api';
  static const storage = FlutterSecureStorage();

  // Authentication
  static Future<Map<String, dynamic>> register({
    required String name,
    required String email,
    required String password,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/register'),
      headers: {'Content-Type': 'application/json'},
      body: jsonEncode({
        'name': name,
        'email': email,
        'password': password,
        'password_confirmation': password,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      await storage.write(key: 'token', value: data['token']);
      return data;
    }
    throw Exception('Registration failed');
  }

  static Future<Map<String, dynamic>> login(String email, String password) async {
    final response = await http.post(
      Uri.parse('$baseUrl/login'),
      headers: {'Content-Type': 'application/json'},
      body: jsonEncode({'email': email, 'password': password}),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      await storage.write(key: 'token', value: data['token']);
      return data;
    }
    throw Exception('Login failed');
  }

  static Future<void> logout() async {
    final token = await storage.read(key: 'token');
    await http.post(
      Uri.parse('$baseUrl/logout'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      },
    );
    await storage.delete(key: 'token');
  }

  // Get authenticated user
  static Future<Map<String, dynamic>> getUser() async {
    final token = await storage.read(key: 'token');
    final response = await http.get(
      Uri.parse('$baseUrl/user'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      },
    );
    
    return jsonDecode(response.body)['data'];
  }

  // Lost Items
  static Future<List<dynamic>> getLostItems({
    String? category,
    String? location,
    String? search,
    int page = 1,
  }) async {
    final queryParams = {
      'page': page.toString(),
      if (category != null) 'category': category,
      if (location != null) 'location': location,
      if (search != null) 'q': search,
    };

    final uri = Uri.parse('$baseUrl/lost-items').replace(queryParameters: queryParams);
    final response = await http.get(uri, headers: {'Accept': 'application/json'});
    
    return jsonDecode(response.body)['data'];
  }

  static Future<Map<String, dynamic>> createLostItem({
    required String title,
    required String description,
    required String category,
    required String location,
    required String dateLost,
    String? imagePath,
  }) async {
    final token = await storage.read(key: 'token');
    var request = http.MultipartRequest('POST', Uri.parse('$baseUrl/lost-items'));
    
    request.headers.addAll({
      'Authorization': 'Bearer $token',
      'Accept': 'application/json',
    });

    request.fields['title'] = title;
    request.fields['description'] = description;
    request.fields['category'] = category;
    request.fields['location'] = location;
    request.fields['date_lost'] = dateLost;

    if (imagePath != null) {
      request.files.add(await http.MultipartFile.fromPath('image', imagePath));
    }

    final streamedResponse = await request.send();
    final response = await http.Response.fromStream(streamedResponse);
    
    return jsonDecode(response.body)['data'];
  }

  // Matches
  static Future<List<dynamic>> getMatches() async {
    final token = await storage.read(key: 'token');
    final response = await http.get(
      Uri.parse('$baseUrl/matches'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      },
    );
    
    return jsonDecode(response.body)['data'];
  }

  static Future<int> getUnviewedMatchesCount() async {
    final token = await storage.read(key: 'token');
    final response = await http.get(
      Uri.parse('$baseUrl/matches/unviewed-count'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      },
    );
    
    return jsonDecode(response.body)['unviewed_count'];
  }

  static Future<void> markMatchAsViewed(int matchId) async {
    final token = await storage.read(key: 'token');
    await http.post(
      Uri.parse('$baseUrl/matches/$matchId/mark-viewed'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      },
    );
  }

  // Dashboard
  static Future<Map<String, dynamic>> getDashboardStats() async {
    final token = await storage.read(key: 'token');
    final response = await http.get(
      Uri.parse('$baseUrl/dashboard'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      },
    );
    
    return jsonDecode(response.body);
  }
}
```

## Usage Example

```dart
// Login
final loginData = await ApiService.login('user@example.com', 'password');
print('Logged in as: ${loginData['user']['name']}');

// Get lost items
final lostItems = await ApiService.getLostItems(category: 'Electronics');

// Create lost item
await ApiService.createLostItem(
  title: 'Lost Laptop',
  description: 'MacBook Pro 16" Silver',
  category: 'Electronics',
  location: 'Building A, 3rd Floor',
  dateLost: '2025-01-15',
  imagePath: '/path/to/image.jpg',
);

// Get matches
final matches = await ApiService.getMatches();

// Get unviewed matches count for badge
final unviewedCount = await ApiService.getUnviewedMatchesCount();

// Dashboard stats
final stats = await ApiService.getDashboardStats();
print('Total lost items: ${stats['total_lost_items']}');
```


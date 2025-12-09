<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('returns JSON with token when login succeeds', function () {
    $password = 'secret1234';
    $user = User::factory()->create([
        'password' => Hash::make($password),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => $password,
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'message',
            'user' => ['id', 'name', 'email'],
            'token',
        ]);
});

it('returns JSON validation errors when credentials bad', function () {
    $response = $this->postJson('/api/login', [
        'email' => 'missing@example.com',
        'password' => 'wrong',
    ]);

    $response->assertStatus(422)
        ->assertJsonStructure(['message', 'errors' => ['email']]);
});

<?php

use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

afterEach(function () {
    RateLimiter::clear('password-email');
    RateLimiter::clear('search');
});

test('password reset requests are throttled after 5 attempts per minute', function () {
    for ($i = 1; $i <= 5; $i++) {
        $this->post(route('password.email'), ['email' => 'someone@example.com'])
            ->assertStatus(302);
    }

    $this->post(route('password.email'), ['email' => 'someone@example.com'])
        ->assertStatus(429);
});

test('search requests are throttled after 30 attempts per minute', function () {
    for ($i = 1; $i <= 30; $i++) {
        $this->get(route('search', ['q' => 'x']))->assertOk();
    }

    $this->get(route('search', ['q' => 'x']))->assertStatus(429);
});

test('withdraw requests are throttled after 5 attempts per minute', function () {
    $user = User::factory()->create(['balance' => 100000]);

    for ($i = 1; $i <= 5; $i++) {
        $this->actingAs($user)->post(route('withdraw'));
    }

    $this->actingAs($user)->post(route('withdraw'))->assertStatus(429);
});

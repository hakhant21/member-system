<?php

namespace DET\Members\Tests\Feature\Api;

use DET\Members\Models\Member;
use DET\Members\Tests\TestCase;
use Spatie\Permission\Models\Permission;

/**
 * 🟢 ADD THIS BLOCK
 *
 * @var TestCase $this
 */
beforeEach(function () {
    $this->artisan('migrate');
    Permission::create(['name' => 'member.view', 'guard_name' => 'member']);

    // ✅ CORRECT: Using Factory for Admin
    $this->admin = Member::factory()->create(['email' => 'admin@test.com']);
    $this->admin->givePermissionTo('member.view');
});

it('can search and filter members using factories', function () {
    // ✅ CORRECT: Using Factory to create dummy data instantly
    Member::factory()->create(['name' => 'John Doe', 'email' => 'john@test.com']);
    Member::factory()->create(['name' => 'Jane Smith']);

    $token = $this->admin->createToken('test-token')->plainTextToken;

    $this->withHeader('Authorization', 'Bearer '.$token)
        ->getJson('/api/v1/members?search=John')
        ->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.email', 'john@test.com');
});

it('denies access if permission is missing', function () {
    // 🟢 UPDATED: Use Factory here too instead of Member::create
    $guest = Member::factory()->create(['email' => 'guest@test.com']);

    $token = $guest->createToken('guest-token')->plainTextToken;

    $this->withHeader('Authorization', 'Bearer '.$token)
        ->getJson('/api/v1/members')
        ->assertStatus(403);
});

<?php

namespace Det\Members\Tests\Feature\Api;

use Det\Members\Models\Member;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Det\Members\Tests\TestCase;

/** 
 * 🟢 ADD THIS BLOCK
 * @var TestCase $this 
 */

beforeEach(function () {
    $this->artisan('migrate');
    Permission::create(['name' => 'member.create', 'guard_name' => 'member']);

    Role::create(['name' => 'member', 'guard_name' => 'member']);
    
    $this->admin = Member::factory()->create(['email' => 'admin@test.com']);
    $this->admin->givePermissionTo('member.create');
});

it('validates input when creating a member', function () {
    $token = $this->admin->createToken('test')->plainTextToken;

    // Send invalid data (bad email, short password)
    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
         ->postJson('/api/v1/members', [
             'email' => 'not-an-email', 
             'password' => 'short',
         ]);

    // Expect 422 Unprocessable Entity
    $response->assertStatus(422)
             ->assertJsonValidationErrors(['email', 'password']);
});

it('successfully creates a member via service', function () {
    $token = $this->admin->createToken('test')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
         ->postJson('/api/v1/members', [
             'email' => 'newuser@example.com',
             'password' => 'password123',
             'password_confirmation' => 'password123',
             'name' => 'New User',
             'profile' => [
                 'first_name' => 'New',
                 'last_name' => 'User',
                 'settings' => '{"theme":"dark"}'
             ]
         ]);

    $response->assertStatus(201)
             ->assertJsonPath('message', 'Member created successfully.')
             ->assertJsonPath('data.email', 'newuser@example.com');
             
    $this->assertDatabaseHas('members', ['email' => 'newuser@example.com']);
});
<?php

namespace Det\Members\Tests\Feature\Api;

use Det\Members\Models\Member;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Det\Members\Tests\TestCase;

/** @var \Det\Members\Tests\TestCase $this */

beforeEach(function () {
    $this->artisan('migrate');
    
    // Setup Permissions
    Permission::create(['name' => 'member.manage', 'guard_name' => 'member']);
    
    // Setup Roles
    Role::create(['name' => 'manager', 'guard_name' => 'member']);
    
    // Setup Admin
    $this->admin = Member::factory()->create();
    $this->admin->givePermissionTo('member.manage');
    
    // Setup Target Member
    $this->target = Member::factory()->create();
});

it('can assign a role to a member', function () {
    $token = $this->admin->createToken('admin')->plainTextToken;

    $this->withHeader('Authorization', 'Bearer ' . $token)
         ->postJson("/api/v1/members/{$this->target->id}/assign-role", [
             'role' => 'manager'
         ])
         ->assertStatus(200)
         ->assertJsonPath('message', "Role 'manager' assigned successfully.");

    expect($this->target->refresh()->hasRole('manager'))->toBeTrue();
});

it('can remove a role from a member', function () {
    // Give the target a role first
    $this->target->assignRole('manager');
    
    $token = $this->admin->createToken('admin')->plainTextToken;

    $this->withHeader('Authorization', 'Bearer ' . $token)
         ->postJson("/api/v1/members/{$this->target->id}/remove-role", [
             'role' => 'manager'
         ])
         ->assertStatus(200);

    expect($this->target->refresh()->hasRole('manager'))->toBeFalse();
});
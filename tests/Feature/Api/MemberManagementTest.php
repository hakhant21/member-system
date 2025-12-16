<?php

namespace DET\Members\Tests\Feature\Api;

use DET\Members\Models\Member;
use Spatie\Permission\Models\Permission;

/** @var \DET\Members\Tests\TestCase $this */
beforeEach(function () {
    $this->artisan('migrate');
    Permission::create(['name' => 'member.edit', 'guard_name' => 'member']);
    Permission::create(['name' => 'member.delete', 'guard_name' => 'member']);

    $this->admin = Member::factory()->create();
    $this->admin->givePermissionTo(['member.edit', 'member.delete']);

    // Target Member
    $this->target = Member::factory()->create(['name' => 'Old Name', 'is_active' => true]);
});

it('can update a member', function () {
    $token = $this->admin->createToken('admin')->plainTextToken;

    $this->withHeader('Authorization', 'Bearer '.$token)
        ->putJson("/api/v1/members/{$this->target->id}", ['name' => 'New Name'])
        ->assertStatus(200)
        ->assertJsonPath('data.name', 'New Name');
});

it('can deactivate a member', function () {
    $token = $this->admin->createToken('admin')->plainTextToken;

    $this->withHeader('Authorization', 'Bearer '.$token)
        ->postJson("/api/v1/members/{$this->target->id}/status", ['status' => false])
        ->assertStatus(200)
        ->assertJsonPath('message', 'Member deactivated successfully.');

    expect($this->target->refresh()->is_active)->toBeFalse();
});

it('can soft delete a member', function () {
    $token = $this->admin->createToken('admin')->plainTextToken;

    $this->withHeader('Authorization', 'Bearer '.$token)
        ->deleteJson("/api/v1/members/{$this->target->id}")
        ->assertStatus(200);

    expect($this->target->refresh()->trashed())->toBeTrue();
});

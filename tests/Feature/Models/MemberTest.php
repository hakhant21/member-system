<?php

namespace DET\Members\Tests\Feature\Models;

use DET\Members\Models\Member;
use DET\Members\Models\MemberProfile;
use DET\Members\Tests\TestCase;
use Spatie\Permission\Models\Role;

/**
 * 🟢 ADD THIS BLOCK
 *
 * @var TestCase $this
 */
beforeEach(function () {
    $this->artisan('migrate');
});

it('can create a member with soft deletes', function () {
    $member = Member::create([
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
        'name' => 'Test User',
    ]);

    expect($member->exists)->toBeTrue();
    expect($member->guard_name)->toBe('member');

    // Test Soft Delete
    $member->delete();
    expect($member->trashed())->toBeTrue();
});

it('can attach a profile with json settings', function () {
    $member = Member::create([
        'email' => 'profile@example.com',
        'password' => 'secret',
    ]);

    $profile = MemberProfile::create([
        'member_id' => $member->id,
        'first_name' => 'John',
        'settings' => ['theme' => 'dark'],
        'preferences' => ['notifications' => true],
    ]);

    $retrieved = MemberProfile::first();

    expect($retrieved->settings)->toBeArray();
    expect($retrieved->settings['theme'])->toBe('dark');
    expect($member->profile->first_name)->toBe('John');
});

it('can assign spatie roles to member', function () {
    $role = Role::create(['name' => 'member_admin', 'guard_name' => 'member']);

    $member = Member::create([
        'email' => 'admin@example.com',
        'password' => 'secret',
    ]);

    $member->assignRole('member_admin');

    expect($member->hasRole('member_admin'))->toBeTrue();
});

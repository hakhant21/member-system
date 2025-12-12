<?php

namespace Det\Members\Tests\Unit\Http\Resources;

use Det\Members\Http\Resources\MemberResource;
use Det\Members\Models\Member;

it('transforms member object to correct array', function () {
    // 1. Create a fake member in memory
    $member = new Member([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'is_active' => true,
        // 'phone' and 'last_login_at' will be null by default, which is fine
    ]);
    
    // 2. IMPORTANT: Manually assign the ID to bypass mass-assignment protection
    $member->id = 1;
    
    // 3. Manually set timestamps so toIso8601String() doesn't fail
    $member->setCreatedAt(now());
    $member->setUpdatedAt(now());

    // 4. Transform
    $resource = (new MemberResource($member))->response()->getData(true);

    // 5. Assert
    expect($resource['data'])
        ->toHaveKey('id', 1)
        ->toHaveKey('name', 'Test User')
        ->toHaveKey('email', 'test@example.com')
        ->toHaveKey('is_active', true)
        ->toHaveKey('created_at');
});
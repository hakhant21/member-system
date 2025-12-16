<?php

use DET\Members\Facades\MemberSystem;

test('it can access the package via the alias', function () {
    // This proves "MemberSystem" works like a global class
    // without needing to type "DET\Members\Facades\MemberSystem"
    expect(class_exists('MemberSystem'))->toBeTrue();

    // This proves it resolves to your service
    expect(MemberSystem::getFacadeRoot())->toBeInstanceOf(\DET\Members\Services\MemberService::class);
});

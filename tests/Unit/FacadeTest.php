<?php

namespace Det\Members\Tests\Unit;

use Det\Members\Facades\MemberSystem;
use Det\Members\Services\Contracts\MemberServiceInterface;
use Det\Members\Tests\TestCase;
use Mockery;

/** @var \Det\Members\Tests\TestCase $this */

it('resolves the facade to the service', function () {
    // Mock the underlying service
    $mock = Mockery::mock(MemberServiceInterface::class);
    $mock->shouldReceive('createMember')->once()->andReturn('mocked_member');
    
    // Swap the instance in the container
    $this->app->instance(MemberServiceInterface::class, $mock);

    // Call via Facade
    $result = MemberSystem::createMember([]);

    expect($result)->toBe('mocked_member');
});
<?php

namespace DET\Members\Tests\Unit;

use DET\Members\Facades\MemberSystem;
use DET\Members\Services\Contracts\MemberServiceInterface;
use Mockery;

/** @var \DET\Members\Tests\TestCase $this */
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

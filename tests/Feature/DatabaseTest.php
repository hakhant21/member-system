<?php

use Illuminate\Support\Facades\Schema;

it('creates the members table', function () {
    expect(Schema::hasTable('members'))->toBeTrue();
});

it('creates the member_profiles table', function () {
    expect(Schema::hasTable('member_profiles'))->toBeTrue();
});

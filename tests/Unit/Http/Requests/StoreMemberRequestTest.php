<?php

namespace Det\Members\Tests\Unit\Http\Requests;

use Det\Members\Http\Requests\StoreMemberRequest;
use Det\Members\Tests\TestCase;
use Illuminate\Support\Facades\Validator;

/** @var \Det\Members\Tests\TestCase $this */

it('validates that email is required', function () {
    $request = new StoreMemberRequest();
    
    $rules = $request->rules();
    
    $validator = Validator::make(['email' => ''], $rules);
    
    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->first('email'))->toBe('The email field is required.');
});

it('validates that password must be confirmed', function () {
    $request = new StoreMemberRequest();
    $rules = $request->rules();
    
    $data = [
        'email' => 'valid@email.com',
        'password' => 'password123',
        'password_confirmation' => 'wrong', // Mismatch
    ];
    
    $validator = Validator::make($data, $rules);
    
    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->first('password'))->toBe('The password field confirmation does not match.');
});
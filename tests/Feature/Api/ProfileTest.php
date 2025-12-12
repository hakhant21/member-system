<?php

namespace Det\Members\Tests\Feature\Api;

use Det\Members\Models\Member;
use Det\Members\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/** @var \Det\Members\Tests\TestCase $this */

beforeEach(function () {
    $this->artisan('migrate');
    $this->user = Member::factory()->create();
});

it('can update profile details', function () {
    $token = $this->user->createToken('test')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->putJson('/api/v1/members/profile', [
            'first_name' => 'Updated',
            'last_name' => 'Name',
            'social' => ['twitter' => 'https://twitter.com/test'] // Should be validated
        ]);

    $response->assertStatus(200)
             ->assertJsonPath('data.profile.first_name', 'Updated');
});

it('can upload an avatar', function () {
    Storage::fake('public');
    $file = UploadedFile::fake()->create('avatar.jpg', 100, 'image/jpeg');
    $token = $this->user->createToken('test')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->postJson('/api/v1/members/profile/avatar', [
            'avatar' => $file
        ]);

    $response->assertStatus(200);
    
    // Ensure file was stored
    $this->assertDatabaseHas('member_profiles', [
        'member_id' => $this->user->id,
    ]);
});
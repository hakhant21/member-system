<?php

namespace Det\Members\Database\Factories;

use Det\Members\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MemberFactory extends Factory
{
    protected $model = Member::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => 'password', 
            'phone' => $this->faker->phoneNumber(),
            'is_active' => true,
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }

    // State for Inactive members
    public function inactive()
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    // State for Unverified members
    public function unverified()
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
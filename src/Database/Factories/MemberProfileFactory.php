<?php

namespace DET\Members\Database\Factories;

use DET\Members\Models\Member;
use DET\Members\Models\MemberProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

class MemberProfileFactory extends Factory
{
    protected $model = MemberProfile::class;

    public function definition()
    {
        return [
            'member_id' => Member::factory(), // Automatically create parent member
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'gender' => $this->faker->randomElement(['male', 'female', 'other']),
            'date_of_birth' => $this->faker->date(),
            'city' => $this->faker->city(),
            'country' => $this->faker->country(),
            'settings' => ['theme' => 'light', 'notifications' => true],
            'preferences' => ['newsletter' => false],
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('password'), // Mot de passe par défaut
            'role' => $this->faker->randomElement(['admin', 'farmer', 'client']),
            'vip_status' => $this->faker->boolean,
        ];
    }
}

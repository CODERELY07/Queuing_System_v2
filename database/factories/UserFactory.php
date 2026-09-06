<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            // 'client' isn't a valid user_type — the column only allows
            // admin/staff (there's no self-registered account type in this
            // app; see routes/auth.php).
            'user_type' => fake()->randomElement(['admin', 'staff']),
            'email_verified_at' => now(),
            // service_id is required (not nullable), so a service must
            // exist first — created on demand rather than assuming a
            // seeded row is already there, which a fresh test database
            // (migrated but not seeded) won't have. Service has no
            // HasFactory of its own, so this creates one directly.
            'service_id' => (Service::inRandomOrder()->first() ?? Service::create([
                'name' => 'Test Service ' . Str::random(8),
                'prefix' => 'T',
            ]))->id,
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
    public function staff(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'staff',
        ]);
    }
}

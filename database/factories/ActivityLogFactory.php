<?php

namespace Database\Factories;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ActivityLog>
 */
class ActivityLogFactory extends Factory
{
    protected $model = ActivityLog::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'event_type' => fake()->randomElement(['login', 'logout', 'quiz_attempt', 'attendance', 'other']),
            'description' => fake()->sentence(),
            'ip_address' => fake()->ipv4(),
            'subject_type' => null,
            'subject_id' => null,
            'created_at' => fake()->dateTimeBetween('-30 days'),
        ];
    }
}

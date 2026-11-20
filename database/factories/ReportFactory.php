<?php

namespace Database\Factories;

use App\Models\Content;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Report>
 */
class ReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'reporter_name' => null,
            'reporter_email' => null,
            'reporter_ip' => '127.0.0.1',
            'reportable_type' => Content::class,
            'reportable_id' => Content::factory(),
            'type' => 'spam',
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'additional_info' => null,
            'status' => 'pending',
            'priority' => 'medium',
            'assigned_to' => null,
            'admin_notes' => null,
            'resolved_at' => null,
        ];
    }
}

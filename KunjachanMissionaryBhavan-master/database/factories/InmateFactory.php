<?php

namespace Database\Factories;

use App\Models\Inmate;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inmate>
 */
class InmateFactory extends Factory
{
    protected $model = Inmate::class;

    public function definition(): array
    {
        $gender = fake()->randomElement(['Male','Female']);

        return [
            'admission_number' => fake()->unique()->numerify('##########'),
            'first_name' => fake()->firstName($gender === 'Male' ? 'male' : 'female'),
            'last_name' => fake()->lastName(),
            'date_of_birth' => fake()->dateTimeBetween('-80 years','-18 years'),
            'gender' => $gender,
            'admission_date' => fake()->dateTimeBetween('-5 years','now'),
            'institution_id' => Institution::query()->inRandomOrder()->value('id'),
            'type' => fake()->randomElement(['child','elderly','mental_health','rehabilitation']),
            'notes' => fake()->optional()->paragraph(),
            'case_notes' => fake()->optional()->paragraph(),
            'health_info' => [
                'allergies' => fake()->words(2, true),
                'conditions' => fake()->words(3, true),
            ],
            'created_by' => User::query()->inRandomOrder()->value('id'),
            'updated_by' => User::query()->inRandomOrder()->value('id'),
        ];
    }
}

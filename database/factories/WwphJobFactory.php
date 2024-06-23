<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class WwphJobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $salary = $this->faker->numberBetween(100, 1000000);
        return [
            'title' => $this->faker->jobTitle(),
            'company_id' => $this->faker->numberBetween(1, 10),
            'work_type' => $this->faker->numberBetween(1,3),
            'job_type' => $this->faker->numberBetween(1,3),
            'salary' => $salary,
            'job_role' => $this->faker->jobTitle(),
            'salary_narration' => $salary .' per month',
            'education' => "Bachelor of Science",
            'location' => $this->faker->streetAddress().' '.$this->faker->country(),
            'description' => $this->faker->paragraphs(3, true),
            'requirements' => $this->faker->paragraphs(1, true),
            'experience' => $this->faker->numberBetween(0, 5),
            'job_cover' => $this->faker->imageUrl(),
            'application_note' => $this->faker->paragraphs(1, true),
            'application_link' => $this->faker->url(),
            'closing_date' => date("2024-10-10"),
            'benefits' => "HMO, FREE FOOD, FREE MONEY",
        ];
    }
}

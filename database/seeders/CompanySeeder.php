<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $users = User::pluck('id')->toArray(); // Fetch all user IDs

        for ($i = 0; $i < 3; $i++) {
            Company::create([
                'user_id' => $faker->randomElement($users), // Assign to a random user
                'company_name' => $faker->company,
                'address' => $faker->streetAddress,
                'tax_number' => strtoupper($faker->bothify('???###')),
                'email' => $faker->unique()->companyEmail,
                'street_number' => $faker->buildingNumber,
                'city' => $faker->city,
                'country' => $faker->country,
                'company_number' => strtoupper($faker->bothify('C-#######')),
                'contact_person' => $faker->name,
            ]);
        }
    }
}

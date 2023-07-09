<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Company;
use App\Models\Employee;
use App\Models\Responsibility;
use App\Models\Role;
use App\Models\Team;
use Illuminate\Database\Seeder;
use Database\Factories\CompanyFactory;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Company::factory(10)->create();
        // Team::factory(5)->create();
        // Role::factory(50)->create();
        // Responsibility::factory(200)->create();
        // Employee::factory(1000)->create();
    }
}

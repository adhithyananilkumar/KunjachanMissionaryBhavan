<?php

namespace Database\Seeders;

use App\Models\Inmate;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure at least one institution exists
        if (Institution::count() === 0) {
            Institution::factory()->count(1)->create([
                'name' => 'Kunjachan Missionary Bhavan',
            ]);
        }

        // Create a couple of staff/admin users if not present
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
            ]
        );

        $staff = User::firstOrCreate(
            ['email' => 'staff@example.com'],
            [
                'name' => 'Staff User',
                'password' => bcrypt('password'),
            ]
        );

        // Optional: set roles if the User model has a role column
        if (\Schema::hasColumn('users','role')) {
            $admin->update(['role' => 'system_admin']);
            $staff->update(['role' => 'staff']);
        }

        // Seed inmates with relationships
        if (Inmate::count() < 30) {
            Inmate::factory()->count(30)->create();
        }
    }
}

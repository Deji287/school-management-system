<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;
    /**
     * Seed the application's database.
    */
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);
        // User::factory(10)->create();

       User::factory()->create([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => bcrypt('password'), // you can set a default password
            'role_id' => 2, // e.g., 2 = student (adjust based on your seeded roles)
            'is_active' => true,
        ]);
    }
}

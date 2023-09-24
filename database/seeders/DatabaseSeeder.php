<?php

namespace Database\Seeders;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::query()->updateOrCreate([
            'email' => 'admin@gmail.com',
        ], [
            'name' => "Admin",
            'password' => bcrypt('11112222')
        ]);

        if (!Todo::query()->count()) {
            Todo::factory(50)->create();
        }
    }
}

<?php

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
//         $this->call(UsersTableSeeder::class);
         $this->call(\Modules\Language\Database\Seeders\LanguageDatabaseSeeder::class);
         $this->call(\Modules\Setting\Database\Seeders\SettingDatabaseSeeder::class);
    }
}

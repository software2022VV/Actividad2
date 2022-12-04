<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name'=>'Administrador',
            'email'=>'admin@admin.com',
            'password'=>bcrypt('12345678')
        ])->assignRole('admin');

        User::create([
            'name'=>'Autor',
            'email'=>'autor@autor.com',
            'password'=>bcrypt('12345678')
        ])->assignRole('autor');

        User::create([
            'name'=>'Lector',
            'email'=>'lector@lector.com',
            'password'=>bcrypt('12345678')
        ])->assignRole('lector');
    }
}

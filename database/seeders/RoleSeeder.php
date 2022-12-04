<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $admin = Role::create(['name' => 'admin']);
        $autor = Role::create(['name' => 'autor']);
        $lector = Role::create(['name' => 'lector']);

        Permission::create(['name' => 'categories.index'])->syncRoles([$admin, $autor, $lector]);
        Permission::create(['name' => 'categories.show'])->syncRoles([$admin, $autor, $lector]);
        Permission::create(['name' => 'categories.store'])->syncRoles([$admin]);
        Permission::create(['name' => 'categories.update'])->syncRoles([$admin]);
        Permission::create(['name' => 'categories.delete'])->syncRoles([$admin]);

        Permission::create(['name' => 'post.index'])->syncRoles([$admin, $autor, $lector]);
        Permission::create(['name' => 'post.show'])->syncRoles([$admin, $autor, $lector]);
        Permission::create(['name' => 'post.store'])->syncRoles([$admin, $autor]);
        Permission::create(['name' => 'post.update'])->syncRoles([$autor]);
        Permission::create(['name' => 'post.delete'])->syncRoles([$admin, $autor]);
        Permission::create(['name' => 'post.publish'])->syncRoles([$admin]);

        Permission::create(['name' => 'users.index'])->syncRoles([ $admin]);
        Permission::create(['name' => 'users.store'])->syncRoles([ $admin]);
        Permission::create(['name' => 'users.show'])->syncRoles([ $admin]);
        Permission::create(['name' => 'users.info'])->syncRoles([ $autor, $lector]);
        Permission::create(['name' => 'users.update-auth'])->syncRoles([ $admin, $autor, $lector]);
        Permission::create(['name' => 'users.destroy'])->syncRoles([ $admin]);
    }
}

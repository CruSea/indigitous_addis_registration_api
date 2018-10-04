<?php

use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user_role1 = new \App\UserRole();
        $user_role1->name = "Super Admin";
        $user_role1->description = "Super Admin Administrator";
        $user_role1->save();

        $user_role2 = new \App\UserRole();
        $user_role2->name = "Admin";
        $user_role2->description = "Admins";
        $user_role2->save();

        $user_role3 = new \App\UserRole();
        $user_role3->name = "Editor";
        $user_role3->description = "Editor";
        $user_role3->save();
    }
}

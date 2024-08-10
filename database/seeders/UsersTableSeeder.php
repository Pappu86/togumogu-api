<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PermissionsTableSeeder::class);
        $this->call(RolesTableSeeder::class);

//        DB::table('users')->delete();

        $super_admin1 = User::create([
            'name' => 'ToguMogu',
            'email' => 'tgmgadmin@togumogu.com',
            'mobile' => null,
            'password' => 'ILoveBatteryLow',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $super_admin2 = User::create([
            'name' => 'Eliyas Hossain',
            'email' => 'eliyas@batterylowinteractive.com',
            'mobile' => '+8801827848374',
            'password' => 'ILoveBatteryLow',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $super_admin3 = User::create([
            'name' => 'Masum Billah',
            'email' => 'mbillah@batterylowinteractive.com',
            'mobile' => '+8801922483273',
            'password' => 'ILoveBatteryLow',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $super_admin1->assignRole('super-admin');
        $super_admin2->assignRole('super-admin');
        $super_admin3->assignRole('super-admin');
    }
}

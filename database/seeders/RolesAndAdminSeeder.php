<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // očisti permission cache
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // kreiraj role
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $admin = Role::firstOrCreate(['name' => 'admin']);

        /**
         * SUPER ADMIN
         */
        $milan = User::firstOrCreate(
            ['email' => 'milan.stankovic@radijator.rs'],
            [
                'name' => 'Milan Stankovic',
                'password' => bcrypt('28januar'),
            ]
        );

        $milan->syncRoles([$superAdmin]);

        /**
         * ADMIN USERS
         */
        $admins = [
            'mihajlo.ilic@radijator.rs',
            'bojan@radijator.rs',
            'bojantrajkovic@radijator.rs',
            'vladimir.knezevic@radijator.rs',
            'nenadmag@radijator.rs',
            'branko@radijator.rs',
            'dejan.vojinovic@radijator.rs',
        ];

        foreach ($admins as $email) {
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => ucfirst(explode('@', $email)[0]),
                    'password' => bcrypt('radijator123'),
                ]
            );

            $user->syncRoles([$admin]);
        }
    }
}
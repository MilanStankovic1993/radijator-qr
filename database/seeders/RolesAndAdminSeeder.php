<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        // clear permission cache
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // roles
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $admin = Role::firstOrCreate(['name' => 'admin']);

        /**
         * SUPER ADMIN
         */
        $milan = User::firstOrCreate(
            ['email' => 'milan.stankovic@radijator.rs'],
            [
                'name' => 'Milan Stankovic',
                'password' => Hash::make('28januar'),
            ]
        );

        $milan->syncRoles([$superAdmin]);

        /**
         * ADMIN USERS
         */
        $admins = [
            'm.janic@radijator.rs',
            'dragana.sindjelic@radijator.rs',
            'mihajlo.ilic@radijator.rs',
            'luka.janic@radijator.rs',
            'mateja.janic@radijator.rs',
            'bojan@radijator.rs',
            'radovan@radijator.rs',
            'bojantrajkovic@radijator.rs',
            'vladimir.knezevic@radijator.rs',
            'nenadmag@radijator.rs',
            'branko@radijator.rs',
            'dejan.vojinovic@radijator.rs',
            'dragana@radijator.rs',
            'milan.vucicevic@radijator.rs',
            'milena.stojnic@radijator.rs',
            'bilja@radijator.rs',
            'radica@radijator.rs',
        ];

        foreach ($admins as $email) {

            $namePart = explode('@', $email)[0];

            // pretvori milan.vucicevic -> Milan Vucicevic
            $nameParts = explode('.', $namePart);

            $formattedName = collect($nameParts)
                ->map(fn ($part) => ucfirst($part))
                ->implode(' ');

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $formattedName,
                    'password' => Hash::make('radijator123'),
                ]
            );

            $user->syncRoles([$admin]);
        }
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use function app;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Carbon\Carbon;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Réinitialiser les rôles et permissions en cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Créer les permissions
        // Permissions pour les projets
        Permission::create(['name' => 'view projects']);
        Permission::create(['name' => 'create projects']);
        Permission::create(['name' => 'edit projects']);
        Permission::create(['name' => 'delete projects']);
        Permission::create(['name' => 'manage projects']);

        // Permissions pour les travailleurs
        Permission::create(['name' => 'view workers']);
        Permission::create(['name' => 'create workers']);
        Permission::create(['name' => 'edit workers']);
        Permission::create(['name' => 'delete workers']);
        Permission::create(['name' => 'manage workers']);

        // Permissions pour les pointages
        Permission::create(['name' => 'view timesheets']);
        Permission::create(['name' => 'create timesheets']);
        Permission::create(['name' => 'edit timesheets']);
        Permission::create(['name' => 'delete timesheets']);
        Permission::create(['name' => 'manage timesheets']);

        // Permissions pour les utilisateurs
        Permission::create(['name' => 'view users']);
        Permission::create(['name' => 'create users']);
        Permission::create(['name' => 'edit users']);
        Permission::create(['name' => 'delete users']);
        Permission::create(['name' => 'manage users']);

        // Permissions pour les rapports
        Permission::create(['name' => 'view reports']);
        Permission::create(['name' => 'export reports']);
        Permission::create(['name' => 'manage reports']);

        // Permissions pour les feuilles vierges
        Permission::create(['name' => 'view blank timesheets']);
        Permission::create(['name' => 'export blank timesheets']);
        Permission::create(['name' => 'manage blank timesheets']);

        // Créer les rôles et leur attribuer des permissions

        // // 1. Rôle Driver (accès limité aux projets assignés et pointages)
        // $driverRole = Role::create(['name' => 'driver']);
        // $driverRole->givePermissionTo([
        //     'view projects',
        //     'view workers',
        //     'view timesheets',
        //     // 'create timesheets',
        //     'edit timesheets',
        //     'view blank timesheets',
        //     'export blank timesheets'
        // ]);

        // // 2. Rôle Admin (gestion complète des projets, travailleurs et pointages)
        // $adminRole = Role::create(['name' => 'admin']);
        // $adminRole->givePermissionTo([
        //     'manage projects',
        //     'manage workers',
        //     'manage timesheets',
        //     'view users',
        //     'create users',
        //     'edit users',
        //     'manage reports'
        // ]);

        // 3. Rôle Leader (accès aux tableau de boord)
        // $leaderRole = Role::create(['name' => 'leader']);
        // $leaderRole->givePermissionTo([
        //     'view projects',
        //     'view workers',
        //     'view timesheets',
        //     'view reports',
        //     'view blank timesheets',
        //     'export blank timesheets'
        // ]);

        // // 4. Rôle Super Admin (accès complet au système)
        $superAdminRole = Role::create(['name' => 'super-admin']);
        $superAdminRole->givePermissionTo(Permission::all());

        // Créer un utilisateur super-admin par défaut
        $superAdminUser = User::create([
            'first_name' => 'Lucas',
            'last_name' => 'M2i',
            'email' => 'lucas@informatique-m2i.fr',
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('#M2@Informatique&!')
        ]);
        $superAdminUser->assignRole('super-admin');

        // // Créer un utilisateur administrateur
        // // Mickael DUBOCQ
        // $admin1 = User::create([
        //     'first_name' => 'Mickael',
        //     'last_name' => 'DUBOCQ',
        //     'email' => 'mickael.dubocq@dubocqsa.com',
        //     'password' => Hash::make('#M2@Informatique&!'),
        // ]);
        // $admin1->assignRole('admin');

        // // Marion CÔME
        // $admin2 = User::create([
        //     'first_name' => 'Marion',
        //     'last_name' => 'CÔME',
        //     'email' => 'm.come@dubocqsa.com',
        //     'password' => Hash::make('#M2@Informatique&!'),
        // ]);
        // $admin2->assignRole('admin');

        // // Philippe DUBOCQ
        // $admin3 = User::create([
        //     'first_name' => 'Philippe',
        //     'last_name' => 'DUBOCQ',
        //     'email' => 'ph.dubocq@dubocqsa.com',
        //     'password' => Hash::make('#M2@Informatique&!'),
        // ]);
        // $admin3->assignRole('admin');

        // // Anais DALLA RIVA
        // $admin4 = User::create([
        //     'first_name' => 'Anais',
        //     'last_name' => 'DALLA RIVA',
        //     'email' => 'a.dallariva@dubocqsa.com',
        //     'password' => Hash::make('#M2@Informatique&!'),
        // ]);
        // $admin4->assignRole('admin');

        // // Créer un utilisateur conducteur
        // // Geoffroy GRASSIN D'ALPHONSE
        // $driver5 = User::create([
        //     'first_name' => 'Geoffroy',
        //     'last_name' => 'GRASSIN D\'ALPHONSE',
        //     'email' => 'g.grassin@dubocqsa.com',
        //     'password' => Hash::make('#M2@Informatique&!'),
        // ]);
        // $driver5->assignRole('driver');

        // // Gilles FERREIRA
        // $driver6 = User::create([
        //     'first_name' => 'Gilles',
        //     'last_name' => 'FERREIRA',
        //     'email' => 'g.ferreira@dubocqsa.com',
        //     'password' => Hash::make('#M2@Informatique&!'),
        // ]);
        // $driver6->assignRole('driver');

        // // Carlos RIBEIRO
        // $driver7 = User::create([
        //     'first_name' => 'Carlos',
        //     'last_name' => 'RIBEIRO',
        //     'email' => 'c.ribeiro@dubocqsa.com',
        //     'password' => Hash::make('#M2@Informatique&!'),
        // ]);
        // $driver7->assignRole('driver');

        // // Bruno PELLETIER
        // $driver8 = User::create([
        //     'first_name' => 'Bruno',
        //     'last_name' => 'PELLETIER',
        //     'email' => 'b.pelletier@dubocqsa.com',
        //     'password' => Hash::make('#M2@Informatique&!'),
        // ]);
        // $driver8->assignRole('driver');

        // // Antoine THEVRET
        // $driver9 = User::create([
        //     'first_name' => 'Antoine',
        //     'last_name' => 'THEVRET',
        //     'email' => 'a.thevret@dubocqsa.com',
        //     'password' => Hash::make('#M2@Informatique&!'),
        // ]);
        // $driver9->assignRole('driver');

        // // Stanislas WILHELEM
        // $driver10 = User::create([
        //     'first_name' => 'Stanislas',
        //     'last_name' => 'WILHELEM',
        //     'email' => 's.wilhelem@dubocqsa.com',
        //     'password' => Hash::make('#M2@Informatique&!'),
        // ]);
        // $driver10->assignRole('driver');

        // // Eric DEBRAY
        // $driver11 = User::create([
        //     'first_name' => 'Eric',
        //     'last_name' => 'DEBRAY',
        //     'email' => 'e.debray@dubocqsa.com',
        //     'password' => Hash::make('#M2@Informatique&!'),
        // ]);
        // $driver11->assignRole('driver');

        // // Cyril POCHON
        // $driver12 = User::create([
        //     'first_name' => 'Cyril',
        //     'last_name' => 'POCHON',
        //     'email' => 'c.pochon@dubocqsa.com',
        //     'password' => Hash::make('#M2@Informatique&!'),
        // ]);
        // $driver12->assignRole('driver');

        // // Alexandre BOULARD
        // $driver13 = User::create([
        //     'first_name' => 'Alexandre',
        //     'last_name' => 'BOULARD',
        //     'email' => 'a.boulard@dubocqsa.com',
        //     'password' => Hash::make('#M2@Informatique&!'),
        // ]);
        // $driver13->assignRole('driver');
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

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
        
        // 1. Rôle Driver (accès limité aux projets assignés et pointages)
        $driverRole = Role::create(['name' => 'driver']);
        $driverRole->givePermissionTo([
            'view projects',
            'view workers',
            'view timesheets',
            // 'create timesheets',
            'edit timesheets',
            'view blank timesheets',
            'export blank timesheets'
        ]);

        // 2. Rôle Admin (gestion complète des projets, travailleurs et pointages)
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo([
            'manage projects',
            'manage workers',
            'manage timesheets',
            'view users',
            'create users',
            'edit users',
            'manage reports'
        ]);

        // 3. Rôle Super Admin (accès complet au système)
        $superAdminRole = Role::create(['name' => 'super-admin']);
        $superAdminRole->givePermissionTo(Permission::all());

        // Créer un utilisateur super-admin par défaut
        $superAdminUser = User::create([
            'first_name' => 'Lucas',
            'last_name' => 'M2i',
            'email' => 'lucas@informatique-m2i.fr',
            'password' => Hash::make('#M2@Informatique&!'),
        ]);
        $superAdminUser->assignRole('super-admin');

        // Créer un utilisateur admin par défaut
        // $adminUser = User::create([
        //     'first_name' => 'Admin',
        //     'last_name' => 'User',
        //     'email' => 'admin@example.com',
        //     'password' => Hash::make('password'),
        // ]);
        // $adminUser->assignRole('admin');

        // // Créer un utilisateur driver par défaut
        // $driverUser = User::create([
        //     'first_name' => 'Driver',
        //     'last_name' => 'User',
        //     'email' => 'driver@example.com',
        //     'password' => Hash::make('password'),
        // ]);
        
        // $driverUser->assignRole('driver');
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Worker;
use App\Models\Interim;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // call the Role and Permission seeder
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        // assign the first user the role of admin
        // $user = User::get()->first();
        // $user->assignRole('driver');

        // call the other seeders
        // $this->call([
        //     SettingSeeder::class,
        //     ZoneSeeder::class,
            // InterimSeeder::class,
            // WorkerSeeder::class,
            // ProjectSeeder::class,
        // ]);

        // assign the first project to the first user
        // $user->projects()->attach(Project::first());

        // $projects = Project::all();
        // $workers = Worker::all();
        // $interims = Interim::all();

        // // assign all workers to all projects
        // foreach ($projects as $project) {
        //     foreach ($workers as $worker) {
        //         $worker->projects()->attach($project);
        //     }
        // }

        // // assign all interims to all projects
        // foreach ($projects as $project) {
        //     foreach ($interims as $interim) {
        //         $interim->projects()->attach($project);
        //     }
        // }

        // // create timesheets for all workers
        // foreach ($projects as $project) {
        //     foreach ($workers as $worker) {
        //         $worker->timesheets()->create([
        //             'date' => now()->subDays(random_int(1, 5)),
        //             'hours' => rand(5, 12),
        //             'category' => random_int(0, 1) ? 'day' : 'night',
        //             'project_id' => $project->id,
        //         ]);
        //     }
        // }

        // // create timesheets for all interims
        // foreach ($projects as $project) {
        //     foreach ($interims as $interim) {
        //         $interim->timesheets()->create([
        //             'date' => now()->subDays(random_int(1, 5)),
        //             'hours' => rand(5, 12),
        //             'category' => random_int(0, 1) ? 'day' : 'night',
        //             'project_id' => $project->id,
        //         ]);
        //     }
        // }
    }
}

<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Project;
use App\Models\Worker;
use App\Models\Interim;
use App\Models\Zone;
use App\Models\Setting;
use App\Models\User;
use App\CQRS\Commands\AssignEmployeeCommand;
use App\CQRS\Queries\GetTrackingDataQuery;
use App\CQRS\CommandBus;
use App\CQRS\QueryBus;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TrackingEmployeeAssignmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Créer les settings nécessaires
        Setting::create(['key' => 'rate_charged', 'value' => '70', 'start_date' => now()]);
        Setting::create(['key' => 'basket', 'value' => '11', 'start_date' => now()]);
        
        // Créer un utilisateur pour l'authentification
        $user = User::factory()->create();
        $this->actingAs($user);
    }

    /**
     * Test d'assignation d'un worker via CQRS
     */
    public function test_can_assign_worker_to_project_via_cqrs()
    {
        // Créer les données de test
        $project = Project::create([
            'code' => 1001,
            'name' => 'Test Project',
            'category' => 'mh',
            'status' => 'active'
        ]);

        $worker = Worker::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'contract_hours' => 35,
            'monthly_salary' => 3000,
            'category' => 'worker',
            'status' => 'active'
        ]);

        // Tester l'assignation via CQRS
        $commandBus = $this->app->make(CommandBus::class);
        
        $command = new AssignEmployeeCommand(
            $project->id,
            'worker',
            $worker->id
        );

        $result = $commandBus->dispatch($command);

        // Vérifier le résultat
        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
        $this->assertEquals('Employé assigné avec succès au projet', $result['message']);

        // Vérifier que l'assignation a bien eu lieu en base
        $this->assertDatabaseHas('projectables', [
            'project_id' => $project->id,
            'projectable_id' => $worker->id,
            'projectable_type' => Worker::class
        ]);

        // Vérifier que le worker est maintenant lié au projet
        $this->assertTrue($project->workers()->where('workers.id', $worker->id)->exists());
    }

    /**
     * Test d'assignation d'un intérim via CQRS
     */
    public function test_can_assign_interim_to_project_via_cqrs()
    {
        // Créer les données de test
        $project = Project::create([
            'code' => 1002,
            'name' => 'Test Project 2',
            'category' => 'go',
            'status' => 'active'
        ]);

        $interim = Interim::create([
            'agency' => 'Agence Test',
            'hourly_rate' => 25.50,
            'status' => 'active'
        ]);

        // Tester l'assignation via CQRS
        $commandBus = $this->app->make(CommandBus::class);
        
        $command = new AssignEmployeeCommand(
            $project->id,
            'interim',
            $interim->id
        );

        $result = $commandBus->dispatch($command);

        // Vérifier le résultat
        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
        $this->assertEquals('Employé assigné avec succès au projet', $result['message']);
        $this->assertStringContains('Agence Test (Intérim)', $result['data']['employee_name']);

        // Vérifier que l'assignation a bien eu lieu en base
        $this->assertDatabaseHas('projectables', [
            'project_id' => $project->id,
            'projectable_id' => $interim->id,
            'projectable_type' => Interim::class
        ]);

        // Vérifier que l'interim est maintenant lié au projet
        $this->assertTrue($project->interims()->where('interims.id', $interim->id)->exists());
    }

    /**
     * Test que les employés disponibles sont correctement filtrés
     */
    public function test_available_employees_are_filtered_correctly()
    {
        // Créer un projet
        $project = Project::create([
            'code' => 1003,
            'name' => 'Test Project 3',
            'category' => 'mh',
            'status' => 'active'
        ]);

        // Créer des workers
        $assignedWorker = Worker::create([
            'first_name' => 'Assigned',
            'last_name' => 'Worker',
            'contract_hours' => 35,
            'monthly_salary' => 3000,
            'category' => 'worker',
            'status' => 'active'
        ]);

        $availableWorker = Worker::create([
            'first_name' => 'Available',
            'last_name' => 'Worker',
            'contract_hours' => 35,
            'monthly_salary' => 3000,
            'category' => 'worker',
            'status' => 'active'
        ]);

        $inactiveWorker = Worker::create([
            'first_name' => 'Inactive',
            'last_name' => 'Worker',
            'contract_hours' => 35,
            'monthly_salary' => 3000,
            'category' => 'worker',
            'status' => 'inactive'
        ]);

        // Assigner un worker au projet
        $project->workers()->attach($assignedWorker->id);

        // Récupérer les données de tracking
        $queryBus = $this->app->make(QueryBus::class);
        
        $query = new GetTrackingDataQuery(
            $project->id,
            now()->month,
            now()->year,
            'day'
        );

        $data = $queryBus->dispatch($query);

        // Vérifier que seuls les workers disponibles sont retournés
        $this->assertIsArray($data);
        $this->assertArrayHasKey('availableWorkers', $data);
        $this->assertArrayHasKey('availableInterims', $data);

        $availableWorkerIds = collect($data['availableWorkers'])->pluck('id')->toArray();
        
        // Le worker disponible doit être dans la liste
        $this->assertContains($availableWorker->id, $availableWorkerIds);
        
        // Le worker assigné ne doit pas être dans la liste
        $this->assertNotContains($assignedWorker->id, $availableWorkerIds);
        
        // Le worker inactif ne doit pas être dans la liste
        $this->assertNotContains($inactiveWorker->id, $availableWorkerIds);
    }

    /**
     * Test de l'assignation via le contrôleur web
     */
    public function test_can_assign_employee_via_controller()
    {
        // Créer les données de test
        $project = Project::create([
            'code' => 1004,
            'name' => 'Test Project 4',
            'category' => 'mh',
            'status' => 'active'
        ]);

        $worker = Worker::create([
            'first_name' => 'Test',
            'last_name' => 'Worker',
            'contract_hours' => 35,
            'monthly_salary' => 3000,
            'category' => 'worker',
            'status' => 'active'
        ]);

        // Tester l'assignation via le contrôleur
        $response = $this->post(route('tracking.assignEmployee'), [
            'project_id' => $project->id,
            'employee_type' => 'worker',
            'employee_id' => $worker->id,
            'month' => now()->month,
            'year' => now()->year,
            'category' => 'day'
        ]);

        // Vérifier la redirection
        $response->assertRedirect(route('tracking.show', [
            'project_id' => $project->id,
            'month' => now()->month,
            'year' => now()->year,
            'category' => 'day'
        ]));

        // Vérifier le message de succès
        $response->assertSessionHas('success', 'Employé assigné avec succès.');

        // Vérifier que l'assignation a bien eu lieu
        $this->assertDatabaseHas('projectables', [
            'project_id' => $project->id,
            'projectable_id' => $worker->id,
            'projectable_type' => Worker::class
        ]);
    }

    /**
     * Test de validation des données d'assignation
     */
    public function test_assignment_validation()
    {
        $project = Project::create([
            'code' => 1005,
            'name' => 'Test Project 5',
            'category' => 'mh',
            'status' => 'active'
        ]);

        // Test avec des données invalides
        $response = $this->post(route('tracking.assignEmployee'), [
            'project_id' => $project->id,
            'employee_type' => 'invalid',
            'employee_id' => 'not_a_number',
            'month' => 13,
            'year' => 1800,
            'category' => 'invalid'
        ]);

        // Vérifier que la validation a échoué
        $response->assertSessionHasErrors([
            'employee_type',
            'employee_id',
            'month',
            'year',
            'category'
        ]);
    }
}
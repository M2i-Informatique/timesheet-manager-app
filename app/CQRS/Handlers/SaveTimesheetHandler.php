<?php

namespace App\CQRS\Handlers;

use App\CQRS\CommandHandlerInterface;
use App\CQRS\CommandInterface;
use App\CQRS\Commands\SaveTimesheetCommand;
use App\Models\Worker;
use App\Models\Interim;
use App\Models\TimeSheetable;
use App\Services\Cache\CacheService;
use Carbon\Carbon;
use InvalidArgumentException;

/**
 * Handler pour sauvegarder les données de pointage
 */
class SaveTimesheetHandler implements CommandHandlerInterface
{
    private CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function handle(CommandInterface $command): mixed
    {
        if (!$command instanceof SaveTimesheetCommand) {
            throw new InvalidArgumentException('Expected SaveTimesheetCommand');
        }

        $projectId = $command->getProjectId();
        $month = $command->getMonth();
        $year = $command->getYear();
        $category = $command->getCategory();
        $data = $command->getData();
        $otherCategory = ($category === 'day') ? 'night' : 'day';

        // Process timesheet data

        $results = [];

        foreach ($data as $entry) {
            $empId = $entry['id'];
            $modelType = $entry['model_type'];
            $className = ($modelType === 'worker') ? Worker::class : Interim::class;

            // Process entry for employee

            foreach ($entry['days'] as $dayNum => $value) {
                // Process day entry

                // Gestion des cellules vides (suppression)
                if ($value === null) {
                    $timesheet = TimeSheetable::where('project_id', $projectId)
                        ->where('timesheetable_id', $empId)
                        ->where('timesheetable_type', $className)
                        ->whereDay('date', $dayNum)
                        ->whereMonth('date', $month)
                        ->whereYear('date', $year)
                        ->where('category', $category)
                        ->first();

                    if ($timesheet) {
                        // Delete existing timesheet
                        $timesheet->delete();
                        $results[] = ['action' => 'deleted', 'day' => $dayNum, 'employee_id' => $empId];
                    } else {
                        // No timesheet found to delete
                    }
                    continue;
                }

                // Validation des heures
                $hours = floatval($value);
                if ($hours < 0) {
                    $hours = 0;
                }

                // Vérification contrainte day + night <= 12h
                $date = Carbon::create($year, $month, $dayNum)->format('Y-m-d');
                $otherTimesheet = TimeSheetable::where('project_id', $projectId)
                    ->where('timesheetable_id', $empId)
                    ->where('timesheetable_type', $className)
                    ->where('date', $date)
                    ->where('category', $otherCategory)
                    ->first();

                $otherHours = $otherTimesheet ? floatval($otherTimesheet->hours) : 0;
                if (($hours + $otherHours) > 12) {
                    throw new InvalidArgumentException(
                        "Somme jour+nuit >12h le " . date('d/m/Y', strtotime($date))
                    );
                }

                // Upsert du timesheet
                $timesheet = TimeSheetable::firstOrNew([
                    'project_id' => $projectId,
                    'timesheetable_id' => $empId,
                    'timesheetable_type' => $className,
                    'date' => $date,
                    'category' => $category
                ]);

                $timesheet->hours = $hours;
                $timesheet->save();

                // Timesheet saved successfully

                $results[] = [
                    'action' => $timesheet->wasRecentlyCreated ? 'created' : 'updated',
                    'day' => $dayNum,
                    'employee_id' => $empId,
                    'hours' => $hours
                ];
            }
        }

        // Invalider le cache pour ce projet et ce mois
        $this->cacheService->invalidateMonthCache($projectId, $month, $year);
        
        return [
            'success' => true,
            'message' => 'Données sauvegardées avec succès.',
            'results' => $results
        ];
    }
}
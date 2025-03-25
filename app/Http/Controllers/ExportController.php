<?php

namespace App\Http\Controllers;

use App\Exports\WorkerMonthlyExport;
use App\Exports\BlankMonthlyExport;
use App\Models\Project;
use App\Models\NonWorkingDay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    /**
     * Exporte une feuille de pointage vierge pour un projet donné
     */
    public function exportBlankMonthly(Request $request)
    {
        $validator = validator($request->all(), [
            'project_id' => 'required|exists:projects,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:1900|max:2099',
        ]);

        if ($validator->fails()) {
            return redirect()->route('home')->withErrors($validator);
        }

        // Récupérer les paramètres validés
        $validatedData = $validator->validated();
        $month = $validatedData['month'];
        $year = $validatedData['year'];
        $projectId = $validatedData['project_id'];

        // Récupérer le projet
        $project = Project::findOrFail($projectId);

        // Récupérer les jours non travaillés pour le mois et l'année
        $nonWorkingDays = NonWorkingDay::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();

        // Log des valeurs reçues
        Log::info("Exportation Feuille Vierge: Mois = {$month}, Année = {$year}, Projet ID = {$projectId}");

        // Formater le mois et l'année pour le nom de fichier
        $monthFormatted = str_pad($month, 2, '0', STR_PAD_LEFT);

        $filename = "Feuille_Pointage_{$project->name}_{$project->city}_{$monthFormatted}_{$year}.xlsx";

        return Excel::download(new BlankMonthlyExport($month, $year, $nonWorkingDays, $project), $filename);
    }
}

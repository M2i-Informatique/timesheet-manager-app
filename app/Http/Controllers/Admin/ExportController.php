<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
     * Affiche la page d'exportation
     */
    public function index()
    {
        $projects = Project::where('status', 'active')->orderBy('code')->get();
        return view('pages.admin.exports.index', compact('projects'));
    }

    /**
     * Exporte le récapitulatif mensuel des heures des workers
     */
    public function exportWorkersMonthly(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:1900|max:2099',
        ]);

        $month = $request->input('month');
        $year = $request->input('year');

        // Récupérer les jours non travaillés pour le mois et l'année
        $nonWorkingDays = NonWorkingDay::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();

        // Log des valeurs reçues
        Log::info("Exportation Mensuelle: Mois = {$month}, Année = {$year}");

        $filename = "Pointage_Travailleurs_{$month}_{$year}.xlsx";

        return Excel::download(new WorkerMonthlyExport($month, $year, $nonWorkingDays), $filename);
    }

    /**
     * Exporte une feuille de pointage vierge pour un projet donné (version admin)
     */
    public function exportBlankMonthly(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:1900|max:2099',
        ]);

        $month = $request->input('month');
        $year = $request->input('year');
        $projectId = $request->input('project_id');

        // Récupérer le projet
        $project = Project::findOrFail($projectId);

        // Récupérer les jours non travaillés pour le mois et l'année
        $nonWorkingDays = NonWorkingDay::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();

        // Log des valeurs reçues
        Log::info("Exportation Feuille Vierge (Admin): Mois = {$month}, Année = {$year}, Projet ID = {$projectId}");

        // Formater le mois et l'année pour le nom de fichier
        $monthFormatted = str_pad($month, 2, '0', STR_PAD_LEFT);

        $filename = "Feuille_Pointage_{$project->name}_{$project->city}_{$monthFormatted}_{$year}.xlsx";

        return Excel::download(new BlankMonthlyExport($month, $year, $nonWorkingDays, $project), $filename);
    }
}

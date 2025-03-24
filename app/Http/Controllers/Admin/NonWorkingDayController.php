<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NonWorkingDay;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NonWorkingDayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $nonWorkingDays = NonWorkingDay::orderBy('date', 'desc')->paginate(10);
        return view('pages.admin.non-working-days.index', compact('nonWorkingDays'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.admin.non-working-days.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date|unique:non_working_days',
            'type' => 'required|string|max:255',
        ]);

        NonWorkingDay::create($validated);

        return redirect()->route('admin.non-working-days.index')
            ->with('success', 'Jour non travaillé créé avec succès');
    }

    /**
     * Display the specified resource.
     */
    public function show(NonWorkingDay $nonWorkingDay)
    {
        return view('pages.admin.non-working-days.show', compact('nonWorkingDay'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NonWorkingDay $nonWorkingDay)
    {
        return view('pages.admin.non-working-days.edit', compact('nonWorkingDay'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, NonWorkingDay $nonWorkingDay)
    {
        $validated = $request->validate([
            'date' => 'required|date|unique:non_working_days,date,' . $nonWorkingDay->id,
            'type' => 'required|string|max:255',
        ]);

        $nonWorkingDay->update($validated);

        return redirect()->route('admin.non-working-days.index')
            ->with('success', 'Jour non travaillé mis à jour avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NonWorkingDay $nonWorkingDay)
    {
        $nonWorkingDay->delete();

        return redirect()->route('admin.non-working-days.index')
            ->with('success', 'Jour non travaillé supprimé avec succès');
    }

    /**
     * Générer automatiquement les jours fériés français pour une année
     */
    public function generateFrenchHolidays(Request $request)
    {
        $validated = $request->validate([
            'year' => 'required|integer|min:1900|max:2099',
        ]);

        $year = $validated['year'];

        // Jours fériés fixes
        $fixedHolidays = [
            ['date' => "{$year}-01-01", 'type' => "Jour de l'an"],
            ['date' => "{$year}-05-01", 'type' => "Fête du travail"],
            ['date' => "{$year}-05-08", 'type' => "Victoire 1945"],
            ['date' => "{$year}-07-14", 'type' => "Fête nationale"],
            ['date' => "{$year}-08-15", 'type' => "Assomption"],
            ['date' => "{$year}-11-01", 'type' => "Toussaint"],
            ['date' => "{$year}-11-11", 'type' => "Armistice 1918"],
            ['date' => "{$year}-12-25", 'type' => "Noël"],
        ];

        // Calcul de Pâques (algorithme de Butcher)
        $a = $year % 19;
        $b = floor($year / 100);
        $c = $year % 100;
        $d = floor($b / 4);
        $e = $b % 4;
        $f = floor(($b + 8) / 25);
        $g = floor(($b - $f + 1) / 3);
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = floor($c / 4);
        $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = floor(($a + 11 * $h + 22 * $l) / 451);
        $month = floor(($h + $l - 7 * $m + 114) / 31);
        $day = (($h + $l - 7 * $m + 114) % 31) + 1;

        $easter = Carbon::create($year, $month, $day);

        // Jours fériés mobiles (basés sur Pâques)
        $mobileHolidays = [
            ['date' => $easter->copy()->addDays(1)->format('Y-m-d'), 'type' => "Lundi de Pâques"],
            ['date' => $easter->copy()->addDays(39)->format('Y-m-d'), 'type' => "Ascension"],
            ['date' => $easter->copy()->addDays(50)->format('Y-m-d'), 'type' => "Pentecôte"],
        ];

        $allHolidays = array_merge($fixedHolidays, $mobileHolidays);

        // Création des jours fériés
        foreach ($allHolidays as $holiday) {
            NonWorkingDay::updateOrCreate(
                ['date' => $holiday['date']],
                ['type' => $holiday['type']]
            );
        }

        return redirect()->route('admin.non-working-days.index')
            ->with('success', "Les jours fériés pour l'année {$year} ont été générés avec succès");
    }
}

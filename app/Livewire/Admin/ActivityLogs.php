<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

class ActivityLogs extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public function render()
    {
        $activityLogs = Activity::with(['causer', 'subject'])
            ->where('subject_type', 'App\Models\TimeSheetable')
            ->latest()
            ->paginate(10);

        return view('livewire.admin.activity-logs', [
            'activityLogs' => $activityLogs
        ]);
    }
}

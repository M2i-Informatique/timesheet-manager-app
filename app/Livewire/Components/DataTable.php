<?php

namespace App\Livewire\Components;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;

class DataTable extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $sortField = 'id';
    public $sortDirection = 'asc';
    public $columns = [];
    public $searchFields = [];
    public $actions = [];
    public $title = '';
    public $description = '';
    public $hasSearch = true;
    protected $modelClass;

    // Définir explicitement le thème Tailwind pour la pagination
    protected $paginationTheme = 'tailwind';

    protected $queryString = ['search' => ['except' => ''], 'perPage', 'sortField', 'sortDirection'];

    public function __construct($modelClass = null)
    {
        if ($modelClass) {
            $this->modelClass = $modelClass;
        }
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $modelClass = $this->modelClass;

        if (!$modelClass) {
            return view('livewire.components.data-table', [
                'data' => collect([])
            ]);
        }

        $data = $modelClass::query()
            ->when($this->search && !empty($this->searchFields), function (Builder $query) {
                $query->where(function (Builder $subQuery) {
                    foreach ($this->searchFields as $field) {
                        $subQuery->orWhere($field, 'ilike', '%' . $this->search . '%');
                    }
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.components.data-table', [
            'data' => $data
        ]);
    }

    public function callAction($method, $id)
    {
        if (method_exists($this, $method)) {
            $this->$method($id);
        }
    }
}

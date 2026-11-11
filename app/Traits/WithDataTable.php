<?php

namespace App\Traits;

/**
 * Shared DataTable state and sorting helpers for Livewire table components.
 */
trait WithDataTable
{
    public $perPage = 10;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    protected function applySorting($query, array $allowedFields, string $defaultField = 'created_at', string $defaultDirection = 'desc')
    {
        if (in_array($this->sortField, $allowedFields, true)) {
            return $query->orderBy($this->sortField, $this->sortDirection === 'asc' ? 'asc' : 'desc');
        }

        return $query->orderBy($defaultField, $defaultDirection);
    }
}


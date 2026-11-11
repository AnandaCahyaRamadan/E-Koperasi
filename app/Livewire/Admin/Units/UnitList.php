<?php

namespace App\Livewire\Admin\Units;

use Livewire\Component;
use Livewire\WithPagination;
use App\Traits\WithDataTable;
use App\Models\Unit;

class UnitList extends Component
{
    use WithPagination, WithDataTable;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $unitId;
    public $name = '';
    public $symbol = '';
    public $description = '';
    public $showModal = false;

    public function mount()
    {
        $this->sortField = 'name';
        $this->sortDirection = 'asc';
    }

    public function openCreateModal()
    {
        $this->reset(['unitId', 'name', 'symbol', 'description']);
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $unit = Unit::findOrFail($id);
        $this->unitId = $unit->id;
        $this->name = $unit->name;
        $this->symbol = $unit->symbol;
        $this->description = $unit->description;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function save()
    {
        $this->validate([
            'name' => ['required', 'string', 'max:50'],
            'symbol' => ['required', 'string', 'max:20'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        if ($this->unitId) {
            $unit = Unit::findOrFail($this->unitId);
            $unit->update([
                'name' => $this->name,
                'symbol' => $this->symbol,
                'description' => $this->description,
            ]);
            session()->flash('success', "Satuan {$unit->name} berhasil diperbarui.");
        } else {
            Unit::create([
                'name' => $this->name,
                'symbol' => $this->symbol,
                'description' => $this->description,
            ]);
            session()->flash('success', 'Satuan baru berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function deleteUnit($id)
    {
        $unit = Unit::withCount('products')->findOrFail($id);
        if ($unit->products_count > 0) {
            session()->flash('error', "Satuan tidak dapat dihapus karena masih digunakan pada {$unit->products_count} produk.");
            return;
        }

        $unit->delete();
        session()->flash('success', 'Satuan berhasil dihapus.');
    }

    public function render()
    {
        $query = Unit::withCount('products')
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('symbol', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            });

        $this->applySorting($query, ['symbol', 'name', 'products_count'], 'name', 'asc');
        $units = $query->paginate($this->perPage);

        return view('livewire.admin.units.unit-list', [
            'units' => $units,
        ])->layout('layouts.admin');
    }
}


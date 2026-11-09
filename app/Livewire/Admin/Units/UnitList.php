<?php

namespace App\Livewire\Admin\Units;

use Livewire\Component;
use App\Models\Unit;

class UnitList extends Component
{
    public $unitId;
    public $name = '';
    public $symbol = '';
    public $description = '';
    public $showModal = false;

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
        $units = Unit::withCount('products')->orderBy('name')->get();

        return view('livewire.admin.units.unit-list', [
            'units' => $units,
        ])->layout('layouts.admin');
    }
}


<?php

namespace App\Livewire\Admin\Suppliers;

use Livewire\Component;
use App\Models\Supplier;

class SupplierList extends Component
{
    public $supplierId;
    public $name = '';
    public $code = '';
    public $contact_person = '';
    public $phone = '';
    public $email = '';
    public $address = '';
    public $is_active = true;
    public $showModal = false;

    public function openCreateModal()
    {
        $this->reset(['supplierId', 'name', 'code', 'contact_person', 'phone', 'email', 'address', 'is_active']);
        $this->is_active = true;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $sup = Supplier::findOrFail($id);
        $this->supplierId = $sup->id;
        $this->name = $sup->name;
        $this->code = $sup->code;
        $this->contact_person = $sup->contact_person;
        $this->phone = $sup->phone;
        $this->email = $sup->email;
        $this->address = $sup->address;
        $this->is_active = (bool)$sup->is_active;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function save()
    {
        $this->validate([
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        if ($this->supplierId) {
            $sup = Supplier::findOrFail($this->supplierId);
            $sup->update([
                'name' => $this->name,
                'code' => $this->code,
                'contact_person' => $this->contact_person,
                'phone' => $this->phone,
                'email' => $this->email,
                'address' => $this->address,
                'is_active' => $this->is_active,
            ]);
            session()->flash('success', "Pemasok {$sup->name} berhasil diperbarui.");
        } else {
            Supplier::create([
                'name' => $this->name,
                'code' => $this->code ?: 'SUP-' . rand(100, 999),
                'contact_person' => $this->contact_person,
                'phone' => $this->phone,
                'email' => $this->email,
                'address' => $this->address,
                'is_active' => $this->is_active,
            ]);
            session()->flash('success', 'Pemasok baru berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function deleteSupplier($id)
    {
        $sup = Supplier::withCount('purchases')->findOrFail($id);
        if ($sup->purchases_count > 0) {
            session()->flash('error', "Pemasok tidak dapat dihapus karena memiliki riwayat {$sup->purchases_count} pembelian.");
            return;
        }

        $sup->delete();
        session()->flash('success', 'Pemasok berhasil dihapus.');
    }

    public function render()
    {
        $suppliers = Supplier::withCount('purchases')->latest()->get();

        return view('livewire.admin.suppliers.supplier-list', [
            'suppliers' => $suppliers,
        ])->layout('layouts.admin');
    }
}


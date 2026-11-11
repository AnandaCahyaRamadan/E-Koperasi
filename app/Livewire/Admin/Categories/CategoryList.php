<?php

namespace App\Livewire\Admin\Categories;

use Livewire\Component;
use Livewire\WithPagination;
use App\Traits\WithDataTable;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryList extends Component
{
    use WithPagination, WithDataTable;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $categoryId;
    public $name = '';
    public $icon = 'bi-tag';
    public $description = '';
    public $is_active = true;
    public $showModal = false;

    public function openCreateModal()
    {
        $this->reset(['categoryId', 'name', 'icon', 'description', 'is_active']);
        $this->icon = 'bi-tag';
        $this->is_active = true;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $cat = Category::findOrFail($id);
        $this->categoryId = $cat->id;
        $this->name = $cat->name;
        $this->icon = $cat->icon ?? 'bi-tag';
        $this->description = $cat->description;
        $this->is_active = (bool)$cat->is_active;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function save()
    {
        $this->validate([
            'name' => ['required', 'string', 'max:100'],
            'icon' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        if ($this->categoryId) {
            $cat = Category::findOrFail($this->categoryId);
            $cat->update([
                'name' => $this->name,
                'slug' => Str::slug($this->name),
                'icon' => $this->icon,
                'description' => $this->description,
                'is_active' => $this->is_active,
            ]);
            session()->flash('success', "Kategori {$cat->name} berhasil diperbarui.");
        } else {
            Category::create([
                'name' => $this->name,
                'slug' => Str::slug($this->name),
                'icon' => $this->icon,
                'description' => $this->description,
                'is_active' => $this->is_active,
            ]);
            session()->flash('success', 'Kategori baru berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function deleteCategory($id)
    {
        $cat = Category::withCount('products')->findOrFail($id);
        if ($cat->products_count > 0) {
            session()->flash('error', "Kategori tidak dapat dihapus karena masih digunakan oleh {$cat->products_count} produk.");
            return;
        }

        $cat->delete();
        session()->flash('success', 'Kategori berhasil dihapus.');
    }

    public function render()
    {
        $query = Category::withCount('products')
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%')
                        ->orWhere('slug', 'like', '%' . $this->search . '%');
                });
            });

        $this->applySorting($query, ['name', 'slug', 'products_count', 'is_active', 'created_at']);
        $categories = $query->paginate($this->perPage);

        return view('livewire.admin.categories.category-list', [
            'categories' => $categories,
        ])->layout('layouts.admin');
    }
}


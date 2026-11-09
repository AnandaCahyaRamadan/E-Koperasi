<?php

namespace App\Livewire\Admin\Products;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use App\Models\StockMovement;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ProductForm extends Component
{
    use WithFileUploads;

    public $productId;
    public $category_id;
    public $unit_id;
    public $code;
    public $name;
    public $description;
    public $purchase_price = 0;
    public $sell_price = 0;
    public $stock = 0;
    public $min_stock = 5;
    public $image;
    public $existing_image;
    public $is_active = true;

    public function mount($id = null)
    {
        if ($id) {
            $product = Product::findOrFail($id);
            $this->productId = $product->id;
            $this->category_id = $product->category_id;
            $this->unit_id = $product->unit_id;
            $this->code = $product->code;
            $this->name = $product->name;
            $this->description = $product->description;
            $this->purchase_price = (float)$product->purchase_price;
            $this->sell_price = (float)$product->sell_price;
            $this->stock = $product->stock;
            $this->min_stock = $product->min_stock;
            $this->existing_image = $product->image;
            $this->is_active = (bool)$product->is_active;
        } else {
            // Auto generate code suggestion
            $this->code = 'PRD-' . strtoupper(Str::random(6));
        }
    }

    public function save()
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', Rule::unique('products', 'code')->ignore($this->productId)],
            'category_id' => ['nullable', 'exists:categories,id'],
            'unit_id' => ['nullable', 'exists:units,id'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'sell_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];

        $this->validate($rules);

        $imagePath = $this->existing_image;
        if ($this->image) {
            $imagePath = $this->image->store('products', 'public');
        }

        if ($this->productId) {
            $product = Product::findOrFail($this->productId);
            $oldStock = $product->stock;

            $product->update([
                'category_id' => $this->category_id ?: null,
                'unit_id' => $this->unit_id ?: null,
                'code' => $this->code,
                'name' => $this->name,
                'slug' => Str::slug($this->name) . '-' . substr($this->code, -4),
                'description' => $this->description,
                'purchase_price' => $this->purchase_price,
                'sell_price' => $this->sell_price,
                'stock' => $this->stock,
                'min_stock' => $this->min_stock,
                'image' => $imagePath,
                'is_active' => $this->is_active,
            ]);

            // If stock manually changed here, record stock movement
            if ($oldStock != $this->stock) {
                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => Auth::id(),
                    'type' => $this->stock > $oldStock ? 'in' : 'out',
                    'reference_type' => 'manual',
                    'quantity' => abs($this->stock - $oldStock),
                    'stock_before' => $oldStock,
                    'stock_after' => $this->stock,
                    'notes' => 'Koreksi stok langsung via edit data produk',
                ]);
            }

            session()->flash('success', "Produk {$product->name} berhasil diperbarui.");
        } else {
            $product = Product::create([
                'category_id' => $this->category_id ?: null,
                'unit_id' => $this->unit_id ?: null,
                'code' => $this->code,
                'name' => $this->name,
                'slug' => Str::slug($this->name) . '-' . substr($this->code, -4),
                'description' => $this->description,
                'purchase_price' => $this->purchase_price,
                'sell_price' => $this->sell_price,
                'stock' => $this->stock,
                'min_stock' => $this->min_stock,
                'image' => $imagePath,
                'is_active' => $this->is_active,
            ]);

            if ($this->stock > 0) {
                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => Auth::id(),
                    'type' => 'in',
                    'reference_type' => 'initial',
                    'quantity' => $this->stock,
                    'stock_before' => 0,
                    'stock_after' => $this->stock,
                    'notes' => 'Stok awal penambahan produk baru',
                ]);
            }

            session()->flash('success', "Produk {$product->name} berhasil ditambahkan!");
        }

        return redirect()->route('admin.products.index');
    }

    public function render()
    {
        return view('livewire.admin.products.product-form', [
            'categories' => Category::where('is_active', true)->get(),
            'units' => Unit::all(),
        ])->layout('layouts.admin');
    }
}


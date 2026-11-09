<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Root redirect to customer home
Route::get('/', function () {
    return redirect()->route('customer.home');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Customer / Pelanggan Routes
Route::prefix('customer')->group(function () {
    Route::get('/', \App\Livewire\Customer\Home::class)->name('customer.home');
    Route::get('/products', \App\Livewire\Customer\ProductCatalog::class)->name('customer.products');

    Route::middleware(['auth', 'role:pelanggan,petugas,admin'])->group(function () {
        Route::get('/cart', \App\Livewire\Customer\CartView::class)->name('customer.cart');
        Route::get('/checkout', \App\Livewire\Customer\CheckoutView::class)->name('customer.checkout');
        Route::get('/orders', \App\Livewire\Customer\OrderList::class)->name('customer.orders');
        Route::get('/orders/{id}', \App\Livewire\Customer\OrderDetailView::class)->name('customer.orders.detail');
        Route::get('/profile', \App\Livewire\Customer\ProfileView::class)->name('customer.profile');
    });
});

// Petugas / Kasir Routes
Route::prefix('petugas')->middleware(['auth', 'role:petugas,admin'])->group(function () {
    Route::get('/dashboard', \App\Livewire\Petugas\Dashboard::class)->name('petugas.dashboard');
    Route::get('/pos', \App\Livewire\Petugas\PosKasir::class)->name('petugas.pos');
    Route::get('/payments', \App\Livewire\Petugas\PaymentVerification::class)->name('petugas.payments.index');
    Route::get('/orders', \App\Livewire\Petugas\OrderManagement::class)->name('petugas.orders.index');
    Route::get('/orders/{id}', \App\Livewire\Petugas\OrderDetail::class)->name('petugas.orders.detail');
    Route::get('/products', \App\Livewire\Petugas\ProductCatalog::class)->name('petugas.products.index');
    Route::get('/history', \App\Livewire\Petugas\TransactionHistory::class)->name('petugas.history');
});

// Admin Routes
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', \App\Livewire\Admin\Dashboard::class)->name('admin.dashboard');

    // Products
    Route::get('/products', \App\Livewire\Admin\Products\ProductList::class)->name('admin.products.index');
    Route::get('/products/create', \App\Livewire\Admin\Products\ProductForm::class)->name('admin.products.create');
    Route::get('/products/{id}/edit', \App\Livewire\Admin\Products\ProductForm::class)->name('admin.products.edit');

    // Categories & Units
    Route::get('/categories', \App\Livewire\Admin\Categories\CategoryList::class)->name('admin.categories.index');
    Route::get('/units', \App\Livewire\Admin\Units\UnitList::class)->name('admin.units.index');

    // Inventory & Stock
    Route::get('/inventory', \App\Livewire\Admin\Inventory\InventoryIndex::class)->name('admin.inventory.index');
    Route::get('/purchases', \App\Livewire\Admin\Purchases\PurchaseList::class)->name('admin.purchases.index');
    Route::get('/purchases/create', \App\Livewire\Admin\Purchases\PurchaseForm::class)->name('admin.purchases.create');
    Route::get('/suppliers', \App\Livewire\Admin\Suppliers\SupplierList::class)->name('admin.suppliers.index');
    Route::get('/stock-movements', \App\Livewire\Admin\StockMovements\StockMovementList::class)->name('admin.stock-movements.index');

    // Stock Opname (Admin exclusive)
    Route::get('/stock-opname', \App\Livewire\Admin\StockOpname\OpnameList::class)->name('admin.stock-opname.index');
    Route::get('/stock-opname/create', \App\Livewire\Admin\StockOpname\OpnameCreate::class)->name('admin.stock-opname.create');
    Route::get('/stock-opname/{id}', \App\Livewire\Admin\StockOpname\OpnameDetailView::class)->name('admin.stock-opname.detail');

    // Orders & Payments
    Route::get('/orders', \App\Livewire\Admin\Orders\OrderList::class)->name('admin.orders.index');
    Route::get('/orders/{id}', \App\Livewire\Admin\Orders\OrderDetail::class)->name('admin.orders.detail');
    Route::get('/payments', \App\Livewire\Admin\Payments\PaymentIndex::class)->name('admin.payments.index');

    // Reports
    Route::get('/reports/sales', \App\Livewire\Admin\Reports\SalesReport::class)->name('admin.reports.sales');
    Route::get('/reports/sales/pdf', [ReportController::class, 'salesPdf'])->name('admin.reports.sales.pdf');
    Route::get('/reports/sales/csv', [ReportController::class, 'salesCsv'])->name('admin.reports.sales.csv');
    Route::get('/reports/inventory', \App\Livewire\Admin\Reports\InventoryReport::class)->name('admin.reports.inventory');
    Route::get('/reports/inventory/pdf', [ReportController::class, 'inventoryPdf'])->name('admin.reports.inventory.pdf');
    Route::get('/reports/inventory/csv', [ReportController::class, 'inventoryCsv'])->name('admin.reports.inventory.csv');

    // Users & Settings
    Route::get('/users', \App\Livewire\Admin\Users\UserList::class)->name('admin.users.index');
    Route::get('/settings', \App\Livewire\Admin\Settings\SettingIndex::class)->name('admin.settings.index');
});

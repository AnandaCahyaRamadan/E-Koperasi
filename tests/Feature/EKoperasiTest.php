<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class EKoperasiTest extends TestCase
{
    public function test_root_redirects_to_customer_home(): void
    {
        $response = $this->get('/');
        $response->assertRedirect(route('customer.home'));
    }

    public function test_customer_home_page_loads_successfully(): void
    {
        $response = $this->get('/customer');
        $response->assertStatus(200);
        $response->assertSee('Katalog Produk');
    }

    public function test_login_page_loads_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Masuk');
    }

    public function test_admin_can_access_dashboard(): void
    {
        $admin = User::whereHas('role', fn ($q) => $q->where('slug', 'admin'))->first();
        if ($admin) {
            $response = $this->actingAs($admin)->get('/admin/dashboard');
            $response->assertStatus(200);
            $response->assertSee('Dashboard Administrator');
        }
    }

    public function test_petugas_can_access_pos_and_orders(): void
    {
        $petugas = User::whereHas('role', fn ($q) => $q->where('slug', 'petugas'))->first();
        if ($petugas) {
            $response = $this->actingAs($petugas)->get('/petugas/pos');
            $response->assertStatus(200);
            $response->assertSee('Transaksi Kasir');
        }
    }

    public function test_pelanggan_cannot_access_admin_panel(): void
    {
        $pelanggan = User::whereHas('role', fn ($q) => $q->where('slug', 'pelanggan'))->first();
        if ($pelanggan) {
            $response = $this->actingAs($pelanggan)->get('/admin/dashboard');
            // Should redirect back to customer home
            $response->assertRedirect(route('customer.home'));
        }
    }

    public function test_admin_reports_pdf_download(): void
    {
        $admin = User::whereHas('role', fn ($q) => $q->where('slug', 'admin'))->first();
        if ($admin) {
            $response = $this->actingAs($admin)->get('/admin/reports/sales/pdf');
            $response->assertStatus(200);
            $response->assertHeader('content-type', 'application/pdf');

            $response2 = $this->actingAs($admin)->get('/admin/reports/inventory/pdf');
            $response2->assertStatus(200);
            $response2->assertHeader('content-type', 'application/pdf');
        }
    }

    public function test_admin_reports_csv_download(): void
    {
        $admin = User::whereHas('role', fn ($q) => $q->where('slug', 'admin'))->first();
        if ($admin) {
            $response = $this->actingAs($admin)->get('/admin/reports/sales/csv');
            $response->assertStatus(200);
            $response->assertHeader('content-type', 'text/csv; charset=UTF-8');

            $response2 = $this->actingAs($admin)->get('/admin/reports/inventory/csv');
            $response2->assertStatus(200);
            $response2->assertHeader('content-type', 'text/csv; charset=UTF-8');
        }
    }

    public function test_offline_assets_are_loaded(): void
    {
        $response = $this->get('/customer');
        $response->assertStatus(200);
        $response->assertSee('vendor/bootstrap/css/bootstrap.min.css');
        $response->assertSee('vendor/bootstrap/js/bootstrap.bundle.min.js');
        $response->assertSee('vendor/bootstrap-icons/bootstrap-icons.min.css');
        $response->assertSee('vendor/sweetalert2/sweetalert2.all.min.js');
        $response->assertSee('vendor/toastr/toastr.min.js');
        $response->assertSee('vendor/flatpickr/flatpickr.min.js');
    }

    public function test_flash_messages_use_toaster_and_no_inline_alert(): void
    {
        $response = $this->withSession(['success' => 'Anda telah berhasil logout.'])->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Anda telah berhasil logout.');
        $response->assertDontSee('alert alert-success');
        $response->assertSee('toastr');
    }

    public function test_all_admin_and_petugas_tables_render_with_datatable_and_pagination(): void
    {
        $admin = User::whereHas('role', fn ($q) => $q->where('slug', 'admin'))->first();
        if ($admin) {
            $adminTableRoutes = [
                '/admin/dashboard',
                '/admin/products',
                '/admin/categories',
                '/admin/units',
                '/admin/inventory',
                '/admin/purchases',
                '/admin/suppliers',
                '/admin/stock-movements',
                '/admin/stock-opname',
                '/admin/orders',
                '/admin/payments',
                '/admin/reports/sales',
                '/admin/reports/inventory',
                '/admin/users',
            ];

            foreach ($adminTableRoutes as $url) {
                $response = $this->actingAs($admin)->get($url);
                $response->assertStatus(200);
                $response->assertSee('datatable-custom');
                $response->assertSee('Tampilkan');
            }
        }

        $petugas = User::whereHas('role', fn ($q) => $q->where('slug', 'petugas'))->first();
        if ($petugas) {
            $petugasTableRoutes = [
                '/petugas/dashboard',
                '/petugas/payments',
                '/petugas/orders',
                '/petugas/products',
                '/petugas/history',
            ];

            foreach ($petugasTableRoutes as $url) {
                $response = $this->actingAs($petugas)->get($url);
                $response->assertStatus(200);
                $response->assertSee('datatable-custom');
                $response->assertSee('Tampilkan');
            }
        }
    }
}

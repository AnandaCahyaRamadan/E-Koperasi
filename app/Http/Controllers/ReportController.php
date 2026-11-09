<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function salesPdf(Request $request)
    {
        $startDate = $request->query('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', now()->toDateString());

        $orders = Order::with(['user', 'items'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->where('payment_status', 'paid')
            ->latest()
            ->get();

        $totalRevenue = $orders->sum('final_amount');
        $koperasiName = Setting::get('koperasi_name', 'E-Koperasi Pegawai');

        $pdf = Pdf::loadView('reports.sales-pdf', [
            'orders' => $orders,
            'totalRevenue' => $totalRevenue,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'koperasiName' => $koperasiName,
        ]);

        return $pdf->download("laporan-penjualan-{$startDate}-sd-{$endDate}.pdf");
    }

    public function salesCsv(Request $request)
    {
        $startDate = $request->query('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', now()->toDateString());

        $orders = Order::with(['user', 'cashier'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->where('payment_status', 'paid')
            ->latest()
            ->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"laporan-penjualan-{$startDate}-sd-{$endDate}.csv\"",
        ];

        $callback = function () use ($orders) {
            $handle = fopen('php://output', 'w');
            // Add BOM for Excel UTF-8 support
            fputs($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, ['No. Pesanan', 'Tanggal', 'Pelanggan', 'Metode Bayar', 'Tipe Layanan', 'Total Tagihan (Rp)', 'Status']);

            foreach ($orders as $ord) {
                fputcsv($handle, [
                    $ord->order_number,
                    $ord->created_at->format('Y-m-d H:i'),
                    $ord->user?->name ?? 'Kasir Langsung',
                    strtoupper($ord->payment_method),
                    $ord->order_type_label,
                    $ord->final_amount,
                    $ord->status_label,
                ]);
            }

            fclose($handle);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    public function inventoryPdf()
    {
        $products = Product::with(['category', 'unit'])->orderBy('category_id')->get();
        $totalStock = $products->sum('stock');
        $totalValue = $products->sum(fn($p) => $p->stock * $p->purchase_price);
        $koperasiName = Setting::get('koperasi_name', 'E-Koperasi Pegawai');

        $pdf = Pdf::loadView('reports.inventory-pdf', [
            'products' => $products,
            'totalStock' => $totalStock,
            'totalValue' => $totalValue,
            'koperasiName' => $koperasiName,
        ]);

        return $pdf->download('laporan-persediaan-stok-' . date('Y-m-d') . '.pdf');
    }

    public function inventoryCsv()
    {
        $products = Product::with(['category', 'unit'])->orderBy('name')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="laporan-persediaan-' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($products) {
            $handle = fopen('php://output', 'w');
            fputs($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, ['Kode SKU', 'Nama Produk', 'Kategori', 'Satuan', 'Harga Beli (Rp)', 'Harga Jual (Rp)', 'Stok Fisik', 'Min. Stok', 'Total Nilai Aset (Rp)']);

            foreach ($products as $p) {
                fputcsv($handle, [
                    $p->code,
                    $p->name,
                    $p->category?->name ?? 'Umum',
                    $p->unit?->symbol ?? 'pcs',
                    $p->purchase_price,
                    $p->sell_price,
                    $p->stock,
                    $p->min_stock,
                    $p->stock * $p->purchase_price,
                ]);
            }

            fclose($handle);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}


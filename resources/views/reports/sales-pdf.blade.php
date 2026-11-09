<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 11px; color: #252B33; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #16834A; padding-bottom: 10px; }
        .title { font-size: 16px; font-weight: bold; color: #16834A; margin: 0; }
        .subtitle { font-size: 11px; color: #666; margin-top: 3px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background-color: #f4f6f8; color: #333; font-weight: bold; text-align: left; padding: 6px; border: 1px solid #ddd; }
        td { padding: 6px; border: 1px solid #ddd; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .total-row { background-color: #e8f5ee; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">{{ $koperasiName }}</div>
        <div class="subtitle">LAPORAN REKAPITULASI PENJUALAN</div>
        <div class="subtitle">Periode: {{ date('d/m/Y', strtotime($startDate)) }} s/d {{ date('d/m/Y', strtotime($endDate)) }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 25px;" class="text-center">No</th>
                <th>No. Pesanan</th>
                <th>Waktu Transaksi</th>
                <th>Pelanggan</th>
                <th>Metode</th>
                <th>Layanan</th>
                <th class="text-end">Total Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $idx => $ord)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td><strong>{{ $ord->order_number }}</strong></td>
                    <td>{{ $ord->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $ord->user?->name ?? 'Kasir POS' }}</td>
                    <td>{{ strtoupper($ord->payment_method) }}</td>
                    <td>{{ $ord->order_type_label }}</td>
                    <td class="text-end">Rp {{ number_format($ord->final_amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada transaksi pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="6" class="text-end">TOTAL OMZET PENJUALAN:</td>
                <td class="text-end">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 30px; float: right; width: 200px; text-align: center;">
        <div>Dicetak pada: {{ date('d/m/Y H:i') }}</div>
        <div style="margin-top: 50px; border-bottom: 1px solid #000;"></div>
        <div>Pengelola / Kasir Koperasi</div>
    </div>
</body>
</html>


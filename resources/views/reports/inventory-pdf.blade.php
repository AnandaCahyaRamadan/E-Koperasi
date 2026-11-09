<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Persediaan Stok</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 10px; color: #252B33; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #16834A; padding-bottom: 10px; }
        .title { font-size: 15px; font-weight: bold; color: #16834A; margin: 0; }
        .subtitle { font-size: 11px; color: #666; margin-top: 3px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background-color: #f4f6f8; color: #333; font-weight: bold; text-align: left; padding: 5px; border: 1px solid #ddd; }
        td { padding: 5px; border: 1px solid #ddd; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .total-row { background-color: #e8f5ee; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">{{ $koperasiName }}</div>
        <div class="subtitle">LAPORAN REKAPITULASI PERSEDIAAN BARANG & NILAI ASET</div>
        <div class="subtitle">Per Tanggal: {{ date('d F Y') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 25px;" class="text-center">No</th>
                <th>Kode SKU</th>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th class="text-center">Satuan</th>
                <th class="text-end">Harga Beli</th>
                <th class="text-end">Harga Jual</th>
                <th class="text-center">Sisa Stok</th>
                <th class="text-end">Total Nilai Aset</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $idx => $p)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>{{ $p->code }}</td>
                    <td><strong>{{ $p->name }}</strong></td>
                    <td>{{ $p->category?->name ?? 'Umum' }}</td>
                    <td class="text-center">{{ $p->unit?->symbol ?? 'pcs' }}</td>
                    <td class="text-end">Rp {{ number_format($p->purchase_price, 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($p->sell_price, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $p->stock }}</td>
                    <td class="text-end">Rp {{ number_format($p->stock * $p->purchase_price, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="7" class="text-end">TOTAL KESELURUHAN:</td>
                <td class="text-center">{{ number_format($totalStock, 0, ',', '.') }} unit</td>
                <td class="text-end">Rp {{ number_format($totalValue, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 30px; float: right; width: 200px; text-align: center;">
        <div>Dicetak pada: {{ date('d/m/Y H:i') }}</div>
        <div style="margin-top: 50px; border-bottom: 1px solid #000;"></div>
        <div>Bagian Persediaan & Logistik</div>
    </div>
</body>
</html>


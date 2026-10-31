<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        h3 { font-size: 14px; margin-top: 20px; margin-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #999; padding: 4px 8px; text-align: left; }
        th { background: #eee; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <h1>Laporan Penjualan</h1>
    <p>Periode {{ $from->format('d/m/Y') }} s.d. {{ $to->format('d/m/Y') }}</p>
    <p>Total penjualan: <strong>Rp {{ number_format($totalPenjualan) }}</strong> dari {{ $jumlahTransaksi }} transaksi</p>

    <h3>5 Produk Terlaris</h3>
    <table>
        <thead>
            <tr>
                <th>Peringkat</th>
                <th>Nama Produk</th>
                <th class="right">Total Terjual (Qty)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($topProducts as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->product->name ?? 'Produk Dihapus' }}</td>
                <td class="right">{{ $item->total_terjual }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h3>50 Transaksi Terbaru</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tanggal</th>
                <th>Kasir</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $t)
            <tr>
                <td>{{ $t->id }}</td>
                <td>{{ $t->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $t->user->name }}</td>
                <td class="right">Rp {{ number_format($t->total) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
<?php

use App\Models\Product;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('api:demo', function () {
    $baseUrl = rtrim(config('app.url'), '/') . '/api/v1';

    $this->info('=== DEMO SIMPLE POS API V1 ===');

    // 1. Login
    $login = Http::acceptJson()->post($baseUrl . '/login', [
        'email' => 'kasir@pos.test',
        'password' => 'password',
    ]);

    $this->line('Login: HTTP ' . $login->status());

    if (! $login->successful() || ! $login->json('token')) {
        $this->error('Login gagal. Periksa server dan akun demo.');
        return;
    }

    $token = $login->json('token');

    // Gunakan request baru untuk setiap pemanggilan API
    $api = fn () => Http::acceptJson()->withToken($token);

    // 2. Akses produk tanpa token
    $unauthorized = Http::acceptJson()->get($baseUrl . '/products');
    $this->line('Produk tanpa token: HTTP ' . $unauthorized->status());

    // 3. Akses produk dengan token
    $products = $api()->get($baseUrl . '/products');
    $this->line('Produk dengan token: HTTP ' . $products->status());

    // Cari produk dengan stok tersedia
    $product = Product::where('stock', '>', 0)->first();

    if (! $product) {
        $this->error('Tidak ada produk dengan stok tersedia.');
        return;
    }

    $payload = [
        'items' => [
            [
                'product_id' => $product->id,
                'qty' => 1,
            ],
        ],
    ];

    // 4. Transaksi valid
    $key = (string) \Illuminate\Support\Str::uuid();

    $transaction = $api()->withHeaders([
        'Idempotency-Key' => $key,
    ])->post($baseUrl . '/transactions', $payload);

    $this->line('Transaksi valid: HTTP ' . $transaction->status());

    // 5. Transaksi dengan data tidak valid
    $invalid = $api()->withHeaders([
        'Idempotency-Key' => (string) \Illuminate\Support\Str::uuid(),
    ])->post($baseUrl . '/transactions', [
        'items' => [
            [
                'product_id' => 999999,
                'qty' => 1,
            ],
        ],
    ]);

    $this->line('Transaksi tidak valid: HTTP ' . $invalid->status());

    // 6. Ulangi request dengan idempotency key yang sama
    $replay = $api()->withHeaders([
        'Idempotency-Key' => $key,
    ])->post($baseUrl . '/transactions', $payload);

    $this->line('Request diulang: HTTP ' . $replay->status());
    $this->line(
        'Idempotent replay: ' .
        ($replay->header('Idempotent-Replayed') === 'true' ? 'Berhasil' : 'Tidak terdeteksi')
    );

    $this->info('=== DEMO SELESAI ===');
})->purpose('Menjalankan demo integrasi Simple POS API v1');
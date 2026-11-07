<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpiredTokenTest extends TestCase
{
    use RefreshDatabase;

    public function test_token_yang_sudah_kedaluwarsa_menghasilkan_401(): void
    {
        // 1. Set waktu sekarang ke tanggal 1 Januari 2026
        Carbon::setTestNow('2026-01-01 00:00:00');

        // 2. Buat user
        $user = User::factory()->create();

        // 3. Buat TOKEN ASLI yang hanya berlaku 1 hari
        $token = $user->createToken('test-token', ['products:read'], now()->addDay())->plainTextToken;

        // 4. Lompati waktu ke tanggal 3 Januari 2026 (token sudah kedaluwarsa)
        Carbon::setTestNow('2026-01-03 00:00:00');

        // 5. Akses endpoint pakai token asli tadi
        $response = $this->withHeader('Authorization', "Bearer {$token}")
                         ->getJson('/api/products');

        // 6. Pastikan statusnya 401 Unauthorized
        $response->assertUnauthorized();
    }
}
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_access_transactions(): void
    {
        $manager = User::factory()->create([
            'role' => 'manager',
        ]);

        $response = $this->actingAs($manager)->get('/transactions');

        $response->assertStatus(200);
    }

    public function test_manager_cannot_access_products(): void
    {
        $manager = User::factory()->create([
            'role' => 'manager',
        ]);

        $response = $this->actingAs($manager)->get('/products');

        $response->assertStatus(403);
    }

    public function test_manager_cannot_access_categories(): void
    {
        $manager = User::factory()->create([
            'role' => 'manager',
        ]);

        $response = $this->actingAs($manager)->get('/categories');

        $response->assertStatus(403);
    }

    public function test_admin_can_access_both_transactions_and_products(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin)->get('/transactions')->assertStatus(200);
        $this->actingAs($admin)->get('/products')->assertStatus(200);
    }

    public function test_kasir_cannot_access_transactions_or_products(): void
    {
        $kasir = User::factory()->create([
            'role' => 'kasir',
        ]);

        $this->actingAs($kasir)->get('/transactions')->assertStatus(403);
        $this->actingAs($kasir)->get('/products')->assertStatus(403);
    }

    public function test_kasir_can_access_pos(): void
    {
        $kasir = User::factory()->create([
            'role' => 'kasir',
        ]);

        $this->actingAs($kasir)->get('/pos')->assertStatus(200);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuyerRoleTest extends TestCase
{
    use RefreshDatabase;

    private function buyer(): User
    {
        return User::factory()->create(['role' => 'pembeli']);
    }

    public function test_login_pembeli_diarahkan_ke_katalog(): void
    {
        $buyer = $this->buyer();

        $this->post('/login', ['email' => $buyer->email, 'password' => 'password'])
            ->assertRedirect(route('products.index'));
    }

    public function test_pembeli_melihat_katalog_tanpa_harga_beli(): void
    {
        $category = Category::create(['name' => 'Umum']);
        $product = Product::create([
            'category_id' => $category->id, 'sku' => 'KTL-1', 'name' => 'Kopi Katalog',
            'purchase_price' => 1000, 'selling_price' => 5000, 'stock' => 10, 'minimum_stock' => 2,
        ]);
        $buyer = $this->buyer();

        $this->actingAs($buyer)->get('/products')->assertOk()->assertSee('Kopi Katalog');
        $this->actingAs($buyer)->get("/products/{$product->id}")
            ->assertOk()
            ->assertSee('Harga jual')
            ->assertDontSee('Harga beli');
    }

    public function test_pembeli_ditolak_di_halaman_admin_dan_staff(): void
    {
        $buyer = $this->buyer();

        $this->actingAs($buyer)->get('/dashboard')->assertForbidden();
        $this->actingAs($buyer)->get('/sales')->assertForbidden();
        $this->actingAs($buyer)->get('/sales/create')->assertForbidden();
        $this->actingAs($buyer)->post('/sales', [])->assertForbidden();
        $this->actingAs($buyer)->get('/purchases')->assertForbidden();
        $this->actingAs($buyer)->get('/categories')->assertForbidden();
        $this->actingAs($buyer)->get('/users')->assertForbidden();
    }

    public function test_admin_dapat_membuat_akun_pembeli(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post('/users', [
            'name' => 'Pembeli Baru', 'email' => 'pembeli@example.com', 'role' => 'pembeli',
            'password' => 'rahasia123', 'password_confirmation' => 'rahasia123',
        ])->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', ['email' => 'pembeli@example.com', 'role' => 'pembeli']);
    }

    public function test_role_tidak_dikenal_ditolak(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post('/users', [
            'name' => 'Orang Aneh', 'email' => 'aneh@example.com', 'role' => 'hacker',
            'password' => 'rahasia123', 'password_confirmation' => 'rahasia123',
        ])->assertSessionHasErrors('role');
    }
}

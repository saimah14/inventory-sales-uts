<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function staff(): User
    {
        return User::factory()->create(['role' => 'staff']);
    }

    private function makeProduct(array $override = []): Product
    {
        $category = Category::firstOrCreate(['name' => 'Umum']);

        return Product::create(array_merge([
            'category_id' => $category->id,
            'sku' => 'SKU-' . uniqid(),
            'name' => 'Produk Uji',
            'purchase_price' => 1000,
            'selling_price' => 2000,
            'stock' => 10,
            'minimum_stock' => 2,
        ], $override));
    }

    // ---------- Auth ----------
    public function test_guest_tidak_bisa_membuka_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_password_salah_ditolak(): void
    {
        User::factory()->create(['email' => 'uji@example.com']);

        $this->post('/login', ['email' => 'uji@example.com', 'password' => 'salah'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    // ---------- Produk ----------
    public function test_sku_duplikat_ditolak(): void
    {
        $existing = $this->makeProduct(['sku' => 'DUP-1']);

        $this->actingAs($this->admin())->post('/products', [
            'sku' => 'DUP-1', 'name' => 'Barang Baru', 'category_id' => $existing->category_id,
            'purchase_price' => 1000, 'selling_price' => 2000, 'stock' => 5, 'minimum_stock' => 1,
        ])->assertSessionHasErrors('sku');

        $this->assertEquals(1, Product::count());
    }

    public function test_harga_negatif_ditolak(): void
    {
        $existing = $this->makeProduct();

        $this->actingAs($this->admin())->post('/products', [
            'sku' => 'NEG-1', 'name' => 'Barang Minus', 'category_id' => $existing->category_id,
            'purchase_price' => 1000, 'selling_price' => -5, 'stock' => 5, 'minimum_stock' => 1,
        ])->assertSessionHasErrors('selling_price');
    }

    // ---------- Pembelian & Penjualan ----------
    public function test_pembelian_menambah_stok(): void
    {
        $supplier = Supplier::create(['name' => 'PT Uji']);
        $product = $this->makeProduct(['stock' => 10]);

        $this->actingAs($this->staff())->post('/purchases', [
            'supplier_id' => $supplier->id,
            'items' => [['product_id' => $product->id, 'quantity' => 5, 'unit_cost' => 1500]],
        ])->assertSessionHasNoErrors();

        $this->assertEquals(15, $product->fresh()->stock);
        $this->assertEquals(7500, Purchase::first()->total);
    }

    public function test_penjualan_mengurangi_stok(): void
    {
        $product = $this->makeProduct(['stock' => 10, 'selling_price' => 2000]);

        $this->actingAs($this->staff())->post('/sales', [
            'items' => [['product_id' => $product->id, 'quantity' => 3]],
        ])->assertSessionHasNoErrors();

        $this->assertEquals(7, $product->fresh()->stock);
        $this->assertEquals(6000, Sale::first()->total);
    }

    public function test_penjualan_stok_tidak_cukup_ditolak_tanpa_perubahan_data(): void
    {
        $product = $this->makeProduct(['stock' => 4]);

        $this->actingAs($this->staff())->post('/sales', [
            'items' => [['product_id' => $product->id, 'quantity' => 6]],
        ])->assertSessionHasErrors('items');

        $this->assertEquals(4, $product->fresh()->stock);
        $this->assertEquals(0, Sale::count());
    }

    public function test_jika_satu_item_gagal_seluruh_transaksi_rollback(): void
    {
        $a = $this->makeProduct(['name' => 'Produk A', 'stock' => 10]);
        $b = $this->makeProduct(['name' => 'Produk B', 'stock' => 1]);

        $this->actingAs($this->staff())->post('/sales', [
            'items' => [
                ['product_id' => $a->id, 'quantity' => 2],
                ['product_id' => $b->id, 'quantity' => 5],
            ],
        ])->assertSessionHasErrors('items');

        $this->assertEquals(10, $a->fresh()->stock);
        $this->assertEquals(1, $b->fresh()->stock);
        $this->assertEquals(0, Sale::count());
    }

    public function test_pembatalan_penjualan_mengembalikan_stok(): void
    {
        $admin = $this->admin();
        $product = $this->makeProduct(['stock' => 10]);

        $this->actingAs($this->staff())->post('/sales', [
            'items' => [['product_id' => $product->id, 'quantity' => 4]],
        ]);
        $this->assertEquals(6, $product->fresh()->stock);

        $sale = Sale::first();
        $this->actingAs($admin)->post("/sales/{$sale->id}/cancel");

        $this->assertEquals(10, $product->fresh()->stock);
        $this->assertEquals('cancelled', $sale->fresh()->status);
    }

    // ---------- Otorisasi ----------
    public function test_staff_tidak_bisa_mengakses_fungsi_admin(): void
    {
        $staff = $this->staff();

        $this->actingAs($staff)->get('/categories')->assertForbidden();
        $this->actingAs($staff)->get('/users')->assertForbidden();
        $this->actingAs($staff)->post('/products', [])->assertForbidden();
    }

    // ---------- Search, filter, pagination ----------
    public function test_pencarian_produk_berdasarkan_nama(): void
    {
        $this->makeProduct(['name' => 'Kopi Hitam', 'sku' => 'KPI-1']);
        $this->makeProduct(['name' => 'Teh Manis', 'sku' => 'TEH-1']);

        $this->actingAs($this->admin())->get('/products?q=Kopi')
            ->assertSee('Kopi Hitam')
            ->assertDontSee('Teh Manis');
    }

    public function test_filter_stok_rendah(): void
    {
        $this->makeProduct(['name' => 'Habis Banget', 'sku' => 'LOW-1', 'stock' => 1, 'minimum_stock' => 5]);
        $this->makeProduct(['name' => 'Aman Banget', 'sku' => 'OK-1', 'stock' => 50, 'minimum_stock' => 5]);

        $this->actingAs($this->admin())->get('/products?low=1')
            ->assertSee('Habis Banget')
            ->assertDontSee('Aman Banget');
    }

    public function test_pagination_produk(): void
    {
        for ($i = 1; $i <= 12; $i++) {
            $this->makeProduct(['name' => sprintf('Produk %02d', $i), 'sku' => "PG-$i"]);
        }

        $this->actingAs($this->admin())->get('/products?page=2')
            ->assertSee('Produk 12')
            ->assertDontSee('Produk 01');
    }
}


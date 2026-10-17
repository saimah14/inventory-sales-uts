<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderFlowTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role): User
    {
        return User::factory()->create(['role' => $role]);
    }

    private function makeProduct(int $stock = 10): Product
    {
        $category = Category::firstOrCreate(['name' => 'Umum']);

        return Product::create([
            'category_id' => $category->id, 'sku' => 'ORD-' . uniqid(), 'name' => 'Produk Pesanan',
            'purchase_price' => 1000, 'selling_price' => 2000, 'stock' => $stock, 'minimum_stock' => 1,
        ]);
    }

    private function makeOrder(User $buyer, Product $product, int $qty = 3): Order
    {
        $order = Order::create([
            'user_id' => $buyer->id, 'order_number' => 'OD-' . uniqid(),
            'total' => 2000 * $qty, 'status' => 'pending',
        ]);
        $order->items()->create([
            'product_id' => $product->id, 'quantity' => $qty,
            'unit_price' => 2000, 'subtotal' => 2000 * $qty,
        ]);

        return $order;
    }

    public function test_pembeli_menambah_produk_ke_keranjang(): void
    {
        $product = $this->makeProduct(10);

        $this->actingAs($this->user('pembeli'))
            ->post('/cart', ['product_id' => $product->id, 'quantity' => 2])
            ->assertSessionHasNoErrors();

        $this->assertEquals(2, session('cart')[$product->id]);
    }

    public function test_keranjang_melebihi_stok_ditolak(): void
    {
        $product = $this->makeProduct(5);

        $this->actingAs($this->user('pembeli'))
            ->post('/cart', ['product_id' => $product->id, 'quantity' => 9])
            ->assertSessionHasErrors('quantity');
    }

    public function test_checkout_membuat_pesanan_tanpa_mengubah_stok(): void
    {
        $product = $this->makeProduct(10);

        $this->actingAs($this->user('pembeli'))
            ->withSession(['cart' => [$product->id => 3]])
            ->post('/orders', ['note' => 'Antar sore'])
            ->assertSessionHasNoErrors();

        $order = Order::first();
        $this->assertNotNull($order);
        $this->assertEquals('pending', $order->status);
        $this->assertEquals(6000, $order->total);
        $this->assertEquals(1, $order->items()->count());
        $this->assertEquals(10, $product->fresh()->stock);
        $this->assertEquals(0, Sale::count());
    }

    public function test_checkout_keranjang_kosong_ditolak(): void
    {
        $this->actingAs($this->user('pembeli'))->post('/orders')->assertSessionHasErrors('cart');

        $this->assertEquals(0, Order::count());
    }

    public function test_checkout_stok_tidak_cukup_ditolak(): void
    {
        $product = $this->makeProduct(5);

        $this->actingAs($this->user('pembeli'))
            ->withSession(['cart' => [$product->id => 9]])
            ->post('/orders')
            ->assertSessionHasErrors('cart');

        $this->assertEquals(0, Order::count());
    }

    public function test_staff_menerima_pesanan_mengurangi_stok_dan_membuat_penjualan(): void
    {
        $staff = $this->user('staff');
        $product = $this->makeProduct(10);
        $order = $this->makeOrder($this->user('pembeli'), $product, 3);

        $this->actingAs($staff)->post("/orders/{$order->id}/accept")->assertSessionHasNoErrors();

        $this->assertEquals(7, $product->fresh()->stock);
        $this->assertEquals('accepted', $order->fresh()->status);
        $this->assertEquals(1, Sale::count());
        $this->assertEquals(6000, Sale::first()->total);
        $this->assertEquals($staff->id, Sale::first()->user_id);
        $this->assertNotNull($order->fresh()->sale_id);
    }

    public function test_penerimaan_ditolak_jika_stok_tidak_cukup(): void
    {
        $product = $this->makeProduct(2);
        $order = $this->makeOrder($this->user('pembeli'), $product, 3);

        $this->actingAs($this->user('staff'))
            ->post("/orders/{$order->id}/accept")
            ->assertSessionHasErrors('order');

        $this->assertEquals(2, $product->fresh()->stock);
        $this->assertEquals('pending', $order->fresh()->status);
        $this->assertEquals(0, Sale::count());
    }

    public function test_pesanan_tidak_bisa_diterima_dua_kali(): void
    {
        $staff = $this->user('staff');
        $product = $this->makeProduct(10);
        $order = $this->makeOrder($this->user('pembeli'), $product, 3);

        $this->actingAs($staff)->post("/orders/{$order->id}/accept");
        $this->actingAs($staff)->post("/orders/{$order->id}/accept")->assertSessionHasErrors('order');

        $this->assertEquals(7, $product->fresh()->stock);
        $this->assertEquals(1, Sale::count());
    }

    public function test_staff_menolak_pesanan_tanpa_mengubah_stok(): void
    {
        $product = $this->makeProduct(10);
        $order = $this->makeOrder($this->user('pembeli'), $product, 3);

        $this->actingAs($this->user('staff'))->post("/orders/{$order->id}/reject");

        $this->assertEquals('rejected', $order->fresh()->status);
        $this->assertEquals(10, $product->fresh()->stock);
        $this->assertEquals(0, Sale::count());
    }

    public function test_admin_hanya_bisa_melihat_pesanan_dan_penjualan(): void
    {
        $admin = $this->user('admin');
        $product = $this->makeProduct(10);
        $order = $this->makeOrder($this->user('pembeli'), $product, 3);

        $this->actingAs($admin)->get('/orders')->assertOk()->assertSee($order->order_number);
        $this->actingAs($admin)->get("/orders/{$order->id}")->assertOk();
        $this->actingAs($admin)->get('/sales')->assertOk();

        $this->actingAs($admin)->post("/orders/{$order->id}/accept")->assertForbidden();
        $this->actingAs($admin)->get('/sales/create')->assertForbidden();
        $this->actingAs($admin)->post('/sales', [])->assertForbidden();

        $this->assertEquals('pending', $order->fresh()->status);
    }

    public function test_pembeli_tidak_bisa_menerima_atau_melihat_pesanan_orang_lain(): void
    {
        $product = $this->makeProduct(10);
        $order = $this->makeOrder($this->user('pembeli'), $product, 3);
        $other = $this->user('pembeli');

        $this->actingAs($other)->post("/orders/{$order->id}/accept")->assertForbidden();
        $this->actingAs($other)->get("/orders/{$order->id}")->assertForbidden();
        $this->actingAs($other)->post("/orders/{$order->id}/cancel")->assertForbidden();
    }

    public function test_pembeli_membatalkan_pesanan_sendiri(): void
    {
        $buyer = $this->user('pembeli');
        $order = $this->makeOrder($buyer, $this->makeProduct(10), 3);

        $this->actingAs($buyer)->post("/orders/{$order->id}/cancel")->assertSessionHasNoErrors();

        $this->assertEquals('cancelled', $order->fresh()->status);
    }

    public function test_pengguna_yang_punya_pesanan_tidak_bisa_dihapus(): void
    {
        $buyer = $this->user('pembeli');
        $this->makeOrder($buyer, $this->makeProduct(10), 1);

        $this->actingAs($this->user('admin'))->delete("/users/{$buyer->id}");

        $this->assertDatabaseHas('users', ['id' => $buyer->id]);
    }

    public function test_menu_dan_tombol_sesuai_role(): void
    {
        $this->makeProduct(10);

        $this->actingAs($this->user('pembeli'))->get('/products')
            ->assertOk()
            ->assertSee('+ Keranjang')
            ->assertSee('Pesanan Saya');

        $this->actingAs($this->user('staff'))->get('/dashboard')
            ->assertOk()
            ->assertSee('Pesanan');
    }
}

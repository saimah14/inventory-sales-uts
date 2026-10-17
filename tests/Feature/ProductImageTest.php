<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ProductImageTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $extra = []): array
    {
        $category = Category::firstOrCreate(['name' => 'Umum']);

        return array_merge([
            'sku' => 'IMG-1', 'name' => 'Produk Foto', 'category_id' => $category->id,
            'purchase_price' => 1000, 'selling_price' => 2000, 'stock' => 5, 'minimum_stock' => 1,
        ], $extra);
    }

    public function test_upload_foto_produk_tersimpan(): void
    {
        if (!extension_loaded('gd')) {
            $this->markTestSkipped('Ekstensi GD tidak aktif.');
        }

        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post('/products', $this->payload([
            'image' => UploadedFile::fake()->image('foto.jpg', 200, 200),
        ]))->assertSessionHasNoErrors();

        $product = Product::where('sku', 'IMG-1')->first();
        $this->assertNotNull($product->image);
        $this->assertFileExists(public_path($product->image));

        @unlink(public_path($product->image));
    }

    public function test_file_bukan_gambar_ditolak(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post('/products', $this->payload([
            'image' => UploadedFile::fake()->create('dokumen.pdf', 10, 'application/pdf'),
        ]))->assertSessionHasErrors('image');

        $this->assertEquals(0, Product::count());
    }

    public function test_dashboard_menghitung_dari_database(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'Umum']);
        Product::create([
            'category_id' => $category->id, 'sku' => 'DSH-1', 'name' => 'Barang Dashboard',
            'purchase_price' => 1000, 'selling_price' => 2000, 'stock' => 1, 'minimum_stock' => 5,
        ]);
        Sale::create([
            'user_id' => $admin->id, 'sale_number' => 'SL-D1', 'total' => 50000,
            'status' => 'completed', 'sold_at' => now(),
        ]);

        $this->actingAs($admin)->get('/dashboard')
            ->assertOk()
            ->assertSee('50.000')
            ->assertSee('Barang Dashboard');
    }
}

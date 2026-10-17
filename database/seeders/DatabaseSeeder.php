<?php
namespace Database\Seeders;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create(['name' => 'Admin Owner', 'email' => 'admin@example.com', 'password' => Hash::make('password'), 'role' => 'admin']);
        User::create(['name' => 'Staff Demo', 'email' => 'staff@example.com', 'password' => Hash::make('password'), 'role' => 'staff']);
        User::create(['name' => 'Pembeli Demo', 'email' => 'pembeli@example.com', 'password' => Hash::make('password'), 'role' => 'pembeli']);

        $makanan = Category::create(['name' => 'Makanan']);
        $minuman = Category::create(['name' => 'Minuman']);
        $rumah = Category::create(['name' => 'Peralatan Rumah']);

        Supplier::create(['name' => 'PT Sumber Makmur', 'phone' => '021-5550101', 'email' => 'sales@sumbermakmur.test']);
        Supplier::create(['name' => 'CV Jaya Abadi', 'phone' => '021-5550202', 'email' => 'order@jayaabadi.test']);

        Product::create(['category_id' => $makanan->id, 'sku' => 'MKN-001', 'name' => 'Mie Instan Goreng', 'purchase_price' => 2500, 'selling_price' => 3500, 'stock' => 100, 'minimum_stock' => 20]);
        Product::create(['category_id' => $makanan->id, 'sku' => 'MKN-002', 'name' => 'Biskuit Cokelat', 'purchase_price' => 6000, 'selling_price' => 8000, 'stock' => 50, 'minimum_stock' => 10]);
        Product::create(['category_id' => $minuman->id, 'sku' => 'MNM-001', 'name' => 'Air Mineral 600ml', 'purchase_price' => 2000, 'selling_price' => 3000, 'stock' => 5, 'minimum_stock' => 24]);
        Product::create(['category_id' => $minuman->id, 'sku' => 'MNM-002', 'name' => 'Teh Botol 450ml', 'purchase_price' => 3500, 'selling_price' => 5000, 'stock' => 60, 'minimum_stock' => 12]);
        Product::create(['category_id' => $rumah->id, 'sku' => 'RMH-001', 'name' => 'Sabun Cuci Piring', 'purchase_price' => 9000, 'selling_price' => 12500, 'stock' => 30, 'minimum_stock' => 8]);
    }
}


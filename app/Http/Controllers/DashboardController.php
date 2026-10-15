<?php
namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\Sale;
class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'totalProducts' => Product::count(),
            'lowStock' => Product::whereColumn('stock', '<=', 'minimum_stock')->count(),
            'salesCount' => Sale::where('status', 'completed')->count(),
            'salesToday' => Sale::where('status', 'completed')->whereDate('sold_at', today())->sum('total'),
        ]);
    }
}

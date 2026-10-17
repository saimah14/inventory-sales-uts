<?php
namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\Sale;
class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return view('dashboard', [
            'totalProducts' => Product::count(),
            'lowStock' => Product::whereColumn('stock', '<=', 'minimum_stock')->count(),
            'salesCount' => Sale::where('status', 'completed')->count(),
            'salesToday' => Sale::where('status', 'completed')->whereDate('sold_at', today())->sum('total'),
            'salesMonth' => Sale::where('status', 'completed')
                ->whereYear('sold_at', now()->year)
                ->whereMonth('sold_at', now()->month)
                ->sum('total'),
            'recentSales' => Sale::with('user')
                ->when(!$user->isAdmin(), fn ($q) => $q->where('user_id', $user->id))
                ->latest('sold_at')
                ->limit(5)
                ->get(),
            'lowProducts' => Product::with('category')
                ->whereColumn('stock', '<=', 'minimum_stock')
                ->orderBy('stock')
                ->limit(5)
                ->get(),
        ]);
    }
}

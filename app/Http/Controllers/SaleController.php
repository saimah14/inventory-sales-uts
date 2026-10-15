<?php
namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
            'status' => ['nullable', 'in:completed,cancelled'],
        ]);

        $sales = Sale::with('user')
            ->when(!auth()->user()->isAdmin(), fn ($q) => $q->where('user_id', auth()->id()))
            ->when($request->q, fn ($q, $s) => $q->where('sale_number', 'like', "%$s%"))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->from, fn ($q, $d) => $q->whereDate('sold_at', '>=', $d))
            ->when($request->to, fn ($q, $d) => $q->whereDate('sold_at', '<=', $d))
            ->latest('sold_at')
            ->paginate(10)
            ->withQueryString();

        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        return view('sales.create', ['products' => Product::orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', Rule::exists('products', 'id')->whereNull('deleted_at')],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        // Gabungkan produk yang sama, lalu urutkan supaya penguncian baris konsisten
        $items = collect($data['items'])
            ->groupBy('product_id')
            ->map(fn ($rows) => (int) $rows->sum('quantity'))
            ->sortKeys();

        // Semua perubahan dalam SATU database transaction: gagal satu = rollback semua
        $sale = DB::transaction(function () use ($items) {
            $sale = Sale::create([
                'user_id' => auth()->id(),
                'sale_number' => 'SL-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5)),
                'total' => 0,
                'status' => 'completed',
                'sold_at' => now(),
            ]);

            $total = 0;

            foreach ($items as $productId => $qty) {
                // lockForUpdate: mencegah dua penjualan membaca stok yang sama bersamaan
                $product = Product::lockForUpdate()->findOrFail($productId);

                if ($product->stock < $qty) {
                    throw ValidationException::withMessages([
                        'items' => "Stok {$product->name} tidak cukup (tersedia {$product->stock}, diminta {$qty}).",
                    ]);
                }

                // Harga diambil dari database, bukan dari browser
                $subtotal = $product->selling_price * $qty;

                $sale->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $product->selling_price,
                    'subtotal' => $subtotal,
                ]);

                $product->decrement('stock', $qty);
                $total += $subtotal;
            }

            $sale->update(['total' => $total]);

            return $sale;
        });

        return redirect()->route('sales.show', $sale)->with('success', 'Penjualan berhasil disimpan.');
    }

    public function show(Sale $sale)
    {
        abort_unless(auth()->user()->isAdmin() || $sale->user_id === auth()->id(), 403);
        $sale->load(['user', 'items.product' => fn ($q) => $q->withTrashed()]);
        return view('sales.show', compact('sale'));
    }

    public function cancel(Sale $sale)
    {
        DB::transaction(function () use ($sale) {
            $sale = Sale::lockForUpdate()->findOrFail($sale->id);

            if ($sale->status !== 'completed') {
                throw ValidationException::withMessages(['status' => 'Transaksi ini sudah dibatalkan.']);
            }

            foreach ($sale->items as $item) {
                Product::withTrashed()->lockForUpdate()->findOrFail($item->product_id)
                    ->increment('stock', $item->quantity);
            }

            $sale->update(['status' => 'cancelled']);
        });

        return back()->with('success', 'Transaksi dibatalkan dan stok dikembalikan.');
    }
}

<?php
namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
            'status' => ['nullable', 'in:completed,cancelled'],
        ]);

        $purchases = Purchase::with(['supplier', 'user'])
            ->when(!auth()->user()->isAdmin(), fn ($q) => $q->where('user_id', auth()->id()))
            ->when($request->q, fn ($q, $s) => $q->where('purchase_number', 'like', "%$s%"))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->from, fn ($q, $d) => $q->whereDate('purchased_at', '>=', $d))
            ->when($request->to, fn ($q, $d) => $q->whereDate('purchased_at', '<=', $d))
            ->latest('purchased_at')
            ->paginate(10)
            ->withQueryString();

        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        return view('purchases.create', [
            'suppliers' => Supplier::orderBy('name')->get(),
            'products' => Product::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', Rule::exists('products', 'id')->whereNull('deleted_at')],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0'],
        ]);

        $purchase = DB::transaction(function () use ($data) {
            $purchase = Purchase::create([
                'supplier_id' => $data['supplier_id'],
                'user_id' => auth()->id(),
                'purchase_number' => 'PO-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5)),
                'total' => 0,
                'status' => 'completed',
                'purchased_at' => now(),
            ]);

            $total = 0;

            foreach (collect($data['items'])->sortBy('product_id') as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);
                $qty = (int) $item['quantity'];
                $cost = (float) $item['unit_cost'];
                $subtotal = $qty * $cost; // dihitung server

                $purchase->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_cost' => $cost,
                    'subtotal' => $subtotal,
                ]);

                $product->increment('stock', $qty);
                $total += $subtotal;
            }

            $purchase->update(['total' => $total]);

            return $purchase;
        });

        return redirect()->route('purchases.show', $purchase)->with('success', 'Pembelian berhasil disimpan, stok bertambah.');
    }

    public function show(Purchase $purchase)
    {
        abort_unless(auth()->user()->isAdmin() || $purchase->user_id === auth()->id(), 403);
        $purchase->load(['supplier', 'user', 'items.product' => fn ($q) => $q->withTrashed()]);
        return view('purchases.show', compact('purchase'));
    }

    public function cancel(Purchase $purchase)
    {
        DB::transaction(function () use ($purchase) {
            $purchase = Purchase::lockForUpdate()->findOrFail($purchase->id);

            if ($purchase->status !== 'completed') {
                throw ValidationException::withMessages(['status' => 'Transaksi ini sudah dibatalkan.']);
            }

            foreach ($purchase->items as $item) {
                $product = Product::withTrashed()->lockForUpdate()->findOrFail($item->product_id);

                // Stok tidak boleh menjadi negatif
                if ($product->stock < $item->quantity) {
                    throw ValidationException::withMessages([
                        'status' => "Tidak bisa dibatalkan: stok {$product->name} sudah terpakai penjualan.",
                    ]);
                }

                $product->decrement('stock', $item->quantity);
            }

            $purchase->update(['status' => 'cancelled']);
        });

        return back()->with('success', 'Pembelian dibatalkan dan stok dikurangi kembali.');
    }
}

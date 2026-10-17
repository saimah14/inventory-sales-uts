<?php
namespace App\Http\Controllers;
use App\Models\Order;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $request->validate(['status' => ['nullable', 'in:pending,accepted,rejected,cancelled']]);
        $user = auth()->user();

        $orders = Order::with(['user', 'processor'])
            ->when($user->isBuyer(), fn ($q) => $q->where('user_id', $user->id))
            ->when($request->q, fn ($q, $s) => $q->where('order_number', 'like', "%$s%"))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $user = auth()->user();
        abort_unless(!$user->isBuyer() || $order->user_id === $user->id, 403);

        $order->load(['user', 'processor', 'sale', 'items.product' => fn ($q) => $q->withTrashed()]);

        return view('orders.show', compact('order'));
    }

    // Pembeli: checkout keranjang menjadi pesanan (stok BELUM berkurang)
    public function store(Request $request)
    {
        $data = $request->validate(['note' => ['nullable', 'string', 'max:200']]);
        $cart = session('cart', []);

        if (empty($cart)) {
            throw ValidationException::withMessages(['cart' => 'Keranjang masih kosong.']);
        }

        $order = DB::transaction(function () use ($cart, $data) {
            $order = Order::create([
                'user_id' => auth()->id(),
                'order_number' => 'OD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5)),
                'total' => 0,
                'status' => 'pending',
                'note' => $data['note'] ?? null,
            ]);

            $total = 0;
            ksort($cart);

            foreach ($cart as $productId => $qty) {
                $qty = (int) $qty;
                $product = Product::find($productId);

                if (!$product) {
                    throw ValidationException::withMessages(['cart' => 'Ada produk di keranjang yang sudah tidak tersedia.']);
                }
                if ($qty < 1 || $product->stock < $qty) {
                    throw ValidationException::withMessages([
                        'cart' => "Stok {$product->name} tidak cukup (tersedia {$product->stock}).",
                    ]);
                }

                // Harga diambil dari database, bukan dari browser
                $subtotal = $product->selling_price * $qty;
                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $product->selling_price,
                    'subtotal' => $subtotal,
                ]);
                $total += $subtotal;
            }

            $order->update(['total' => $total]);

            return $order;
        });

        session()->forget('cart');

        return redirect()->route('orders.show', $order)->with('success', 'Pesanan berhasil dibuat. Menunggu konfirmasi staff.');
    }

    // Pembeli: batalkan pesanan miliknya yang masih menunggu
    public function cancel(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        DB::transaction(function () use ($order) {
            $order = Order::lockForUpdate()->findOrFail($order->id);

            if ($order->status !== 'pending') {
                throw ValidationException::withMessages(['order' => 'Hanya pesanan yang masih menunggu yang bisa dibatalkan.']);
            }

            $order->update(['status' => 'cancelled']);
        });

        return back()->with('success', 'Pesanan dibatalkan.');
    }

    // Staff: terima pesanan -> buat penjualan + kurangi stok dalam SATU transaction
    public function accept(Order $order)
    {
        DB::transaction(function () use ($order) {
            $order = Order::lockForUpdate()->findOrFail($order->id);

            if ($order->status !== 'pending') {
                throw ValidationException::withMessages(['order' => 'Pesanan ini sudah diproses.']);
            }

            $sale = Sale::create([
                'user_id' => auth()->id(),
                'sale_number' => 'SL-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5)),
                'total' => 0,
                'status' => 'completed',
                'sold_at' => now(),
            ]);

            $total = 0;

            foreach ($order->items->sortBy('product_id') as $item) {
                $product = Product::withTrashed()->lockForUpdate()->findOrFail($item->product_id);

                if ($product->stock < $item->quantity) {
                    throw ValidationException::withMessages([
                        'order' => "Stok {$product->name} tidak cukup (tersedia {$product->stock}, diminta {$item->quantity}).",
                    ]);
                }

                $sale->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'subtotal' => $item->subtotal,
                ]);

                $product->decrement('stock', $item->quantity);
                $total += $item->subtotal;
            }

            $sale->update(['total' => $total]);
            $order->update([
                'status' => 'accepted',
                'processed_by' => auth()->id(),
                'processed_at' => now(),
                'sale_id' => $sale->id,
            ]);
        });

        return back()->with('success', 'Pesanan diterima. Stok berkurang dan transaksi penjualan tercatat.');
    }

    // Staff: tolak pesanan (stok tidak berubah)
    public function reject(Order $order)
    {
        DB::transaction(function () use ($order) {
            $order = Order::lockForUpdate()->findOrFail($order->id);

            if ($order->status !== 'pending') {
                throw ValidationException::withMessages(['order' => 'Pesanan ini sudah diproses.']);
            }

            $order->update([
                'status' => 'rejected',
                'processed_by' => auth()->id(),
                'processed_at' => now(),
            ]);
        });

        return back()->with('success', 'Pesanan ditolak.');
    }
}

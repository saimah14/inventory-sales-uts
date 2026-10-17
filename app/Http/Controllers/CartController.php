<?php
namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    private function lines(): array
    {
        $cart = session('cart', []);
        $products = Product::with('category')->whereIn('id', array_keys($cart))->get()->keyBy('id');
        $lines = [];
        $total = 0;

        foreach ($cart as $id => $qty) {
            $product = $products->get($id);
            if (!$product) {
                continue;
            }
            $subtotal = $product->selling_price * $qty;
            $lines[] = ['product' => $product, 'quantity' => $qty, 'subtotal' => $subtotal];
            $total += $subtotal;
        }

        return [$lines, $total];
    }

    public function index()
    {
        [$lines, $total] = $this->lines();
        return view('cart.index', compact('lines', 'total'));
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', Rule::exists('products', 'id')->whereNull('deleted_at')],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::findOrFail($data['product_id']);
        $cart = session('cart', []);
        $newQty = ($cart[$product->id] ?? 0) + (int) $data['quantity'];

        if ($newQty > $product->stock) {
            throw ValidationException::withMessages([
                'quantity' => "Stok {$product->name} hanya {$product->stock}.",
            ]);
        }

        $cart[$product->id] = $newQty;
        session(['cart' => $cart]);

        return back()->with('success', "{$product->name} ditambahkan ke keranjang.");
    }

    public function update(Request $request)
    {
        $request->validate([
            'quantities' => ['required', 'array'],
            'quantities.*' => ['required', 'integer', 'min:0'],
        ]);

        $cart = [];
        foreach ($request->quantities as $id => $qty) {
            $qty = (int) $qty;
            $product = Product::find($id);
            if ($qty < 1 || !$product) {
                continue;
            }
            if ($qty > $product->stock) {
                throw ValidationException::withMessages([
                    'quantities' => "Stok {$product->name} hanya {$product->stock}.",
                ]);
            }
            $cart[$product->id] = $qty;
        }

        session(['cart' => $cart]);

        return back()->with('success', 'Keranjang diperbarui.');
    }

    public function remove($productId)
    {
        $cart = session('cart', []);
        unset($cart[(int) $productId]);
        session(['cart' => $cart]);

        return back()->with('success', 'Item dihapus dari keranjang.');
    }
}

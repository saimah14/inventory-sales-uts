@extends('layouts.app')
@section('title', 'Keranjang')
@section('content')
<h1>Keranjang Belanja</h1>
@include('partials.errors')
@if(count($lines) === 0)
    <div class="card">Keranjang masih kosong. <a href="{{ route('products.index') }}">Lihat katalog</a></div>
@else
<form id="updateForm" method="POST" action="{{ route('cart.update') }}">
    @csrf
    @method('PATCH')
</form>
<table>
    <tr><th>Produk</th><th>Harga</th><th>Jumlah</th><th>Subtotal</th><th></th></tr>
    @foreach($lines as $l)
        <tr>
            <td>{{ $l['product']->name }}</td>
            <td>Rp {{ number_format($l['product']->selling_price, 0, ',', '.') }}</td>
            <td><input form="updateForm" type="number" min="0" max="{{ $l['product']->stock }}" name="quantities[{{ $l['product']->id }}]" value="{{ $l['quantity'] }}" style="width:80px"></td>
            <td>Rp {{ number_format($l['subtotal'], 0, ',', '.') }}</td>
            <td>
                <form method="POST" action="{{ route('cart.remove', $l['product']->id) }}" style="display:inline">
                    @csrf
                    @method('DELETE')
                    <button class="link-danger">Hapus</button>
                </form>
            </td>
        </tr>
    @endforeach
</table>
<p><button form="updateForm" class="btn">Perbarui keranjang</button></p>
<p><strong>Total: Rp {{ number_format($total, 0, ',', '.') }}</strong> (dihitung ulang oleh server saat checkout)</p>
<form method="POST" action="{{ route('orders.store') }}">
    @csrf
    <p><label>Catatan (opsional)<br><input name="note" maxlength="200" value="{{ old('note') }}"></label></p>
    <button class="btn">Checkout</button>
</form>
@endif
@endsection

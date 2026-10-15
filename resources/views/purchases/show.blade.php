@extends('layouts.app')
@section('title', 'Detail Pembelian')
@section('content')
<h1>Pembelian {{ $purchase->purchase_number }}</h1>
@include('partials.errors')
<p>Supplier: {{ $purchase->supplier->name }} | Dicatat oleh: {{ $purchase->user->name }} | Waktu: {{ $purchase->purchased_at->format('d/m/Y H:i') }} | Status: <strong>{{ $purchase->status }}</strong></p>
<table>
    <tr><th>Produk</th><th>Jumlah</th><th>Harga beli</th><th>Subtotal</th></tr>
    @foreach($purchase->items as $it)
        <tr>
            <td>{{ $it->product->name }}</td>
            <td>{{ $it->quantity }}</td>
            <td>Rp {{ number_format($it->unit_cost, 0, ',', '.') }}</td>
            <td>Rp {{ number_format($it->subtotal, 0, ',', '.') }}</td>
        </tr>
    @endforeach
</table>
<p><strong>Total: Rp {{ number_format($purchase->total, 0, ',', '.') }}</strong></p>
@if(auth()->user()->isAdmin() && $purchase->status === 'completed')
    <form method="POST" action="{{ route('purchases.cancel', $purchase) }}" onsubmit="return confirm('Batalkan pembelian ini? Stok akan dikurangi kembali.')">
        @csrf
        <button class="btn" style="background:#c0392b">Batalkan Pembelian</button>
    </form>
@endif
<p><a href="{{ route('purchases.index') }}">Kembali</a></p>
@endsection

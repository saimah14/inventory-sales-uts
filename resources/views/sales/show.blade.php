@extends('layouts.app')
@section('title', 'Detail Penjualan')
@section('content')
<h1>Penjualan {{ $sale->sale_number }}</h1>
@include('partials.errors')
<p>Kasir: {{ $sale->user->name }} | Waktu: {{ $sale->sold_at->format('d/m/Y H:i') }} | Status: <strong>{{ $sale->status }}</strong></p>
<table>
    <tr><th>Produk</th><th>Jumlah</th><th>Harga</th><th>Subtotal</th></tr>
    @foreach($sale->items as $it)
        <tr>
            <td>{{ $it->product->name }}</td>
            <td>{{ $it->quantity }}</td>
            <td>Rp {{ number_format($it->unit_price, 0, ',', '.') }}</td>
            <td>Rp {{ number_format($it->subtotal, 0, ',', '.') }}</td>
        </tr>
    @endforeach
</table>
<p><strong>Total: Rp {{ number_format($sale->total, 0, ',', '.') }}</strong></p>
@if(auth()->user()->isAdmin() && $sale->status === 'completed')
    <form method="POST" action="{{ route('sales.cancel', $sale) }}" onsubmit="return confirm('Batalkan transaksi ini? Stok akan dikembalikan.')">
        @csrf
        <button class="btn" style="background:#c0392b">Batalkan Transaksi</button>
    </form>
@endif
<p><a href="{{ route('sales.index') }}">Kembali</a></p>
@endsection

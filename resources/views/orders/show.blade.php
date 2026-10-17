@extends('layouts.app')
@section('title', 'Detail Pesanan')
@section('content')
@php
    $labels = ['pending' => 'Menunggu', 'accepted' => 'Diterima', 'rejected' => 'Ditolak', 'cancelled' => 'Dibatalkan'];
    $colors = ['pending' => '', 'accepted' => 'badge-ok', 'rejected' => 'badge-bad', 'cancelled' => 'badge-bad'];
@endphp
<h1>Pesanan {{ $order->order_number }}</h1>
@include('partials.errors')
<p>
    Pembeli: {{ $order->user->name }} | Waktu: {{ $order->created_at->format('d/m/Y H:i') }} |
    Status: <span class="badge {{ $colors[$order->status] ?? '' }}">{{ $labels[$order->status] ?? $order->status }}</span>
</p>
@if($order->note)
    <p>Catatan pembeli: {{ $order->note }}</p>
@endif
@if($order->processor)
    <p>Diproses oleh: {{ $order->processor->name }} ({{ $order->processed_at->format('d/m/Y H:i') }})</p>
@endif
<table>
    <tr><th>Produk</th><th>Jumlah</th><th>Harga</th><th>Subtotal</th></tr>
    @foreach($order->items as $it)
        <tr>
            <td>{{ $it->product->name }}</td>
            <td>{{ $it->quantity }}</td>
            <td>Rp {{ number_format($it->unit_price, 0, ',', '.') }}</td>
            <td>Rp {{ number_format($it->subtotal, 0, ',', '.') }}</td>
        </tr>
    @endforeach
</table>
<p><strong>Total: Rp {{ number_format($order->total, 0, ',', '.') }}</strong></p>

@if($order->status === 'pending')
    @if(auth()->user()->role === 'staff')
        <form method="POST" action="{{ route('orders.accept', $order) }}" style="display:inline-block">
            @csrf
            <button class="btn">Terima Pesanan</button>
        </form>
        <form method="POST" action="{{ route('orders.reject', $order) }}" style="display:inline-block" onsubmit="return confirm('Tolak pesanan ini?')">
            @csrf
            <button class="btn" style="background:#dc2626">Tolak</button>
        </form>
    @elseif(auth()->user()->isBuyer())
        <form method="POST" action="{{ route('orders.cancel', $order) }}" onsubmit="return confirm('Batalkan pesanan ini?')">
            @csrf
            <button class="btn" style="background:#dc2626">Batalkan Pesanan</button>
        </form>
    @endif
@endif

@if($order->sale && (auth()->user()->isAdmin() || $order->processed_by == auth()->id()))
    <p>Transaksi penjualan: <a href="{{ route('sales.show', $order->sale) }}">{{ $order->sale->sale_number }}</a></p>
@endif
<p><a href="{{ route('orders.index') }}">Kembali</a></p>
@endsection

@extends('layouts.app')
@section('title', 'Pesanan')
@section('content')
@php
    $labels = ['pending' => 'Menunggu', 'accepted' => 'Diterima', 'rejected' => 'Ditolak', 'cancelled' => 'Dibatalkan'];
    $colors = ['pending' => '', 'accepted' => 'badge-ok', 'rejected' => 'badge-bad', 'cancelled' => 'badge-bad'];
@endphp
<div class="page-head">
    <h1>{{ auth()->user()->isBuyer() ? 'Pesanan Saya' : 'Pesanan Masuk' }}</h1>
</div>
@if(auth()->user()->role === 'admin')
    <p class="muted">Admin hanya dapat melihat data pesanan dan penjualan.</p>
@endif
<form method="GET" action="{{ route('orders.index') }}" class="filters">
    <input name="q" value="{{ request('q') }}" placeholder="No. pesanan">
    <select name="status">
        <option value="">Semua status</option>
        @foreach($labels as $key => $text)
            <option value="{{ $key }}" @selected(request('status') == $key)>{{ $text }}</option>
        @endforeach
    </select>
    <button class="btn">Cari</button>
    <a href="{{ route('orders.index') }}">Reset</a>
</form>
<table>
    <tr>
        <th>No. Pesanan</th><th>Waktu</th>
        @unless(auth()->user()->isBuyer())<th>Pembeli</th>@endunless
        <th>Total</th><th>Status</th><th></th>
    </tr>
    @forelse($orders as $o)
        <tr>
            <td>{{ $o->order_number }}</td>
            <td>{{ $o->created_at->format('d/m/Y H:i') }}</td>
            @unless(auth()->user()->isBuyer())<td>{{ $o->user->name }}</td>@endunless
            <td>Rp {{ number_format($o->total, 0, ',', '.') }}</td>
            <td><span class="badge {{ $colors[$o->status] ?? '' }}">{{ $labels[$o->status] ?? $o->status }}</span></td>
            <td><a href="{{ route('orders.show', $o) }}">Detail</a></td>
        </tr>
    @empty
        <tr><td colspan="6">Belum ada pesanan.</td></tr>
    @endforelse
</table>
{{ $orders->links() }}
@endsection

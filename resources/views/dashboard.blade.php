@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="page-head">
    <div>
        <h1>Dashboard</h1>
        <div class="muted">Selamat datang, {{ auth()->user()->name }}.</div>
    </div>
</div>
<div class="cards">
    <a class="card stat" href="{{ route('products.index') }}">
        <div class="ico">&#128230;</div>
        <div><div class="num">{{ $totalProducts }}</div><div class="lbl">Total produk</div></div>
    </a>
    <a class="card stat" href="{{ route('products.index', ['low' => 1]) }}">
        <div class="ico">&#9888;</div>
        <div><div class="num">{{ $lowStock }}</div><div class="lbl">Produk stok rendah</div></div>
    </a>
    <div class="card stat">
        <div class="ico">&#129534;</div>
        <div><div class="num">{{ $salesCount }}</div><div class="lbl">Transaksi penjualan</div></div>
    </div>
    <div class="card stat">
        <div class="ico">&#128176;</div>
        <div><div class="num">Rp {{ number_format($salesToday, 0, ',', '.') }}</div><div class="lbl">Penjualan hari ini</div></div>
    </div>
    <div class="card stat">
        <div class="ico">&#128202;</div>
        <div><div class="num">Rp {{ number_format($salesMonth, 0, ',', '.') }}</div><div class="lbl">Penjualan bulan ini</div></div>
    </div>
</div>
<div class="grid-2">
    <div class="panel">
        <h2>Penjualan terbaru</h2>
        <table>
            <tr><th>No.</th><th>Waktu</th><th>Total</th></tr>
            @forelse($recentSales as $s)
                <tr>
                    <td><a href="{{ route('sales.show', $s) }}">{{ $s->sale_number }}</a></td>
                    <td>{{ $s->sold_at->format('d/m H:i') }}</td>
                    <td>Rp {{ number_format($s->total, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="3">Belum ada penjualan.</td></tr>
            @endforelse
        </table>
    </div>
    <div class="panel">
        <h2>Produk stok rendah</h2>
        <table>
            <tr><th>Produk</th><th>Stok</th><th>Minimum</th></tr>
            @forelse($lowProducts as $p)
                <tr>
                    <td>{{ $p->name }}</td>
                    <td><span class="badge badge-bad">{{ $p->stock }}</span></td>
                    <td>{{ $p->minimum_stock }}</td>
                </tr>
            @empty
                <tr><td colspan="3">Semua stok aman.</td></tr>
            @endforelse
        </table>
    </div>
</div>
@endsection

@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<h1>Dashboard</h1>
<div class="cards">
    <div class="card"><h2>{{ $totalProducts }}</h2><p>Total produk</p></div>
    <div class="card"><h2>{{ $lowStock }}</h2><p>Produk stok rendah</p></div>
    <div class="card"><h2>{{ $salesCount }}</h2><p>Jumlah transaksi penjualan</p></div>
    <div class="card"><h2>Rp {{ number_format($salesToday, 0, ',', '.') }}</h2><p>Penjualan hari ini</p></div>
</div>
@endsection

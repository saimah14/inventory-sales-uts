@extends('layouts.app')
@section('title', 'Ringkasan Stok')
@section('content')
<h1>Ringkasan Stok</h1>
<div class="cards">
    <div class="card"><div style="font-size:24px;font-weight:700">{{ $totalStock }}</div><div class="muted">Total unit stok</div></div>
    <div class="card"><div style="font-size:24px;font-weight:700">Rp {{ number_format($totalValue, 0, ',', '.') }}</div><div class="muted">Nilai persediaan (harga beli)</div></div>
</div>
<table>
    <tr><th>Produk</th><th>Kategori</th><th>Stok</th><th>Nilai</th></tr>
    @foreach($products as $p)
        <tr>
            <td>{{ $p->name }}</td>
            <td>{{ $p->category->name }}</td>
            <td>{{ $p->stock }}</td>
            <td>Rp {{ number_format($p->stock * $p->purchase_price, 0, ',', '.') }}</td>
        </tr>
    @endforeach
</table>
@endsection

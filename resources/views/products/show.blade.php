@extends('layouts.app')
@section('title', 'Detail Produk')
@section('content')
<h1>{{ $product->name }}</h1>
<table>
    <tr><th>SKU</th><td>{{ $product->sku }}</td></tr>
    <tr><th>Kategori</th><td>{{ $product->category->name }}</td></tr>
    @unless(auth()->user()->isBuyer())
            <tr><th>Harga beli</th><td>Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</td></tr>
            @endunless
    <tr><th>Harga jual</th><td>Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td></tr>
    <tr><th>Stok</th><td>{{ $product->stock }}</td></tr>
    <tr><th>Stok minimum</th><td>{{ $product->minimum_stock }}</td></tr>
</table>
<p><a href="{{ route('products.index') }}">Kembali</a></p>
@endsection


@extends('layouts.app')
@section('title', 'Form Produk')
@section('content')
<h1>{{ $product->exists ? 'Ubah' : 'Tambah' }} Produk</h1>
@include('partials.errors')
<form method="POST" action="{{ $product->exists ? route('products.update', $product) : route('products.store') }}">
    @csrf
    @if($product->exists)
        @method('PUT')
    @endif
    <p><label>SKU<br><input name="sku" value="{{ old('sku', $product->sku) }}" required></label></p>
    <p><label>Nama<br><input name="name" value="{{ old('name', $product->name) }}" required></label></p>
    <p><label>Kategori<br>
        <select name="category_id" required>
            <option value="">-- pilih kategori --</option>
            @foreach($categories as $c)
                <option value="{{ $c->id }}" @selected(old('category_id', $product->category_id) == $c->id)>{{ $c->name }}</option>
            @endforeach
        </select></label></p>
    <p><label>Harga beli<br><input type="number" step="0.01" min="0" name="purchase_price" value="{{ old('purchase_price', $product->purchase_price) }}" required></label></p>
    <p><label>Harga jual<br><input type="number" step="0.01" min="0" name="selling_price" value="{{ old('selling_price', $product->selling_price) }}" required></label></p>
    @if($product->exists)
        <p>Stok saat ini: <strong>{{ $product->stock }}</strong> (stok hanya berubah lewat transaksi pembelian/penjualan)</p>
    @else
        <p><label>Stok awal<br><input type="number" min="0" name="stock" value="{{ old('stock', 0) }}" required></label></p>
    @endif
    <p><label>Stok minimum<br><input type="number" min="0" name="minimum_stock" value="{{ old('minimum_stock', $product->minimum_stock ?? 0) }}" required></label></p>
    <button class="btn">Simpan</button>
    <a href="{{ route('products.index') }}">Batal</a>
</form>
@endsection

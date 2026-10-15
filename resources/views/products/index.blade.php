@extends('layouts.app')
@section('title', 'Produk')
@section('content')
<h1>Produk</h1>
@if(auth()->user()->isAdmin())
    <p><a class="btn" href="{{ route('products.create') }}">+ Tambah Produk</a></p>
@endif
<form method="GET" action="{{ route('products.index') }}" class="filters">
    <input name="q" value="{{ request('q') }}" placeholder="Cari SKU / nama">
    <select name="category_id">
        <option value="">Semua kategori</option>
        @foreach($categories as $c)
            <option value="{{ $c->id }}" @selected(request('category_id') == $c->id)>{{ $c->name }}</option>
        @endforeach
    </select>
    <label><input type="checkbox" name="low" value="1" @checked(request('low'))> Stok rendah saja</label>
    <button class="btn">Cari</button>
    <a href="{{ route('products.index') }}">Reset</a>
</form>
<table>
    <tr><th>SKU</th><th>Nama</th><th>Kategori</th><th>Harga Jual</th><th>Stok</th><th>Aksi</th></tr>
    @forelse($products as $p)
        <tr>
            <td>{{ $p->sku }}</td>
            <td>{{ $p->name }}</td>
            <td>{{ $p->category->name }}</td>
            <td>Rp {{ number_format($p->selling_price, 0, ',', '.') }}</td>
            <td>
                {{ $p->stock }}
                @if($p->stock <= $p->minimum_stock)
                    <span class="badge">Stok rendah</span>
                @endif
            </td>
            <td>
                <a href="{{ route('products.show', $p) }}">Detail</a>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('products.edit', $p) }}">Ubah</a>
                    <form method="POST" action="{{ route('products.destroy', $p) }}" style="display:inline" onsubmit="return confirm('Hapus produk ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="link-danger">Hapus</button>
                    </form>
                @endif
            </td>
        </tr>
    @empty
        <tr><td colspan="6">Tidak ada produk.</td></tr>
    @endforelse
</table>
{{ $products->links() }}
@endsection

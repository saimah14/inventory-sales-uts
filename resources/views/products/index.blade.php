@extends('layouts.app')
@section('title', 'Produk')
@section('content')
@php $icons = ['Makanan' => '&#127836;', 'Minuman' => '&#129380;', 'Peralatan Rumah' => '&#129533;']; @endphp
<div class="page-head">
    <h1>{{ auth()->user()->isBuyer() ? 'Katalog Produk' : 'Produk' }}</h1>
    @if(auth()->user()->isAdmin())
        <a class="btn" href="{{ route('products.create') }}">+ Tambah Produk</a>
    @endif
</div>
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
<div class="product-grid">
    @forelse($products as $p)
        <div class="product-card">
            <div class="thumb">
                @if($p->image)
                    <img src="{{ asset($p->image) }}" alt="{{ $p->name }}">
                @else
                    <span class="emoji">{!! $icons[$p->category->name] ?? '&#128230;' !!}</span>
                @endif
            </div>
            <div class="body">
                <div class="cat">{{ $p->category->name }}</div>
                <h3>{{ $p->name }}</h3>
                <div class="sku">{{ $p->sku }}</div>
                <div class="price">Rp {{ number_format($p->selling_price, 0, ',', '.') }}</div>
                <div>
                    Stok: <strong>{{ $p->stock }}</strong>
                    @if($p->stock <= $p->minimum_stock)
                        <span class="badge">Stok rendah</span>
                    @endif
                </div>
                <div class="actions">
                    <a href="{{ route('products.show', $p) }}">Detail</a>
                    @if(auth()->user()->isBuyer() && $p->stock > 0)
                        <form method="POST" action="{{ route('cart.add') }}" style="display:inline">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $p->id }}">
                            <input type="hidden" name="quantity" value="1">
                            <button class="btn" style="padding:5px 10px">+ Keranjang</button>
                        </form>
                    @endif
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('products.edit', $p) }}">Ubah</a>
                        <form method="POST" action="{{ route('products.destroy', $p) }}" style="display:inline" onsubmit="return confirm('Hapus produk ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="link-danger">Hapus</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <p>Tidak ada produk.</p>
    @endforelse
</div>
{{ $products->links() }}
@endsection


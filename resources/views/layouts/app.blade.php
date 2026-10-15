<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Inventory & Sales')</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f4f6f8; color: #222; }
        nav { background: #1f2d3d; color: #fff; padding: 12px 24px; display: flex; gap: 16px; align-items: center; flex-wrap: wrap; }
        nav a { color: #fff; text-decoration: none; }
        nav .spacer { flex: 1; }
        nav button { background: #c0392b; color: #fff; border: 0; padding: 6px 12px; border-radius: 4px; cursor: pointer; }
        .container { max-width: 1000px; margin: 24px auto; padding: 0 16px; }
        .cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; }
        .card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,.1); }
        .card h2 { margin: 0; font-size: 28px; }
        .card p { margin: 4px 0 0; color: #666; }
        table { width: 100%; background: #fff; border-collapse: collapse; }
        th, td { padding: 10px; border-bottom: 1px solid #eee; text-align: left; }
        .alert { background: #fdecea; color: #b71c1c; padding: 10px; border-radius: 4px; margin-bottom: 12px; }
        .success { background: #e8f5e9; color: #1b5e20; padding: 10px; border-radius: 4px; margin-bottom: 12px; }
        .btn { background: #2e7d32; color: #fff; padding: 8px 14px; border: 0; border-radius: 4px; text-decoration: none; cursor: pointer; display: inline-block; font-size: 14px; }
        .link-danger { background: none; border: 0; color: #c0392b; cursor: pointer; padding: 0; font-size: 14px; }
        .badge { background: #fff3cd; color: #8a6d00; padding: 2px 8px; border-radius: 10px; font-size: 12px; }
        input, select { padding: 8px; width: 100%; max-width: 360px; box-sizing: border-box; }
        input[type=checkbox] { width: auto; }
        .filters { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; margin-bottom: 12px; }
        .filters input, .filters select { width: auto; }
        .pagination { display: flex; list-style: none; gap: 4px; padding: 0; margin: 12px 0; }
        .page-link { padding: 6px 10px; border: 1px solid #ccc; background: #fff; color: #222; text-decoration: none; display: block; }
        .active .page-link { background: #1f2d3d; color: #fff; }
        .disabled .page-link { color: #aaa; }
    </style>
</head>
<body>
@auth
    <nav>
        <strong>Inventory & Sales</strong>
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <a href="{{ route('products.index') }}">Produk</a>
        <a href="{{ route('products.index', ['low' => 1]) }}">Stok Rendah</a>
        <a href="{{ route('purchases.index') }}">Pembelian</a>
        <a href="{{ route('sales.index') }}">Penjualan</a>
        @if(auth()->user()->isAdmin())
            <a href="{{ route('categories.index') }}">Kategori</a>
            <a href="{{ route('suppliers.index') }}">Supplier</a>
            <a href="{{ route('users.index') }}">Pengguna</a>
        @endif
        <span class="spacer"></span>
        <span>{{ auth()->user()->name }} ({{ auth()->user()->role }})</span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </nav>
@endauth
<div class="container">
    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert">{{ session('error') }}</div>
    @endif
    @yield('content')
</div>
</body>
</html>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Mini Market') - Mini Market</title>
    <style>
        :root { --primary:#4f46e5; --primary-dark:#3730a3; --dark:#1e1b4b; --bg:#f4f5fb; --text:#1f2937; --muted:#6b7280; --line:#e5e7eb; }
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; margin: 0; background: var(--bg); color: var(--text); line-height: 1.5; }
        body.guest { background: linear-gradient(135deg, #4f46e5, #7c3aed 55%, #db2777); min-height: 100vh; }
        a { color: var(--primary); }
        h1 { font-size: 24px; margin: 0 0 14px; }
        h2 { font-size: 17px; margin: 0 0 12px; }
        .muted { color: var(--muted); }
        nav { background: var(--dark); color: #fff; padding: 10px 24px; display: flex; gap: 6px; align-items: center; flex-wrap: wrap; box-shadow: 0 2px 8px rgba(0,0,0,.25); }
        nav .brand { font-weight: 700; font-size: 18px; margin-right: 14px; }
        nav a { color: #c7d2fe; text-decoration: none; padding: 7px 12px; border-radius: 8px; font-size: 14px; }
        nav a:hover { background: rgba(255,255,255,.1); color: #fff; }
        nav a.active { background: var(--primary); color: #fff; }
        nav .spacer { flex: 1; }
        nav .who { font-size: 13px; color: #c7d2fe; margin-right: 8px; }
        nav button { background: #dc2626; color: #fff; border: 0; padding: 7px 14px; border-radius: 8px; cursor: pointer; font-size: 14px; }
        .container { max-width: 1100px; margin: 24px auto; padding: 0 16px; }
        .page-head { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; margin-bottom: 14px; }
        .page-head h1 { margin: 0; }
        .cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 16px; margin: 16px 0; }
        .card { background: #fff; padding: 18px; border-radius: 14px; box-shadow: 0 2px 10px rgba(30,27,75,.08); }
        .stat { display: flex; gap: 14px; align-items: center; text-decoration: none; color: inherit; }
        .stat .ico { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; background: #eef0ff; }
        .stat .num { font-size: 21px; font-weight: 700; line-height: 1.2; }
        .stat .lbl { color: var(--muted); font-size: 13px; }
        .grid-2 { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 16px; margin-top: 8px; }
        .panel { background: #fff; border-radius: 14px; padding: 18px; box-shadow: 0 2px 10px rgba(30,27,75,.08); }
        .panel table { box-shadow: none; }
        table { width: 100%; background: #fff; border-collapse: separate; border-spacing: 0; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 10px rgba(30,27,75,.08); }
        th { background: #eef0ff; font-size: 13px; text-align: left; padding: 11px 12px; color: var(--dark); }
        td { padding: 11px 12px; border-top: 1px solid var(--line); text-align: left; font-size: 14px; }
        tr:hover td { background: #fafaff; }
        .alert { background: #fee2e2; color: #991b1b; padding: 11px 14px; border-radius: 10px; margin-bottom: 14px; }
        .success { background: #dcfce7; color: #166534; padding: 11px 14px; border-radius: 10px; margin-bottom: 14px; }
        .btn { background: var(--primary); color: #fff; padding: 9px 16px; border: 0; border-radius: 8px; text-decoration: none; cursor: pointer; display: inline-block; font-size: 14px; font-weight: 600; }
        .btn:hover { background: var(--primary-dark); }
        .link-danger { background: none; border: 0; color: #dc2626; cursor: pointer; padding: 0; font-size: 14px; }
        .badge { background: #fef3c7; color: #92400e; padding: 2px 9px; border-radius: 999px; font-size: 12px; font-weight: 600; }
        .badge-ok { background: #dcfce7; color: #166534; }
        .badge-bad { background: #fee2e2; color: #991b1b; }
        .badge-info { background: #e0e7ff; color: #3730a3; }
        input, select { padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; width: 100%; max-width: 380px; font-size: 14px; background: #fff; }
        input:focus, select:focus { outline: 2px solid #c7d2fe; border-color: var(--primary); }
        input[type=checkbox] { width: auto; }
        label { font-size: 14px; font-weight: 600; }
        .filters { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; margin-bottom: 16px; }
        .filters input, .filters select { width: auto; }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(210px, 1fr)); gap: 16px; margin-bottom: 12px; }
        .product-card { background: #fff; border-radius: 14px; overflow: hidden; box-shadow: 0 2px 10px rgba(30,27,75,.08); display: flex; flex-direction: column; }
        .product-card .thumb { height: 150px; background: linear-gradient(135deg, #e0e7ff, #fce7f3); display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .product-card .thumb img { width: 100%; height: 100%; object-fit: cover; }
        .product-card .emoji { font-size: 64px; }
        .product-card .body { padding: 14px; display: flex; flex-direction: column; gap: 4px; flex: 1; }
        .product-card .cat { font-size: 12px; color: var(--primary); font-weight: 700; text-transform: uppercase; }
        .product-card h3 { margin: 0; font-size: 16px; }
        .product-card .sku { font-size: 12px; color: var(--muted); }
        .product-card .price { font-size: 18px; font-weight: 700; margin-top: 4px; }
        .product-card .actions { margin-top: auto; padding-top: 10px; display: flex; gap: 12px; font-size: 14px; align-items: center; }
        .detail-img { width: 260px; height: 260px; border-radius: 14px; background: linear-gradient(135deg, #e0e7ff, #fce7f3); display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .detail-img img { width: 100%; height: 100%; object-fit: cover; }
        .detail-img .emoji { font-size: 110px; }
        .login-box { max-width: 380px; margin: 60px auto; background: #fff; padding: 32px; border-radius: 18px; box-shadow: 0 10px 40px rgba(0,0,0,.25); }
        .login-box .logo { font-size: 48px; text-align: center; }
        .login-box h2 { text-align: center; margin: 4px 0; font-size: 22px; }
        .login-box p.sub { text-align: center; color: var(--muted); margin: 0 0 18px; font-size: 14px; }
        .login-box input { max-width: none; }
        .login-box .btn { width: 100%; padding: 11px; }
        .pagination { display: flex; list-style: none; gap: 4px; padding: 0; margin: 14px 0; flex-wrap: wrap; }
        .page-link { padding: 7px 12px; border: 1px solid var(--line); background: #fff; color: var(--text); text-decoration: none; display: block; border-radius: 8px; font-size: 14px; }
        .active > .page-link, .page-item.active .page-link { background: var(--primary); color: #fff; border-color: var(--primary); }
        .disabled .page-link { color: #aaa; }
        .d-sm-none { display: none !important; }
        .d-sm-flex { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; }
    </style>
</head>
<body class="{{ auth()->check() ? '' : 'guest' }}">
@auth
    @if(auth()->user()->isBuyer())
    <nav>
        <span class="brand">&#128717; Saimah Mart</span>
        <a href="{{ route('products.index') }}" class="active">Katalog Produk</a>
        <a href="{{ route('cart.index') }}">Keranjang ({{ array_sum(session('cart', [])) }})</a>
        <a href="{{ route('orders.index') }}">Pesanan Saya</a>
        <span class="spacer"></span>
        <span class="who">{{ auth()->user()->name }} <span class="badge badge-info">pembeli</span></span>
        <form method="POST" action="{{ route('logout') }}" style="margin:0">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </nav>
    @else
    <nav>
        <span class="brand">&#128230; Mini Market</span>
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
        <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') && !request('low') ? 'active' : '' }}">Produk</a>
        <a href="{{ route('products.index', ['low' => 1]) }}" class="{{ request()->routeIs('products.index') && request('low') ? 'active' : '' }}">Stok Rendah</a>
        <a href="{{ route('purchases.index') }}" class="{{ request()->routeIs('purchases.*') ? 'active' : '' }}">Pembelian</a>
        <a href="{{ route('orders.index') }}" class="{{ request()->routeIs('orders.*') ? 'active' : '' }}">Pesanan</a>
        <a href="{{ route('sales.index') }}" class="{{ request()->routeIs('sales.*') ? 'active' : '' }}">Penjualan</a>
        @if(auth()->user()->isAdmin())
            <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">Kategori</a>
            <a href="{{ route('suppliers.index') }}" class="{{ request()->routeIs('suppliers.*') ? 'active' : '' }}">Supplier</a>
            <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">Pengguna</a>
        @endif
        <span class="spacer"></span>
        <span class="who">{{ auth()->user()->name }} <span class="badge badge-info">{{ auth()->user()->role }}</span></span>
        <form method="POST" action="{{ route('logout') }}" style="margin:0">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </nav>
    @endif
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




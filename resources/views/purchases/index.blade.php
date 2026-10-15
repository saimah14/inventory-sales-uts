@extends('layouts.app')
@section('title', 'Pembelian')
@section('content')
<h1>Pembelian (Stock-in)</h1>
<p><a class="btn" href="{{ route('purchases.create') }}">+ Pembelian Baru</a></p>
<form method="GET" action="{{ route('purchases.index') }}" class="filters">
    <input name="q" value="{{ request('q') }}" placeholder="No. transaksi">
    <select name="status">
        <option value="">Semua status</option>
        <option value="completed" @selected(request('status') == 'completed')>Selesai</option>
        <option value="cancelled" @selected(request('status') == 'cancelled')>Dibatalkan</option>
    </select>
    <input type="date" name="from" value="{{ request('from') }}"> s/d
    <input type="date" name="to" value="{{ request('to') }}">
    <button class="btn">Cari</button>
    <a href="{{ route('purchases.index') }}">Reset</a>
</form>
<table>
    <tr><th>No. Transaksi</th><th>Waktu</th><th>Supplier</th><th>Total</th><th>Status</th><th></th></tr>
    @forelse($purchases as $p)
        <tr>
            <td>{{ $p->purchase_number }}</td>
            <td>{{ $p->purchased_at->format('d/m/Y H:i') }}</td>
            <td>{{ $p->supplier->name }}</td>
            <td>Rp {{ number_format($p->total, 0, ',', '.') }}</td>
            <td>{{ $p->status }}</td>
            <td><a href="{{ route('purchases.show', $p) }}">Detail</a></td>
        </tr>
    @empty
        <tr><td colspan="6">Belum ada pembelian.</td></tr>
    @endforelse
</table>
{{ $purchases->links() }}
@endsection

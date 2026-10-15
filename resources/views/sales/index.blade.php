@extends('layouts.app')
@section('title', 'Penjualan')
@section('content')
<h1>Penjualan</h1>
<p><a class="btn" href="{{ route('sales.create') }}">+ Penjualan Baru</a></p>
<form method="GET" action="{{ route('sales.index') }}" class="filters">
    <input name="q" value="{{ request('q') }}" placeholder="No. transaksi">
    <select name="status">
        <option value="">Semua status</option>
        <option value="completed" @selected(request('status') == 'completed')>Selesai</option>
        <option value="cancelled" @selected(request('status') == 'cancelled')>Dibatalkan</option>
    </select>
    <input type="date" name="from" value="{{ request('from') }}"> s/d
    <input type="date" name="to" value="{{ request('to') }}">
    <button class="btn">Cari</button>
    <a href="{{ route('sales.index') }}">Reset</a>
</form>
<table>
    <tr><th>No. Transaksi</th><th>Waktu</th><th>Kasir</th><th>Total</th><th>Status</th><th></th></tr>
    @forelse($sales as $s)
        <tr>
            <td>{{ $s->sale_number }}</td>
            <td>{{ $s->sold_at->format('d/m/Y H:i') }}</td>
            <td>{{ $s->user->name }}</td>
            <td>Rp {{ number_format($s->total, 0, ',', '.') }}</td>
            <td>{{ $s->status }}</td>
            <td><a href="{{ route('sales.show', $s) }}">Detail</a></td>
        </tr>
    @empty
        <tr><td colspan="6">Belum ada penjualan.</td></tr>
    @endforelse
</table>
{{ $sales->links() }}
@endsection

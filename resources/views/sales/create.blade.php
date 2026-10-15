@extends('layouts.app')
@section('title', 'Penjualan Baru')
@section('content')
<h1>Penjualan Baru</h1>
@include('partials.errors')
@php $rows = old('items', [['product_id' => '', 'quantity' => 1]]); @endphp
<form method="POST" action="{{ route('sales.store') }}">
    @csrf
    <table id="items">
        <tr><th>Produk</th><th>Jumlah</th><th></th></tr>
        @foreach($rows as $i => $row)
            <tr>
                <td>
                    <select name="items[{{ $i }}][product_id]" required>
                        <option value="">-- pilih produk --</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" @selected(($row['product_id'] ?? '') == $p->id)>{{ $p->name }} (stok {{ $p->stock }}, Rp {{ number_format($p->selling_price, 0, ',', '.') }})</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" min="1" name="items[{{ $i }}][quantity]" value="{{ $row['quantity'] ?? 1 }}" required></td>
                <td><button type="button" onclick="removeRow(this)">Hapus</button></td>
            </tr>
        @endforeach
    </table>
    <p><button type="button" onclick="addRow()">+ Tambah item</button></p>
    <p>Total dihitung otomatis oleh server dari harga di database.</p>
    <button class="btn">Simpan Penjualan</button>
    <a href="{{ route('sales.index') }}">Batal</a>
</form>
<script>
let idx = {{ max(array_keys($rows)) + 1 }};
function addRow() {
    const t = document.getElementById('items');
    const rows = t.querySelectorAll('tr');
    const clone = rows[rows.length - 1].cloneNode(true);
    clone.innerHTML = clone.innerHTML.replace(/items\[\d+\]/g, 'items[' + idx + ']');
    idx++;
    clone.querySelectorAll('select').forEach(s => s.value = '');
    clone.querySelectorAll('input').forEach(i => i.value = 1);
    t.appendChild(clone);
}
function removeRow(b) {
    const t = document.getElementById('items');
    if (t.querySelectorAll('tr').length > 2) b.closest('tr').remove();
}
</script>
@endsection

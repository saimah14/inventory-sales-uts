@extends('layouts.app')
@section('title', 'Pembelian Baru')
@section('content')
<h1>Pembelian Baru</h1>
@include('partials.errors')
@php $rows = old('items', [['product_id' => '', 'quantity' => 1, 'unit_cost' => '']]); @endphp
<form method="POST" action="{{ route('purchases.store') }}">
    @csrf
    <p><label>Supplier<br>
        <select name="supplier_id" required>
            <option value="">-- pilih supplier --</option>
            @foreach($suppliers as $s)
                <option value="{{ $s->id }}" @selected(old('supplier_id') == $s->id)>{{ $s->name }}</option>
            @endforeach
        </select></label></p>
    <table id="items">
        <tr><th>Produk</th><th>Jumlah</th><th>Harga beli satuan</th><th></th></tr>
        @foreach($rows as $i => $row)
            <tr>
                <td>
                    <select name="items[{{ $i }}][product_id]" onchange="fillCost(this)" required>
                        <option value="">-- pilih produk --</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" data-cost="{{ $p->purchase_price }}" @selected(($row['product_id'] ?? '') == $p->id)>{{ $p->name }} (stok {{ $p->stock }})</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" min="1" name="items[{{ $i }}][quantity]" value="{{ $row['quantity'] ?? 1 }}" required></td>
                <td><input class="cost" type="number" step="0.01" min="0" name="items[{{ $i }}][unit_cost]" value="{{ $row['unit_cost'] ?? '' }}" required></td>
                <td><button type="button" onclick="removeRow(this)">Hapus</button></td>
            </tr>
        @endforeach
    </table>
    <p><button type="button" onclick="addRow()">+ Tambah item</button></p>
    <p>Stok bertambah otomatis setelah transaksi berhasil disimpan.</p>
    <button class="btn">Simpan Pembelian</button>
    <a href="{{ route('purchases.index') }}">Batal</a>
</form>
<script>
let idx = {{ max(array_keys($rows)) + 1 }};
function fillCost(s) {
    const o = s.selectedOptions[0];
    s.closest('tr').querySelector('.cost').value = (o && o.dataset.cost) ? o.dataset.cost : '';
}
function addRow() {
    const t = document.getElementById('items');
    const rows = t.querySelectorAll('tr');
    const clone = rows[rows.length - 1].cloneNode(true);
    clone.innerHTML = clone.innerHTML.replace(/items\[\d+\]/g, 'items[' + idx + ']');
    idx++;
    clone.querySelectorAll('select').forEach(s => s.value = '');
    clone.querySelectorAll('input').forEach(i => i.value = i.name.includes('quantity') ? 1 : '');
    t.appendChild(clone);
}
function removeRow(b) {
    const t = document.getElementById('items');
    if (t.querySelectorAll('tr').length > 2) b.closest('tr').remove();
}
</script>
@endsection

@extends('layouts.app')
@section('title', 'Supplier')
@section('content')
<h1>Supplier</h1>
<p><a class="btn" href="{{ route('suppliers.create') }}">+ Tambah Supplier</a></p>
<table>
    <tr><th>Nama</th><th>Telepon</th><th>Email</th><th>Aksi</th></tr>
    @forelse($suppliers as $s)
        <tr>
            <td>{{ $s->name }}</td>
            <td>{{ $s->phone }}</td>
            <td>{{ $s->email }}</td>
            <td>
                <a href="{{ route('suppliers.edit', $s) }}">Ubah</a>
                <form method="POST" action="{{ route('suppliers.destroy', $s) }}" style="display:inline" onsubmit="return confirm('Hapus supplier ini?')">
                    @csrf
                    @method('DELETE')
                    <button class="link-danger">Hapus</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="4">Belum ada supplier.</td></tr>
    @endforelse
</table>
{{ $suppliers->links() }}
@endsection

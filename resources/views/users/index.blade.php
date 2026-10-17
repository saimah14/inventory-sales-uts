@extends('layouts.app')
@section('title', 'Pengguna')
@section('content')
<div class="page-head">
    <h1>Pengguna</h1>
    <a class="btn" href="{{ route('users.create') }}">+ Tambah Pengguna</a>
</div>
<form method="GET" action="{{ route('users.index') }}" class="filters">
    <input name="q" value="{{ request('q') }}" placeholder="Cari nama / email">
    <select name="role">
        <option value="">Semua role</option>
        <option value="admin" @selected(request('role') == 'admin')>Admin</option>
        <option value="staff" @selected(request('role') == 'staff')>Staff</option>
        <option value="pembeli" @selected(request('role') == 'pembeli')>Pembeli</option>
    </select>
    <button class="btn">Cari</button>
    <a href="{{ route('users.index') }}">Reset</a>
</form>
<table>
    <tr><th>Nama</th><th>Email</th><th>Role</th><th>Aksi</th></tr>
    @forelse($users as $u)
        <tr>
            <td>{{ $u->name }} @if($u->id == auth()->id()) <span class="badge">Anda</span> @endif</td>
            <td>{{ $u->email }}</td>
            <td><span class="badge {{ $u->role === 'admin' ? 'badge-info' : 'badge-ok' }}">{{ $u->role }}</span></td>
            <td>
                <a href="{{ route('users.edit', $u) }}">Ubah</a>
                @if($u->id != auth()->id())
                    <form method="POST" action="{{ route('users.destroy', $u) }}" style="display:inline" onsubmit="return confirm('Hapus pengguna ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="link-danger">Hapus</button>
                    </form>
                @endif
            </td>
        </tr>
    @empty
        <tr><td colspan="4">Tidak ada pengguna.</td></tr>
    @endforelse
</table>
{{ $users->links() }}
@endsection


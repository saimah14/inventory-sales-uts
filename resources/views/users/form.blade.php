@extends('layouts.app')
@section('title', 'Form Pengguna')
@section('content')
<h1>{{ $user->exists ? 'Ubah' : 'Tambah' }} Pengguna</h1>
@include('partials.errors')
<form method="POST" action="{{ $user->exists ? route('users.update', $user) : route('users.store') }}">
    @csrf
    @if($user->exists)
        @method('PUT')
    @endif
    <p><label>Nama<br><input name="name" value="{{ old('name', $user->name) }}" required></label></p>
    <p><label>Email<br><input type="email" name="email" value="{{ old('email', $user->email) }}" required></label></p>
    <p><label>Role<br>
        <select name="role" required>
            <option value="staff" @selected(old('role', $user->role) === 'staff')>Staff</option>
            <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
            <option value="pembeli" @selected(old('role', $user->role) === 'pembeli')>Pembeli</option>
        </select></label></p>
    <p><label>Password (minimal 8 karakter){{ $user->exists ? ' - kosongkan jika tidak diganti' : '' }}<br>
        <input type="password" name="password" @if(!$user->exists) required @endif></label></p>
    <p><label>Ulangi password<br>
        <input type="password" name="password_confirmation" @if(!$user->exists) required @endif></label></p>
    <button class="btn">Simpan</button>
    <a href="{{ route('users.index') }}">Batal</a>
</form>
@endsection


@extends('layouts.app')
@section('title', 'Form Supplier')
@section('content')
<h1>{{ $supplier->exists ? 'Ubah' : 'Tambah' }} Supplier</h1>
@include('partials.errors')
<form method="POST" action="{{ $supplier->exists ? route('suppliers.update', $supplier) : route('suppliers.store') }}">
    @csrf
    @if($supplier->exists)
        @method('PUT')
    @endif
    <p><label>Nama<br><input name="name" value="{{ old('name', $supplier->name) }}" required></label></p>
    <p><label>Telepon<br><input name="phone" value="{{ old('phone', $supplier->phone) }}"></label></p>
    <p><label>Email<br><input name="email" value="{{ old('email', $supplier->email) }}"></label></p>
    <button class="btn">Simpan</button>
    <a href="{{ route('suppliers.index') }}">Batal</a>
</form>
@endsection

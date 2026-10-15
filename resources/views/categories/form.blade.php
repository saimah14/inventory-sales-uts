@extends('layouts.app')
@section('title', 'Form Kategori')
@section('content')
<h1>{{ $category->exists ? 'Ubah' : 'Tambah' }} Kategori</h1>
@include('partials.errors')
<form method="POST" action="{{ $category->exists ? route('categories.update', $category) : route('categories.store') }}">
    @csrf
    @if($category->exists)
        @method('PUT')
    @endif
    <p><label>Nama<br><input name="name" value="{{ old('name', $category->name) }}" required></label></p>
    <button class="btn">Simpan</button>
    <a href="{{ route('categories.index') }}">Batal</a>
</form>
@endsection

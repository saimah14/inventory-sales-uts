@extends('layouts.app')
@section('title', 'Pengguna')
@section('content')
<h1>Manajemen Pengguna (Admin)</h1>
<table>
    <tr><th>Nama</th><th>Email</th><th>Role</th></tr>
    @foreach($users as $u)
        <tr><td>{{ $u->name }}</td><td>{{ $u->email }}</td><td>{{ $u->role }}</td></tr>
    @endforeach
</table>
@endsection

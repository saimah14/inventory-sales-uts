@extends('layouts.app')
@section('title', 'Login')
@section('content')
<div class="card" style="max-width:380px;margin:60px auto;">
    <h2>Login</h2>
    @if($errors->any())
        <div class="alert">{{ $errors->first() }}</div>
    @endif
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <p><label>Email<br><input type="email" name="email" value="{{ old('email') }}" required style="width:100%;padding:8px;"></label></p>
        <p><label>Password<br><input type="password" name="password" required style="width:100%;padding:8px;"></label></p>
        <button type="submit" style="padding:8px 16px;">Masuk</button>
    </form>
</div>
@endsection

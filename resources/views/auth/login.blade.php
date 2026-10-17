@extends('layouts.app')
@section('title', 'Login')
@section('content')
<style>
    .auth-wrap { display: grid; grid-template-columns: 1.1fr 1fr; max-width: 900px; margin: 30px auto; background: #fff; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,.25); }
    .auth-left { background: linear-gradient(160deg, #eef2ff, #fce7f3); padding: 28px 22px; text-align: center; }
    .auth-left svg { width: 100%; max-width: 380px; height: auto; }
    .auth-left h2 { margin: 6px 0 4px; font-size: 24px; color: #1e1b4b; }
    .auth-left p { margin: 0 0 12px; color: #6b7280; font-size: 14px; }
    .auth-left ul { list-style: none; padding: 0; margin: 0; text-align: left; display: inline-block; font-size: 14px; color: #374151; }
    .auth-left li { margin: 6px 0; }
    .auth-right { padding: 40px 34px; display: flex; flex-direction: column; justify-content: center; }
    .auth-right h1 { margin: 0 0 4px; font-size: 26px; }
    .auth-right .sub { color: #6b7280; margin: 0 0 18px; font-size: 14px; }
    .auth-right input { max-width: none; }
    .auth-right .btn { width: 100%; padding: 12px; font-size: 15px; }
    @media (max-width: 700px) { .auth-wrap { grid-template-columns: 1fr; } .auth-right { padding: 28px 22px; } }
</style>
<div class="auth-wrap">
    <div class="auth-left">
        <svg viewBox="0 0 400 290" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Ilustrasi Mini Market">
            <circle cx="200" cy="150" r="130" fill="#ffffff" opacity="0.6"/>
            <rect x="40" y="258" width="320" height="10" rx="5" fill="#cbd5e1"/>
            <rect x="70" y="108" width="260" height="152" fill="#fef3c7"/>
            <rect x="90" y="138" width="95" height="84" rx="6" fill="#e0e7ff" stroke="#4f46e5" stroke-width="3"/>
            <line x1="90" y1="180" x2="185" y2="180" stroke="#4f46e5" stroke-width="3"/>
            <rect x="98" y="152" width="14" height="26" rx="3" fill="#ef4444"/>
            <rect x="116" y="158" width="14" height="20" rx="3" fill="#f59e0b"/>
            <rect x="134" y="150" width="14" height="28" rx="3" fill="#10b981"/>
            <rect x="152" y="156" width="14" height="22" rx="3" fill="#3b82f6"/>
            <rect x="168" y="160" width="12" height="18" rx="3" fill="#ec4899"/>
            <rect x="98" y="192" width="18" height="28" rx="3" fill="#3b82f6"/>
            <rect x="120" y="198" width="18" height="22" rx="3" fill="#ef4444"/>
            <rect x="142" y="194" width="18" height="26" rx="3" fill="#f59e0b"/>
            <rect x="164" y="200" width="16" height="20" rx="3" fill="#10b981"/>
            <rect x="225" y="138" width="75" height="122" rx="6" fill="#4f46e5"/>
            <rect x="233" y="148" width="59" height="60" rx="4" fill="#c7d2fe"/>
            <circle cx="285" cy="232" r="4" fill="#fde68a"/>
            <rect x="60" y="68" width="40" height="42" fill="#ef4444"/>
            <rect x="100" y="68" width="40" height="42" fill="#ffffff"/>
            <rect x="140" y="68" width="40" height="42" fill="#ef4444"/>
            <rect x="180" y="68" width="40" height="42" fill="#ffffff"/>
            <rect x="220" y="68" width="40" height="42" fill="#ef4444"/>
            <rect x="260" y="68" width="40" height="42" fill="#ffffff"/>
            <rect x="300" y="68" width="40" height="42" fill="#ef4444"/>
            <circle cx="80" cy="110" r="20" fill="#ef4444"/>
            <circle cx="120" cy="110" r="20" fill="#ffffff"/>
            <circle cx="160" cy="110" r="20" fill="#ef4444"/>
            <circle cx="200" cy="110" r="20" fill="#ffffff"/>
            <circle cx="240" cy="110" r="20" fill="#ef4444"/>
            <circle cx="280" cy="110" r="20" fill="#ffffff"/>
            <circle cx="320" cy="110" r="20" fill="#ef4444"/>
            <rect x="88" y="22" width="224" height="38" rx="10" fill="#1e1b4b"/>
            <text x="200" y="48" text-anchor="middle" font-family="Segoe UI, Arial, sans-serif" font-size="21" font-weight="700" fill="#ffffff" letter-spacing="2">MINI MARKET</text>
            <circle cx="345" cy="236" r="9" fill="#ef4444"/>
            <rect x="343" y="222" width="4" height="8" fill="#16a34a"/>
            <circle cx="58" cy="240" r="9" fill="#f59e0b"/>
        </svg>
        <h2>Mini Market</h2>
        <p>Belanja kebutuhan harian jadi lebih mudah</p>
        <ul>
            <li>&#128722; Pilih produk, masuk keranjang</li>
            <li>&#9989; Pesanan diproses staff toko</li>
            <li>&#128230; Stok selalu terupdate</li>
        </ul>
    </div>
    <div class="auth-right">
        <h1>Selamat datang</h1>
        <p class="sub">Silakan login untuk melanjutkan</p>
        @if($errors->any())
            <div class="alert">{{ $errors->first() }}</div>
        @endif
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <p><label>Email<br><input type="email" name="email" value="{{ old('email') }}" required autofocus></label></p>
            <p><label>Password<br><input type="password" name="password" required></label></p>
            <button type="submit" class="btn">Masuk</button>
        </form>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', $title ?? 'Kontak')

@section('content')
    <h2>{{ $title ?? 'Kontak' }}</h2>
    <ul>
        <li>Email: <a href="mailto:hello@contoh.com">hello@contoh.com</a></li>
        <li>WhatsApp: 08xx-xxxx-xxxx</li>
    </ul>
    
    @php
        $now = now()->format('d M Y H:i');
    @endphp
    <p><em>Diakses pada: {{ $now }}</em></p>
@endsection
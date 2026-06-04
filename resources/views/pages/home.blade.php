@extends('layouts.app')

@section('title', $title ?? 'Beranda')

@section('content')
    @include('partials.alert', ['message' => 'Semua latihan mandiri berhasil diselesaikan!'])

    <h2>{{ $title ?? 'Beranda' }}</h2>
    <p>{{ $tagline ?? 'Selamat datang!' }}</p>
    <p>Ini contoh halaman yang datanya dikirim dari Controller.</p>
@endsection
@extends('layouts.app')

@section('title', $judul)

@section('content')
    <h2>{{ $judul }}</h2>
    <p>Ini adalah halaman artikel dinamis.</p>
    <p><em>URL Slug asli: {{ request()->route('slug') }}</em></p>
@endsection
@extends('layouts.app')

@section('title', $title ?? 'Tentang')

@section('content')
    <h2>{{ $title ?? 'Tentang Kami' }}</h2>
    <p>{{ $content ?? 'Deskripsi singkat.' }}</p>
@endsection
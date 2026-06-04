<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Belajar Laravel')</title>
    <link rel="stylesheet" href="https://unpkg.com/sakura.css/css/sakura.css">
</head>
<body>
    <header>
        <h1>Belajar Laravel</h1>
        <nav>
            <a href="{{ route('home') }}" style="{{ request()->routeIs('home') ? 'font-weight: bold; border-bottom: 2px solid;' : '' }}">Beranda</a> | 
            <a href="{{ route('about') }}" style="{{ request()->routeIs('about') ? 'font-weight: bold; border-bottom: 2px solid;' : '' }}">Tentang</a> | 
            <a href="{{ route('contact') }}" style="{{ request()->routeIs('contact') ? 'font-weight: bold; border-bottom: 2px solid;' : '' }}">Kontak</a> | 
            <a href="{{ route('services') }}" style="{{ request()->routeIs('services') ? 'font-weight: bold; border-bottom: 2px solid;' : '' }}">Layanan</a>
        </nav>
        <hr>
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        <hr>
        <small>&copy; {{ date('Y') }} Belajar Laravel</small>
    </footer>
</body>
</html>
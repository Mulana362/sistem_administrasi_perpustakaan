<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Perpustakaan SMPN 1 Bandung')</title>

    {{-- MATIKAN ALERT NATIVE (GA NGUBAH UI) --}}
    <script>
    (function () {
        const noop = function () {};
        window.alert = noop;
        window.confirm = function () { return true; };
        window.prompt = function () { return null; };
    })();
    </script>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom Style -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    {{-- optional: kalau ada halaman push CSS --}}
    @stack('styles')
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ url('/') }}">Perpustakaan SMPN 1 Bandung</a>
    </div>
</nav>

<div class="container mt-4">
    @yield('content')
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

{{-- WAJIB: render semua script dari view (auto-fill NIS ada di sini) --}}
@stack('scripts')

</body>
</html>

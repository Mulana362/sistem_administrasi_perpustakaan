<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Admin – Perpustakaan SMPN 1 Bandung</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- CSRF token untuk keamanan --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: system-ui, sans-serif;
            min-height: 100vh;
            background: radial-gradient(circle at top left, #2563eb 0, #020617 55%, #020617 100%);
            color: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-shell {
            width: 100%;
            max-width: 1100px;
            border-radius: 24px;
            background: radial-gradient(circle at top left, #1d4ed8 0, #020617 55%);
            box-shadow:
                0 30px 80px rgba(15,23,42,.85),
                0 0 0 1px rgba(148,163,184,.08);
            display: grid;
            grid-template-columns: 1.1fr 1fr;
            overflow: hidden;
        }

        /* kiri */
        .login-left {
            padding: 32px 40px;
            background: radial-gradient(circle at top, #0b1120 0, #020617 52%);
        }

        .pill-role {
            display: inline-flex;
            gap: .4rem;
            padding: 6px 14px;
            border-radius: 999px;
            border: 1px solid rgba(59,130,246,.5);
            font-size: .78rem;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #bfdbfe;
            margin-bottom: 24px;
        }

        .dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: #22c55e;
        }

        .login-title { font-size: 2rem; font-weight: 700; margin-bottom: 4px; }
        .login-sub { color: #9ca3af; margin-bottom: 26px; }

        .form-label { font-size: .85rem; font-weight: 600; margin-bottom: 6px; }
        .form-control {
            width: 100%;
            background: #020617;
            border-radius: 999px;
            border: 1px solid #1f2937;
            padding: 11px 16px;
            color: #e5e7eb;
            font-size: .9rem;
        }
        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 1px rgba(59,130,246,.8);
        }

        .btn-login {
            width: 100%;
            border: none;
            border-radius: 999px;
            padding: 11px;
            margin-top: 6px;
            font-weight: 600;
            background: linear-gradient(135deg,#2563eb,#1d4ed8);
            color: white;
            box-shadow: 0 18px 40px rgba(37,99,235,.55);
            cursor: pointer;
        }
        .btn-login:hover { filter: brightness(1.08); }

        .alert-error {
            background: rgba(248,113,113,.12);
            border: 1px solid rgba(248,113,113,.6);
            color: #fecaca;
            border-radius: 10px;
            padding: 8px 10px;
            margin-bottom: 14px;
        }

        .back-link {
            margin-top: 24px;
            display: inline-block;
            color: #9ca3af;
            font-size: .8rem;
            text-decoration: none;
        }

        /* kanan */
        .login-right {
            padding: 32px;
            background: radial-gradient(circle at top left,#111827 0,#020617 55%);
            border-left: 1px solid rgba(148,163,184,.18);
        }

        .badge-info {
            display: inline-flex;
            gap: .4rem;
            padding: 6px 14px;
            border-radius: 999px;
            background: rgba(15,23,42,.9);
            border: 1px solid rgba(148,163,184,.45);
            margin-bottom: 20px;
            font-size: .78rem;
        }

        .right-title { font-size: 1.25rem; font-weight: 700; margin-bottom: 6px; }
        .right-sub { color: #9ca3af; margin-bottom: 20px; }

        .mini-card {
            background: radial-gradient(circle at top left,#0f172a 0,#020617 75%);
            border-radius: 18px;
            border: 1px solid rgba(31,41,55,.9);
            padding: 14px;
            margin-bottom: 12px;
        }

        .mini-row { display: flex; justify-content: space-between; margin-bottom: 8px; }
        .mini-pill {
            padding: 4px 10px;
            border-radius: 999px;
            background: rgba(15,23,42,.9);
            border: 1px solid rgba(55,65,81,.9);
            font-size: .75rem;
        }

        @media (max-width: 900px) {
            .login-shell { grid-template-columns: 1fr; max-width: 480px; }
            .login-right { display: none; }
        }
    </style>
</head>
<body>

<div class="login-shell">

    {{-- FORM LOGIN --}}
    <div class="login-left">

        <div class="pill-role">
            <span class="dot"></span> Admin Perpustakaan
        </div>

        <h1 class="login-title">Login Admin</h1>
        <p class="login-sub">Perpustakaan SMPN 1 Bandung</p>

        @if (session('error'))
            <div class="alert-error">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Email Admin</label>
                <input type="email" id="email" name="email" class="form-control"
                       placeholder="admin@sekolah.sch.id" required autofocus>
                @error('email')
                    <div class="text-danger" style="font-size:.8rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password Admin</label>
                <input type="password" id="password" name="password" class="form-control"
                       placeholder="Masukkan password" required>
                @error('password')
                    <div class="text-danger" style="font-size:.8rem;">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-login">Login</button>

            <a href="{{ route('home') }}" class="back-link">← Kembali ke halaman utama</a>
        </form>
    </div>

    {{-- PANEL KANAN --}}
    <div class="login-right">

        <div class="badge-info">📊 Sistem Informasi Perpustakaan</div>

        <div class="right-title">Kelola perpustakaan sekolah dengan mudah.</div>

        <div class="right-sub">
            Admin dapat mengelola daftar buku, anggota, peminjaman, pengembalian,
            dan memantau kunjungan perpustakaan.
        </div>

        <div class="mini-card">
            <div class="mini-row">
                <span>Total Koleksi Buku</span>
                <span class="mini-pill">± 2702</span>
            </div>
            <div class="mini-row">
                <span>Anggota Terdaftar</span>
                <span class="mini-pill">Siswa & Guru</span>
            </div>
            <div class="mini-row">
                <span>Aktivitas Bulanan</span>
                <span class="mini-pill">Realtime</span>
            </div>
        </div>

        <div class="right-footer">
            Akses hanya untuk admin yang berwenang.
        </div>
    </div>

</div>

</body>
</html>

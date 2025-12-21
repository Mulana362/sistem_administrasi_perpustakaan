<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Perpustakaan SMPN 1 Bandung</title>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap');

        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            overflow: hidden;
            background: #0f172a;
            color: white;
        }

        /* BACKGROUND CINEMATIC */
        .bg {
            position: absolute;
            inset: 0;
            background-image: url('{{ asset("images/gambar.jpg") }}');
            background-size: cover;
            background-position: center;
            filter: brightness(45%) blur(2px);
            z-index: -2;
        }

        /* GLOW EFFECT MODERN */
        .glow-layer {
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 20% 30%, rgba(250, 204, 21, .35), transparent 60%),
                radial-gradient(circle at 80% 75%, rgba(59, 130, 246, .35), transparent 55%);
            z-index: -1;
            pointer-events: none;
        }

        .content {
            position: absolute;
            top: 52%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            width: 90%;
            max-width: 680px;
            padding: 0 12px;
        }

        .content h1 {
            font-size: 40px;
            font-weight: 800;
            letter-spacing: 1.5px;
            text-shadow: 0 4px 22px rgba(0,0,0,0.7);
            margin-bottom: 6px;
        }

        .content h2 {
            font-size: 20px;
            opacity: .85;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .content p {
            font-size: 15px;
            opacity: 0.92;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .role-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 2px;
            opacity: 0.82;
            margin-bottom: 6px;
        }

        /* BUTTON GLASS + PREMIUM MODE */
        .btn {
            display: block;
            padding: 13px 24px;
            margin: 10px auto;
            width: 80%;
            max-width: 380px;
            border-radius: 999px;
            font-weight: 600;
            text-decoration: none;
            backdrop-filter: blur(10px);
            transition: .25s;
            font-size: 15px;
        }

        /* BUTTON ADMIN GOLD */
        .btn-primary {
            background: linear-gradient(135deg, #facc15, #eab308);
            color: #111827;
            border: none;
            box-shadow:
                0 8px 25px rgba(250, 204, 21, 0.45),
                0 0 0 1px rgba(120, 90, 8, 0.6);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #fbbf24, #d97706);
            transform: translateY(-2px);
        }

        /* BUTTON SISWA GLASS */
        .btn-secondary {
            background: rgba(255, 255, 255, 0.15);
            color: #f9fafb;
            border: 2px solid rgba(255, 255, 255, 0.35);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.28);
            transform: translateY(-2px);
        }

        .footer {
            position: absolute;
            bottom: 18px;
            width: 100%;
            text-align: center;
            color: #e5e7eb;
            font-size: 12px;
            opacity: 0.75;
        }
    </style>
</head>
<body>

<div class="bg"></div>
<div class="glow-layer"></div>

<div class="content">
    <h1>Sistem Informasi Perpustakaan</h1>
    <h2>SMPN 1 Bandung</h2>

    <p>
    Selamat datang di Sistem Informasi Perpustakaan SMPN 1 Bandung.
    Aplikasi ini membantu petugas dalam mengelola data perpustakaan,
    serta memudahkan siswa menemukan buku yang dibutuhkan melalui katalog online.
    </p>

    {{-- MODUL ADMIN --}}
    <div class="role-label">Modul Admin</div>
    <a href="{{ route('admin.login') }}" class="btn btn-primary">
        Login Admin (Petugas Perpustakaan)
    </a>

    {{-- LAYANAN SISWA --}}
    <div class="role-label" style="margin-top: 20px;">Layanan Siswa</div>
    <a href="{{ route('catalog') }}" class="btn btn-secondary">
        Lihat Katalog Buku Perpustakaan
    </a>
</div>

<div class="footer">
    © {{ date('Y') }} Sistem Informasi Perpustakaan SMPN 1 Bandung
</div>

</body>
</html>

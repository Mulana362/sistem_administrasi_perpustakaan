<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu Anggota - {{ $member->name }}</title>
    <style>
        @page {
            size: A4;
            margin: 20mm;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f3f4f6;
        }

        .card-wrapper {
            width: 340px;              /* kira-kira 85 x 53 mm */
            height: 210px;
            border-radius: 14px;
            border: 1px solid #d1d5db;
            background: linear-gradient(135deg, #1e3a8a, #2563eb);
            color: #ffffff;
            padding: 14px 16px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 12px 35px rgba(15,23,42,0.40);
            margin: 40px auto;
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-logo {
            width: 46px;
            height: 46px;
            border-radius: 999px;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            color: #111827;
            overflow: hidden;
        }

        .card-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .card-title {
            font-size: 13px;
            font-weight: 600;
            line-height: 1.3;
        }

        .card-sub {
            font-size: 11px;
            opacity: .9;
        }

        .card-body {
            margin-top: 8px;
            font-size: 12px;
        }

        .data-row {
            display: grid;
            grid-template-columns: 80px 4px 1fr;
            gap: 2px;
            margin-bottom: 4px;
        }

        .data-label {
            opacity: .85;
        }

        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            font-size: 10px;
            margin-top: 8px;
        }

        .barcode-box {
            width: 120px;
            height: 26px;
            border-radius: 6px;
            background: rgba(15,23,42,0.75);
            display: flex;
            align-items: center;
            justify-content: center;
            letter-spacing: .18em;
            font-size: 10px;
        }

        .valid-text {
            font-size: 10px;
            opacity: .9;
        }

        .btn-print-area {
            text-align: center;
            margin-top: 12px;
        }

        .btn-print {
            padding: 8px 16px;
            border-radius: 999px;
            border: none;
            background: #111827;
            color: #ffffff;
            cursor: pointer;
            font-size: 13px;
        }

        @media print {
            .btn-print-area {
                display: none;
            }

            body {
                background: #ffffff;
            }

            .card-wrapper {
                margin: 0 auto;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>

<div class="card-wrapper">
    <div class="card-header">
        <div class="card-logo">
            {{-- LOGO SEKOLAH --}}
            @php
                $logoPath = public_path('images/logo-sekolah.png');
            @endphp

            @if (file_exists($logoPath))
                <img src="{{ asset('images/logo-sekolah.png') }}" alt="Logo Sekolah">
            @else
                LOGO
            @endif
        </div>

        <div>
            <div class="card-title">KARTU ANGGOTA PERPUSTAKAAN</div>
            <div class="card-sub">SMP NEGERI 1 BANDUNG</div>
        </div>
    </div>

    <div class="card-body">
        <div class="data-row">
            <div class="data-label">Nama</div>
            <div>:</div>
            <div>{{ $member->name }}</div>
        </div>
        <div class="data-row">
            <div class="data-label">NIS</div>
            <div>:</div>
            <div>{{ $member->nis }}</div>
        </div>
        <div class="data-row">
            <div class="data-label">Kelas</div>
            <div>:</div>
            <div>{{ $member->class }}</div>
        </div>
    </div>

    <div class="card-footer">
        <div>
            <div class="valid-text">
                Kartu ini digunakan untuk peminjaman buku di perpustakaan sekolah.
            </div>
            <div class="valid-text">
                Berlaku selama siswa terdaftar aktif pada tahun pelajaran berjalan.
            </div>
        </div>
        <div class="barcode-box">
            {{ $member->nis }}
        </div>
    </div>
</div>

<div class="btn-print-area">
    <button class="btn-print" onclick="window.print()">
        🖨 Cetak Kartu Anggota
    </button>
</div>

</body>
</html>

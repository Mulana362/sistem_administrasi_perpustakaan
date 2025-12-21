@extends('layouts.app')

@section('title', 'Dashboard Admin Perpustakaan')

@section('content')
@php
    use App\Models\Book;
    use App\Models\Borrowing;
    use App\Models\Visitor;

    // total koleksi mengikuti jumlah stok buku
    $totalBooks        = Book::sum('stock');
    $activeBorrowings  = Borrowing::where('status', 'Dipinjam')->count();
    $overdueBorrowings = Borrowing::where('status', 'Dipinjam')
                            ->whereDate('due_date', '<', now())
                            ->count();
    $todayVisitors     = Visitor::whereDate('created_at', today())->count();
    $todayBorrowings   = Borrowing::whereDate('created_at', today())->count();
@endphp

<style>
    /* ===== BACKGROUND HALAMAN (dibikin adem & modern) ===== */
    body {
        background:
            radial-gradient(circle at top left, #dbeafe 0, #eff6ff 28%, transparent 55%),
            radial-gradient(circle at bottom right, #e5e7eb 0, #f9fafb 40%, #e5e7eb 100%);
    }

    .admin-wrapper {
        max-width: 1150px;
        margin: 24px auto 40px;
    }

    /* HEADER */
    .admin-hero {
        background: linear-gradient(135deg, #1d4ed8, #2563eb);
        border-radius: 18px;
        padding: 20px 24px;
        color: #fff;
        margin-bottom: 24px;
        box-shadow: 0 16px 40px rgba(37, 99, 235, 0.38);
        display: flex;
        justify-content: space-between;
        gap: 16px;
        align-items: flex-start;
        position: relative;
        overflow: hidden;
    }

    .admin-hero::after {
        content: "";
        position: absolute;
        width: 260px;
        height: 260px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.14);
        right: -80px;
        top: -80px;
    }

    .hero-left {
        display: flex;
        align-items: center;
        gap: 12px;
        position: relative;
        z-index: 2;
    }

    .hero-icon {
        width: 50px;
        height: 50px;
        border-radius: 16px;
        background: rgba(15,23,42,0.18);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.7rem;
    }

    .hero-title {
        font-size: 1.7rem;
        font-weight: 700;
        margin-bottom: 3px;
    }

    .hero-sub {
        font-size: 0.9rem;
        opacity: 0.92;
    }

    .hero-right {
        text-align: right;
        position: relative;
        z-index: 2;
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        background: rgba(15,23,42,0.16);
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 0.8rem;
    }

    .hero-right small {
        display: block;
        margin-top: 6px;
        font-size: 0.78rem;
        opacity: .9;
    }

    /* STATISTIK */
    .stat-row {
        display: grid;
        grid-template-columns: repeat(4, minmax(0,1fr));
        gap: 12px;
        margin-bottom: 22px;
    }

    .stat-card {
        border-radius: 14px;
        border: none;
        box-shadow: 0 8px 22px rgba(15, 23, 42, 0.08);
        background: #ffffff;
        padding: 12px 14px;
    }

    .stat-label {
        font-size: .78rem;
        text-transform: uppercase;
        letter-spacing: .08em;
        font-weight: 600;
        color: #6b7280;
        margin-bottom: 4px;
    }

    .stat-value {
        font-size: 1.9rem;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .stat-note {
        font-size: .8rem;
        color: #9ca3af;
    }

    /* GRID MENU + QR */
    .main-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.1fr) minmax(0, 0.9fr);
        gap: 18px;
        align-items: flex-start;
    }

    .card-box {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 8px 20px rgba(15,23,42,0.06);
        padding: 16px 18px;
    }

    .section-title {
        font-weight: 700;
        font-size: 1.05rem;
        display: flex;
        align-items: center;
        gap: .45rem;
        margin-bottom: 6px;
        color: #111827;
    }

    .section-title span.emoji {
        font-size: 1.4rem;
    }

    .section-sub {
        font-size: .8rem;
        color: #6b7280;
        margin-bottom: 14px;
    }

    /* MENU CEPAT WARNA-WARNI */
    .quick-menu {
        display: grid;
        gap: 10px;
    }

    .quick-btn {
        border-radius: 12px;
        padding: 10px 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        text-decoration: none;
        color: #111827;
        font-size: .9rem;
        font-weight: 600;
        box-shadow: 0 10px 22px rgba(15,23,42,0.08);
        transition: transform .12s ease, box-shadow .12s ease, filter .12s ease;
        border: none;
    }

    .quick-btn span.left {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .quick-btn span.emoji {
        font-size: 1.3rem;
    }

    .quick-btn span.caption-small {
        font-size: .8rem;
        display: block;
        font-weight: 400;
        opacity: .9;
    }

    .quick-btn:hover {
        transform: translateY(-2px);
        filter: brightness(1.03);
        text-decoration: none;
    }

    .quick-books {
        background: linear-gradient(135deg, #1d4ed8, #3b82f6);
        color: #fff;
    }

    .quick-borrow {
        background: linear-gradient(135deg, #f59e0b, #f97316);
        color: #111827;
    }

    .quick-members {
        background: linear-gradient(135deg, #6366f1, #a855f7);
        color: #f9fafb;
    }

    .quick-visitors {
        background: linear-gradient(135deg, #10b981, #22c55e);
        color: #022c22;
    }

    .quick-input-visit {
        background: linear-gradient(135deg, #0ea5e9, #22c55e);
        color: #052e16;
    }

    .quick-logout {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        color: #b91c1c;
    }

    /* QR + RINGKASAN */
    .qr-box {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .qr-inner {
        background: #f9fafb;
        border-radius: 16px;
        border: 1px dashed #d1d5db;
        padding: 14px;
        text-align: center;
    }

    /* QR lebih besar dikit */
    .qr-inner img {
        width: 210px;
        height: 210px;
        object-fit: contain;
    }

    .small-muted {
        font-size: .8rem;
        color: #6b7280;
    }

    .activity-box {
        background: #f9fafb;
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        padding: 10px 12px;
    }

    .activity-item {
        display: flex;
        gap: 8px;
        align-items: center;
        padding: 5px 0;
        border-bottom: 1px dashed #e5e7eb;
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-dot {
        width: 9px;
        height: 9px;
        border-radius: 999px;
        background: #22c55e;
    }

    .activity-title {
        font-size: .9rem;
        font-weight: 600;
        color: #111827;
    }

    .activity-text {
        font-size: .8rem;
        color: #6b7280;
    }
</style>

<div class="admin-wrapper">

    {{-- HEADER --}}
    <div class="admin-hero">
        <div class="hero-left">
            <div class="hero-icon">📚</div>
            <div>
                <div class="hero-title">Dashboard Admin Perpustakaan</div>
                <div class="hero-sub">
                    Kelola koleksi buku, peminjaman, dan kunjungan perpustakaan SMPN 1 Bandung.
                </div>
            </div>
        </div>
        <div class="hero-right">
            <div class="hero-badge">
                🕒 Jam buka: <strong>07.00 – 15.00</strong>
            </div>
            <small>Tanggal: <strong>{{ now()->translatedFormat('d F Y') }}</strong></small>
        </div>
    </div>

    {{-- STATISTIK --}}
    <div class="stat-row">
        <div class="stat-card">
            <div class="stat-label">Total Koleksi (Stok Buku)</div>
            <div class="stat-value text-primary">{{ $totalBooks }}</div>
            <div class="stat-note">Total stok semua buku yang diinput admin.</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Peminjaman Aktif</div>
            <div class="stat-value" style="color:#f97316;">{{ $activeBorrowings }}</div>
            <div class="stat-note">Buku yang sedang dipinjam siswa.</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Peminjaman Terlambat</div>
            <div class="stat-value" style="color:#dc2626;">{{ $overdueBorrowings }}</div>
            <div class="stat-note">Perlu dicek untuk pengingat / denda.</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Kunjungan Hari Ini</div>
            <div class="stat-value" style="color:#16a34a;">{{ $todayVisitors }}</div>
            <div class="stat-note">Jumlah tamu yang tercatat hari ini.</div>
        </div>
    </div>

    <div class="main-grid">

        {{-- KIRI: MENU CEPAT --}}
        <div class="card-box">
            <div class="section-title">
                <span class="emoji">📌</span>
                <span>Menu Cepat</span>
            </div>
            <div class="section-sub">
                Pilih menu untuk mengelola data perpustakaan. Input kunjungan dibuat terpisah seperti fitur lainnya.
            </div>

            <div class="quick-menu">

                {{-- Input Kunjungan (Form Terpisah) --}}
                <a href="{{ route('visit.register') }}" class="quick-btn quick-input-visit">
                    <span class="left">
                        <span class="emoji">📝</span>
                        <span>
                            Input Kunjungan (Form)
                            <span class="caption-small">Buka halaman form /kunjungan untuk siswa / tamu.</span>
                        </span>
                    </span>
                </a>

                {{-- Peminjaman --}}
                <a href="{{ route('borrowings.index') }}" class="quick-btn quick-borrow">
                    <span class="left">
                        <span class="emoji">🔄</span>
                        <span>
                            Peminjaman & Pengembalian
                            <span class="caption-small">Transaksi peminjaman harian.</span>
                        </span>
                    </span>
                </a>

                {{-- Kartu Anggota --}}
                <a href="{{ route('members.index') }}" class="quick-btn quick-members">
                    <span class="left">
                        <span class="emoji">🎫</span>
                        <span>
                            Cetak Kartu Anggota
                            <span class="caption-small">Kartu ID siswa untuk peminjaman.</span>
                        </span>
                    </span>
                </a>

                {{-- Rekap Kunjungan --}}
                <a href="{{ route('visitors.index') }}" class="quick-btn quick-visitors">
                    <span class="left">
                        <span class="emoji">📊</span>
                        <span>
                            Rekap Kunjungan Perpustakaan
                            <span class="caption-small">Daftar tamu yang sudah tercatat.</span>
                        </span>
                    </span>
                </a>

                {{-- Buku --}}
                <a href="{{ route('books.index') }}" class="quick-btn quick-books">
                    <span class="left">
                        <span class="emoji">📖</span>
                        <span>
                            Kelola Data Buku
                            <span class="caption-small">Tambah, ubah, dan atur stok koleksi.</span>
                        </span>
                    </span>
                </a>

                {{-- Logout --}}
                <a href="{{ route('admin.logout') }}" class="quick-btn quick-logout">
                    <span class="left">
                        <span class="emoji">🚪</span>
                        <span>
                            Logout Admin
                            <span class="caption-small">Keluar dari sesi admin.</span>
                        </span>
                    </span>
                </a>

            </div>
        </div>

        {{-- KANAN: QR CODE + RINGKASAN --}}
        <div class="card-box">
            <div class="qr-box">

                {{-- QR Kunjungan --}}
                <div>
                    <div class="section-title" style="margin-bottom:6px;">
                        <span class="emoji">📱</span>
                        <span>QR Kunjungan Perpustakaan</span>
                    </div>
                    <div class="section-sub" style="margin-bottom:10px;">
                        Tempel QR di meja perpustakaan. Siswa cukup scan dari HP, lalu mengisi form di halaman <b>/kunjungan</b>.
                    </div>

                    <div class="qr-inner">
                        <img src="{{ asset('images/qr-kunjungan.jpg') }}" alt="QR Kunjungan Perpustakaan">
                    </div>
                    <div class="small-muted mt-2 text-center">
                        Pastikan koneksi WiFi sekolah tersedia agar siswa bisa mengakses halaman kunjungan.
                    </div>
                </div>

                {{-- Ringkasan Hari Ini --}}
                <div>
                    <div class="section-title" style="margin-top:14px; font-size:1rem;">
                        <span class="emoji">📆</span>
                        <span>Ringkasan Aktivitas Hari Ini</span>
                    </div>
                    <div class="section-sub">
                        Aktivitas perpustakaan pada tanggal <b>{{ now()->translatedFormat('d F Y') }}</b>.
                    </div>

                    <div class="activity-box">
                        <div class="activity-item">
                            <div class="activity-dot" style="background:#22c55e;"></div>
                            <div>
                                <div class="activity-title">Kunjungan Siswa</div>
                                <div class="activity-text">
                                    Terdapat <strong>{{ $todayVisitors }}</strong> kunjungan yang tercatat hari ini.
                                </div>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-dot" style="background:#3b82f6;"></div>
                            <div>
                                <div class="activity-title">Peminjaman Buku</div>
                                <div class="activity-text">
                                    Tercatat <strong>{{ $todayBorrowings }}</strong> transaksi peminjaman hari ini.
                                </div>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-dot" style="background:#f97316;"></div>
                            <div>
                                <div class="activity-title">Catatan Petugas</div>
                                <div class="activity-text">
                                    Gunakan menu peminjaman & rekap kunjungan untuk melihat detail tiap transaksi.
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>

    </div>

</div>
@endsection

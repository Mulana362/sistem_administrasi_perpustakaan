@extends('layouts.app')

@section('title', 'Dashboard Admin Perpustakaan')

@section('content')
@php
    use App\Models\Book;
    use App\Models\Borrowing;
    use App\Models\Visitor;
    use Illuminate\Support\Facades\Schema;
    use Carbon\Carbon;

    $today = Carbon::now()->timezone(config('app.timezone', 'Asia/Jakarta'))->toDateString();

    $totalBooks        = Book::sum('stock');
    $activeBorrowings  = Borrowing::where('status', 'Dipinjam')->count();
    $overdueBorrowings = Borrowing::where('status', 'Terlambat')->count();
    $todayBorrowings   = Borrowing::whereDate('created_at', $today)->count();
    $totalPengajuan    = Borrowing::where('status', 'Diajukan')->count();

    $visitorTable = (new Visitor)->getTable();

    if (Schema::hasColumn($visitorTable, 'visit_date')) {
        $todayVisitors = Visitor::whereDate('visit_date', $today)->count();
    } elseif (Schema::hasColumn($visitorTable, 'date')) {
        $todayVisitors = Visitor::whereDate('date', $today)->count();
    } elseif (Schema::hasColumn($visitorTable, 'tanggal')) {
        $todayVisitors = Visitor::whereDate('tanggal', $today)->count();
    } else {
        $todayVisitors = Visitor::whereDate('created_at', $today)->count();
    }

    $visitPath = route('visit.register', [], false);
    $baseUrl   = rtrim(config('app.url') ?: request()->getSchemeAndHttpHost(), '/');
    $visitUrl  = $baseUrl . $visitPath;
    $qrUrl     = 'https://quickchart.io/qr?text=' . urlencode($visitUrl) . '&size=320';
@endphp

<style>
    body {
        background:
            radial-gradient(circle at top left, #dbeafe 0, #eff6ff 28%, transparent 55%),
            radial-gradient(circle at bottom right, #e5e7eb 0, #f9fafb 40%, #e5e7eb 100%);
    }

    .admin-wrapper {
        max-width: 1150px;
        margin: 24px auto 40px;
    }

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

    .stat-row {
        display: grid;
        grid-template-columns: repeat(5, minmax(0,1fr));
        gap: 12px;
        margin-bottom: 22px;
    }

    @media (max-width: 992px){
        .stat-row { grid-template-columns: repeat(2, minmax(0,1fr)); }
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

    .main-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.1fr) minmax(0, 0.9fr);
        gap: 18px;
        align-items: flex-start;
    }

    @media (max-width: 992px){
        .main-grid { grid-template-columns: 1fr; }
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

    .qr-box {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .qr-inner {
        background: #f9fafb;
        border-radius: 16px;
        border: 1px dashed #d1d5db;
        padding: 18px;
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

    .voice-toggle-wrap {
        display: flex;
        justify-content: center;
        margin-top: 12px;
    }

    .voice-toggle-btn {
        border: none;
        border-radius: 999px;
        padding: 8px 16px;
        font-size: .85rem;
        font-weight: 600;
        cursor: pointer;
        background: linear-gradient(135deg, #2563eb, #4f46e5);
        color: #fff;
        box-shadow: 0 10px 24px rgba(37,99,235,.22);
    }

    .qr-visit-shell {
        display: grid;
        gap: 18px;
        justify-items: center;
    }

    .qr-visit-badge {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        width: fit-content;
        padding: .46rem .9rem;
        border-radius: 999px;
        background: #dbeafe;
        color: #1d4ed8;
        font-size: .8rem;
        font-weight: 700;
    }

    .qr-visit-code-wrap {
        width: 100%;
        max-width: 320px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 22px;
        padding: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 14px 30px rgba(15,23,42,0.08);
    }

    .qr-visit-code {
        width: 100%;
        max-width: 260px;
        aspect-ratio: 1 / 1;
        object-fit: contain;
        display: block;
        background: #fff;
    }

    .qr-step-grid {
        width: 100%;
        display: grid;
        grid-template-columns: repeat(3, minmax(0,1fr));
        gap: 10px;
    }

    .qr-step-item {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 12px;
    }

    .qr-step-number {
        width: 28px;
        height: 28px;
        border-radius: 999px;
        background: linear-gradient(135deg, #2563eb, #4f46e5);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: .8rem;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .qr-step-title {
        font-size: .84rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 4px;
    }

    .qr-step-text {
        font-size: .78rem;
        color: #6b7280;
        line-height: 1.5;
    }

    @media (max-width: 768px) {
        .qr-step-grid {
            grid-template-columns: 1fr;
        }

        .qr-visit-code-wrap {
            max-width: 280px;
        }

        .qr-visit-code {
            max-width: 220px;
        }
    }
</style>

<div class="admin-wrapper">

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

        <div class="stat-card">
            <div class="stat-label">Total Pengajuan</div>
            <div class="stat-value" style="color:#2563eb;">{{ $totalPengajuan }}</div>
            <div class="stat-note">Jumlah pengajuan yang masih menunggu diproses (status: Diajukan).</div>
        </div>
    </div>

    <div class="main-grid">

        <div class="card-box">
            <div class="section-title">
                <span class="emoji">📌</span>
                <span>Menu Cepat</span>
            </div>
            <div class="section-sub">
                Pilih menu untuk mengelola data perpustakaan. Input kunjungan dibuat terpisah seperti fitur lainnya.
            </div>

            <div class="quick-menu">

                <a href="{{ route('visit.register') }}" class="quick-btn quick-input-visit">
                    <span class="left">
                        <span class="emoji">📝</span>
                        <span>
                            Input Kunjungan (Form)
                            <span class="caption-small">Buka halaman form /kunjungan untuk siswa / tamu.</span>
                        </span>
                    </span>
                </a>

                <a href="{{ route('borrowings.index') }}" class="quick-btn quick-borrow">
                    <span class="left">
                        <span class="emoji">🔄</span>
                        <span>
                            Pengajuan, Peminjaman & Pengembalian
                            <span class="caption-small">Kelola pengajuan + transaksi peminjaman harian.</span>
                        </span>
                    </span>
                </a>

                <a href="{{ route('members.index') }}" class="quick-btn quick-members">
                    <span class="left">
                        <span class="emoji">🎫</span>
                        <span>
                            Cetak Kartu Anggota
                            <span class="caption-small">Kartu ID siswa untuk peminjaman.</span>
                        </span>
                    </span>
                </a>

                <a href="{{ route('visitors.index') }}" class="quick-btn quick-visitors">
                    <span class="left">
                        <span class="emoji">📊</span>
                        <span>
                            Rekap Kunjungan Perpustakaan
                            <span class="caption-small">Daftar tamu yang sudah tercatat.</span>
                        </span>
                    </span>
                </a>

                <a href="{{ route('books.index') }}" class="quick-btn quick-books">
                    <span class="left">
                        <span class="emoji">📖</span>
                        <span>
                            Kelola Data Buku
                            <span class="caption-small">Tambah, ubah, dan atur stok koleksi.</span>
                        </span>
                    </span>
                </a>

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

        <div class="card-box">
            <div class="qr-box">

                <div>
                    <div class="section-title" style="margin-bottom:6px;">
                        <span class="emoji">📱</span>
                        <span>QR Kunjungan Perpustakaan</span>
                    </div>
                    <div class="section-sub" style="margin-bottom:10px;">
                        Siswa bisa scan QR ini lewat HP untuk langsung membuka form kunjungan perpustakaan.
                    </div>

                    <div class="qr-inner">
                        <div class="qr-visit-shell">
                            <div class="qr-visit-badge">⚡ Akses cepat dari HP siswa</div>

                            <div class="qr-visit-code-wrap">
                                <img
                                    class="qr-visit-code"
                                    src="{{ $qrUrl }}"
                                    alt="QR Kunjungan Perpustakaan"
                                >
                            </div>

                            <div class="qr-step-grid">
                                <div class="qr-step-item">
                                    <div class="qr-step-number">1</div>
                                    <div class="qr-step-title">Buka kamera HP</div>
                                    <div class="qr-step-text">
                                        Siswa cukup membuka kamera atau aplikasi scan QR di ponsel.
                                    </div>
                                </div>

                                <div class="qr-step-item">
                                    <div class="qr-step-number">2</div>
                                    <div class="qr-step-title">Scan QR kunjungan</div>
                                    <div class="qr-step-text">
                                        Arahkan kamera ke QR agar link form kunjungan muncul otomatis.
                                    </div>
                                </div>

                                <div class="qr-step-item">
                                    <div class="qr-step-number">3</div>
                                    <div class="qr-step-title">Isi form kunjungan</div>
                                    <div class="qr-step-text">
                                        Setelah terbuka, siswa tinggal isi data kunjungan langsung dari HP.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="voice-toggle-wrap" style="gap:8px; flex-wrap:wrap;">
                        <button type="button" id="testBreakVoice" class="voice-toggle-btn">
                            🔔 Test Istirahat
                        </button>

                        <button type="button" id="testCloseVoice" class="voice-toggle-btn">
                            🔔 Test Tutup
                        </button>
                    </div>

                    <div class="small-muted mt-2 text-center">
                        Pengumuman otomatis akan diputar saat jam istirahat dan menjelang tutup perpustakaan.
                    </div>
                </div>

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
                                <div class="activity-title">Pengajuan / Peminjaman Buku</div>
                                <div class="activity-text">
                                    Tercatat <strong>{{ $todayBorrowings }}</strong> aktivitas pengajuan/peminjaman hari ini.
                                </div>
                            </div>
                        </div>

                        <div class="activity-item">
                            <div class="activity-dot" style="background:#f97316;"></div>
                            <div>
                                <div class="activity-title">Catatan Petugas</div>
                                <div class="activity-text">
                                    Gunakan menu pengajuan, peminjaman & pengembalian untuk melihat detail tiap transaksi.
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>

    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const testBreakBtn = document.getElementById('testBreakVoice');
    const testCloseBtn = document.getElementById('testCloseVoice');

    const storageKeyLastBreakDate = 'library_voice_break_last_date';
    const storageKeyLastCloseDate = 'library_voice_close_last_date';

    const breakMessageId = 'Diberitahukan kepada seluruh pengunjung perpustakaan bahwa saat ini Perpustakaan SMP Negeri 1 Bandung memasuki jam istirahat. Kami mengimbau seluruh siswa untuk mengembalikan buku yang telah dibaca ke tempatnya dengan rapi. Layanan akan dibuka kembali setelah waktu istirahat berakhir. Terima kasih atas perhatian dan kerja samanya.';

    const closeMessageId = 'Perhatian kepada seluruh pengunjung perpustakaan. Layanan Perpustakaan SMP Negeri 1 Bandung akan segera berakhir pada pukul 15.00. Silakan menyelesaikan kegiatan membaca, peminjaman, dan pengembalian buku. Terima kasih.';

    function speakText(text) {
        if (!('speechSynthesis' in window)) {
            return;
        }

        window.speechSynthesis.cancel();

        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'id-ID';
        utterance.rate = 0.95;
        utterance.pitch = 1;
        utterance.volume = 1;

        const voices = window.speechSynthesis.getVoices();
        const matchedVoice = voices.find(v => v.lang && v.lang.toLowerCase().startsWith('id'));
        if (matchedVoice) {
            utterance.voice = matchedVoice;
        }

        window.speechSynthesis.speak(utterance);
    }

    function playStationChimeThenSpeak(text) {
        const audio = new Audio('/audio/chime-station.mp3');
        audio.volume = 1;

        audio.onended = function () {
            speakText(text);
        };

        audio.onerror = function () {
            speakText(text);
        };

        audio.play().catch(function () {
            speakText(text);
        });
    }

    function todayKey() {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    function checkAndPlayReminder() {
        const now = new Date();
        const hours = now.getHours();
        const minutes = now.getMinutes();
        const today = todayKey();

        if (hours === 12 && minutes >= 0 && minutes <= 4) {
            if (localStorage.getItem(storageKeyLastBreakDate) !== today) {
                localStorage.setItem(storageKeyLastBreakDate, today);
                playStationChimeThenSpeak(breakMessageId);
            }
        }

        if (hours === 14 && minutes >= 55 && minutes <= 59) {
            if (localStorage.getItem(storageKeyLastCloseDate) !== today) {
                localStorage.setItem(storageKeyLastCloseDate, today);
                playStationChimeThenSpeak(closeMessageId);
            }
        }
    }

    if (testBreakBtn) {
        testBreakBtn.addEventListener('click', function () {
            playStationChimeThenSpeak(breakMessageId);
        });
    }

    if (testCloseBtn) {
        testCloseBtn.addEventListener('click', function () {
            playStationChimeThenSpeak(closeMessageId);
        });
    }

    if ('speechSynthesis' in window) {
        window.speechSynthesis.getVoices();
        window.speechSynthesis.onvoiceschanged = function () {
            window.speechSynthesis.getVoices();
        };
    }

    checkAndPlayReminder();
    setInterval(checkAndPlayReminder, 30000);
});
</script>
@endsection

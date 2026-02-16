@extends('layouts.app')

@section('title', 'Peminjaman & Pengembalian Buku')

@section('content')
<style>
    :root {
        --blue-main: #1e88e5;
        --blue-soft: #e3f2fd;
        --blue-soft-2: #bbdefb;
        --gray-bg: #f5f5f5;
        --gray-border: #e0e0e0;
        --green-main: #2e7d32;
        --red-main: #c62828;
        --orange-main: #ef6c00;
    }

    body { background-color: #f3f4f6; }

    .page-header-borrow{
        border-radius:18px;padding:18px 22px;
        background:linear-gradient(135deg,#e3f2fd,#ffffff);
        border:1px solid var(--blue-soft-2);
        display:flex;flex-wrap:wrap;justify-content:space-between;gap:14px;align-items:center;margin-bottom:20px;
    }
    .page-header-left{display:flex;align-items:center;gap:14px;}
    .page-icon{
        width:50px;height:50px;border-radius:16px;background:var(--blue-main);
        display:flex;align-items:center;justify-content:center;color:white;font-size:1.6rem;
        box-shadow:0 14px 30px rgba(30,136,229,.45);
    }
    .page-title{font-size:1.4rem;font-weight:700;margin-bottom:4px;color:#0f172a;}
    .page-subtitle{font-size:.9rem;color:#4b5563;}

    .stat-card-borrow{
        border-radius:14px;border:1px solid var(--gray-border);
        background:#fff;box-shadow:0 8px 18px rgba(15,23,42,.04);
        transition:transform .12s ease, box-shadow .12s ease, border-color .12s ease;
    }
    .stat-card-borrow:hover{transform:translateY(-3px);box-shadow:0 16px 34px rgba(15,23,42,.10);border-color:var(--blue-soft-2);}
    .stat-label{font-size:.78rem;text-transform:uppercase;letter-spacing:.08em;font-weight:600;color:#6b7280;}
    .stat-value{font-size:1.9rem;font-weight:700;}
    .small-muted{font-size:.8rem;color:#6b7280;}

    .badge-status{
        border-radius:999px;font-size:.75rem;padding:.28rem .8rem;
        display:inline-flex;align-items:center;gap:.35rem;
    }
    .badge-status span.dot{width:8px;height:8px;border-radius:999px;display:inline-block;}
    .badge-status.diajukan{background:#fff3e0;color:var(--orange-main);}
    .badge-status.diajukan span.dot{background:var(--orange-main);}
    .badge-status.dipinjam{background:#e8f5e9;color:var(--green-main);}
    .badge-status.dipinjam span.dot{background:var(--green-main);}
    .badge-status.kembali{background:#e8f5e9;color:#2e7d32;}
    .badge-status.kembali span.dot{background:var(--green-main);}
    .badge-status.terlambat{background:#ffebee;color:#c62828;}
    .badge-status.terlambat span.dot{background:var(--red-main);}

    .btn-pill-sm{border-radius:999px;font-size:.8rem;padding:.3rem .85rem;}

    .tab-menu-card{border-radius:16px;border:1px solid var(--gray-border);box-shadow:0 6px 18px rgba(15,23,42,.05);background:#fff;}
    .tab-menu-header{
        border-bottom:1px solid var(--gray-border);
        padding:12px 16px;display:flex;justify-content:space-between;align-items:center;gap:12px;
        background:#f9fafb;border-radius:16px 16px 0 0;
    }
    .section-title{font-weight:600;display:flex;align-items:center;gap:.4rem;}
    .section-title span.emoji{font-size:1.3rem;}

    .table-report thead th{background:#eff6ff;border-bottom:1px solid var(--gray-border);font-size:.84rem;}
    .table-report tbody tr:hover{background:#f9fafb;}

    .pagination{
        gap:6px;
        margin: 10px 0 0;
        flex-wrap: wrap;
    }
    .pagination .page-item .page-link{
        border-radius: 12px !important;
        padding: 6px 10px !important;
        font-size: .85rem !important;
        line-height: 1 !important;
        border: 1px solid #e5e7eb !important;
        color: #0f172a !important;
        background: #fff !important;
        box-shadow: 0 6px 16px rgba(15,23,42,.06);
        min-width: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .pagination .page-item.active .page-link{
        background: linear-gradient(135deg, #2563eb, #4f46e5) !important;
        border-color: transparent !important;
        color: #fff !important;
        box-shadow: 0 10px 22px rgba(37,99,235,.35);
    }
    .pagination .page-item.disabled .page-link{
        opacity: .55;
        box-shadow: none;
    }
    .pagination .page-link:hover{
        background: #f3f4f6 !important;
        border-color: #d1d5db !important;
    }

    /* ✅ PRINT ONLY (tidak ngubah tampilan normal) */
    @media print {
        .page-header-borrow,
        .row.g-3.mb-4,
        .tab-menu-header,
        #borrowTab,
        .btn,
        .pagination,
        .small-muted { display: none !important; }

        .tab-pane { display: none !important; }
        .tab-pane.active { display: block !important; }

        body { background: #fff !important; }
    }
</style>

@php
    use App\Models\Borrowing;

    // statistik ambil dari DB biar akurat
    $totalBorrow    = Borrowing::whereIn('status', ['Dipinjam', 'Kembali', 'Terlambat'])->count();
    $countDiajukan  = Borrowing::where('status', 'Diajukan')->count();

    // ✅ FIX: aktif harus Dipinjam + Terlambat
    $activeBorrow   = Borrowing::whereIn('status', ['Dipinjam','Terlambat'])->count();

    // ✅ FIX: overdue aman kalau due_date null
    $overdueBorrow  = Borrowing::where('status', 'Dipinjam')
                        ->whereNotNull('due_date')
                        ->whereDate('due_date', '<', today())
                        ->count();
@endphp

<div class="container py-4">

    <div class="page-header-borrow">
        <div class="page-header-left">
            <div class="page-icon">📘</div>
            <div>
                <div class="page-title">Kelola Peminjaman & Pengembalian</div>
                <div class="page-subtitle">
                    Halaman khusus petugas perpustakaan untuk mengelola pengajuan, peminjaman aktif, dan riwayat pengembalian.
                </div>
            </div>
        </div>
        <div class="d-flex flex-column flex-md-row gap-2">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">← Kembali ke Dashboard</a>
            <a href="{{ route('borrowings.create') }}" class="btn btn-primary btn-sm">+ Tambah Peminjaman</a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card stat-card-borrow h-100">
                <div class="card-body">
                    <div class="stat-label mb-1">Total Transaksi</div>
                    <div class="stat-value text-primary mb-1">{{ $totalBorrow }}</div>
                    <div class="small-muted">Transaksi yang sudah diproses (Dipinjam / Kembali / Terlambat).</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card-borrow h-100">
                <div class="card-body">
                    <div class="stat-label mb-1">Pengajuan</div>
                    <div class="stat-value mb-1" style="color: var(--orange-main);">{{ $countDiajukan }}</div>
                    <div class="small-muted">Pengajuan menunggu diproses admin.</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card-borrow h-100">
                <div class="card-body">
                    <div class="stat-label mb-1">Dipinjam (Aktif)</div>
                    <div class="stat-value mb-1" style="color: var(--green-main);">{{ $activeBorrow }}</div>
                    <div class="small-muted">Buku yang saat ini masih dipinjam siswa.</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card-borrow h-100">
                <div class="card-body">
                    <div class="stat-label mb-1">Terlambat</div>
                    <div class="stat-value mb-1" style="color: var(--red-main);">{{ $overdueBorrow }}</div>
                    <div class="small-muted">Perlu perhatian untuk pengingat dan denda.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-menu-card">
        <div class="tab-menu-header">
            <div>
                <div class="section-title mb-0">
                    <span class="emoji">📂</span>
                    <span>Menu Tampilan Data</span>
                </div>
                <div class="small-muted">
                    Pengajuan (butuh aksi), Peminjaman aktif, dan Riwayat (untuk laporan/cetak).
                </div>
            </div>
            <div class="d-none d-md-block small-muted">
                Klik salah satu tab di bawah untuk mengganti kategori.
            </div>
        </div>

        <div class="px-3 pt-2">
            <ul class="nav nav-pills mb-3 mt-1" id="borrowTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pengajuan-tab" data-bs-toggle="tab" data-bs-target="#pengajuan" type="button" role="tab">
                        🟠 Pengajuan
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="aktif-tab" data-bs-toggle="tab" data-bs-target="#aktif" type="button" role="tab">
                        ✅ Peminjaman Aktif
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="riwayat-tab" data-bs-toggle="tab" data-bs-target="#riwayat" type="button" role="tab">
                        📄 Riwayat (Kembali)
                    </button>
                </li>
            </ul>
        </div>

        <div class="tab-content pb-3 px-2">

            {{-- TAB 1: PENGAJUAN --}}
            <div class="tab-pane fade show active" id="pengajuan" role="tabpanel">
                <div class="px-2 pb-3">
                    <div class="small-muted mb-2">Daftar pengajuan menunggu diproses (setujui/tolak).</div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:70px;">No</th>
                                    <th style="width:130px;">ID Buku</th>
                                    <th>Anggota / Siswa</th>
                                    <th>Judul Buku</th>
                                    <th style="width:170px;">Kadaluarsa</th>
                                    <th style="width:140px;">Status</th>
                                    <th style="width:260px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pengajuan as $borrowing)
                                    @php
                                        $nama  = $borrowing->student_name ?? optional($borrowing->member)->name;
                                        $nis   = $borrowing->student_nis ?? optional($borrowing->member)->nis;
                                        $kelas = $borrowing->student_class ?? optional($borrowing->member)->class;
                                        $bookCode = optional($borrowing->book)->book_code ?? '-';

                                        // ✅ FIX: expired_at fallback ke created_at + 2 hari
                                        $exp = $borrowing->expired_at
                                            ? \Carbon\Carbon::parse($borrowing->expired_at)
                                            : \Carbon\Carbon::parse($borrowing->created_at)->addDays(2);
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $bookCode }}</td>
                                        <td>
                                            {{ $nama ?? '—' }}
                                            <div class="small-muted">NIS: {{ $nis ?? '-' }} | Kelas: {{ $kelas ?? '-' }}</div>
                                        </td>
                                        <td>
                                            {{ optional($borrowing->book)->title ?? '—' }}
                                            <div class="small-muted">{{ optional($borrowing->book)->author ?? '' }}</div>
                                        </td>
                                        <td>
                                            {{ $exp->timezone(config('app.timezone'))->locale('id')->translatedFormat('d F Y H:i') }}
                                        </td>
                                        <td>
                                            <span class="badge-status diajukan"><span class="dot"></span> Diajukan</span>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                <a href="{{ route('borrowings.edit', $borrowing->id) }}" class="btn btn-success btn-pill-sm">
                                                    Setujui / Proses
                                                </a>

                                                <form action="{{ route('borrowings.destroy', $borrowing->id) }}" method="POST"
                                                      onsubmit="return confirm('Tolak pengajuan ini? Data akan dihapus.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-outline-danger btn-pill-sm">Tolak</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center text-muted py-4">Belum ada pengajuan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-2">
                        {{ $pengajuan->withQueryString()->links() }}
                    </div>
                </div>
            </div>

            {{-- TAB 2: PEMINJAMAN AKTIF --}}
            <div class="tab-pane fade" id="aktif" role="tabpanel">
                <div class="px-2 pb-3">
                    <div class="small-muted mb-2">Buku yang sedang dipinjam / terlambat. Fokus untuk “tandai kembali”.</div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:70px;">No</th>
                                    <th style="width:130px;">ID Buku</th>
                                    <th style="width:170px;">Tanggal Pinjam</th>
                                    <th>Anggota / Siswa</th>
                                    <th>Judul Buku</th>
                                    <th style="width:170px;">Jatuh Tempo</th>
                                    <th style="width:140px;">Status</th>
                                    <th style="width:220px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($aktif as $borrowing)
                                    @php
                                        $nama  = $borrowing->student_name ?? optional($borrowing->member)->name;
                                        $nis   = $borrowing->student_nis ?? optional($borrowing->member)->nis;
                                        $kelas = $borrowing->student_class ?? optional($borrowing->member)->class;

                                        $isOverdue = $borrowing->status === 'Dipinjam'
                                            && $borrowing->due_date
                                            && $borrowing->due_date < now()->toDateString();

                                        $bookCode = optional($borrowing->book)->book_code ?? '-';
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $bookCode }}</td>
                                        <td>
                                            @if($borrowing->borrow_date)
                                                {{ \Carbon\Carbon::parse($borrowing->borrow_date)->locale('id')->translatedFormat('d F Y') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $nama ?? '—' }}
                                            <div class="small-muted">NIS: {{ $nis ?? '-' }} | Kelas: {{ $kelas ?? '-' }}</div>
                                        </td>
                                        <td>
                                            {{ optional($borrowing->book)->title ?? '—' }}
                                            <div class="small-muted">{{ optional($borrowing->book)->author ?? '' }}</div>
                                        </td>
                                        <td>
                                            @if($borrowing->due_date)
                                                {{ \Carbon\Carbon::parse($borrowing->due_date)->locale('id')->translatedFormat('d F Y') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($borrowing->status === 'Terlambat' || $isOverdue)
                                                <span class="badge-status terlambat"><span class="dot"></span> Terlambat</span>
                                            @else
                                                <span class="badge-status dipinjam"><span class="dot"></span> Dipinjam</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                <a href="{{ route('borrowings.edit', $borrowing->id) }}" class="btn btn-success btn-pill-sm">
                                                    Tandai Kembali
                                                </a>
                                                <a href="{{ route('borrowings.edit', $borrowing->id) }}" class="btn btn-outline-secondary btn-pill-sm">
                                                    Ubah
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="text-center text-muted py-4">Belum ada peminjaman aktif.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-2">
                        {{ $aktif->withQueryString()->links() }}
                    </div>
                </div>
            </div>

            {{-- TAB 3: RIWAYAT --}}
            <div class="tab-pane fade" id="riwayat" role="tabpanel">
                <div class="px-2 pb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="small-muted">Riwayat pengembalian untuk laporan/cetak.</div>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="window.print()">🖨 Cetak Laporan</button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-report align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width:70px;">No</th>
                                    <th style="width:130px;">ID Buku</th>
                                    <th style="width:170px;">Tanggal Pinjam</th>
                                    <th style="width:170px;">Tanggal Kembali</th>
                                    <th>Nama Peminjam</th>
                                    <th style="width:130px;">NIS</th>
                                    <th style="width:90px;">Kelas</th>
                                    <th style="width:140px;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($riwayat as $borrowing)
                                    @php
                                        $nama  = $borrowing->student_name ?? optional($borrowing->member)->name;
                                        $nis   = $borrowing->student_nis ?? optional($borrowing->member)->nis;
                                        $kelas = $borrowing->student_class ?? optional($borrowing->member)->class;
                                        $bookCode = optional($borrowing->book)->book_code ?? '-';
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $bookCode }}</td>
                                        <td>
                                            @if($borrowing->borrow_date)
                                                {{ \Carbon\Carbon::parse($borrowing->borrow_date)->locale('id')->translatedFormat('d F Y') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($borrowing->return_date)
                                                {{ \Carbon\Carbon::parse($borrowing->return_date)->locale('id')->translatedFormat('d F Y') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $nama ?? '—' }}</td>
                                        <td>{{ $nis ?? '-' }}</td>
                                        <td>{{ $kelas ?? '-' }}</td>
                                        <td>
                                            <span class="badge-status kembali"><span class="dot"></span> Kembali</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="text-center text-muted py-3">Belum ada riwayat pengembalian.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-2">
                        {{ $riwayat->withQueryString()->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    function activateTabFromHash() {
        const hash = window.location.hash;
        if (!hash) return;
        const tabBtn = document.querySelector(`[data-bs-target="${hash}"]`);
        if (tabBtn) {
            const tab = new bootstrap.Tab(tabBtn);
            tab.show();
        }
    }

    document.querySelectorAll('#borrowTab button[data-bs-toggle="tab"]').forEach(btn => {
        btn.addEventListener('shown.bs.tab', function (e) {
            const target = e.target.getAttribute('data-bs-target');
            if (target) window.location.hash = target;
        });
    });

    function patchPaginationLinks() {
        const hash = window.location.hash || '#pengajuan';
        document.querySelectorAll('.pagination a.page-link').forEach(a => {
            const url = new URL(a.href);
            a.href = url.toString().split('#')[0] + hash;
        });
    }

    activateTabFromHash();
    patchPaginationLinks();
});
</script>
@endsection

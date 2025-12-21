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

    body {
        background-color: #f3f4f6;
    }

    /* HEADER HALAMAN */
    .page-header-borrow {
        border-radius: 18px;
        padding: 18px 22px;
        background: linear-gradient(135deg, #e3f2fd, #ffffff);
        border: 1px solid var(--blue-soft-2);
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 14px;
        align-items: center;
        margin-bottom: 20px;
    }

    .page-header-left {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .page-icon {
        width: 50px;
        height: 50px;
        border-radius: 16px;
        background: var(--blue-main);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.6rem;
        box-shadow: 0 14px 30px rgba(30, 136, 229, 0.45);
    }

    .page-title {
        font-size: 1.4rem;
        font-weight: 700;
        margin-bottom: 4px;
        color: #0f172a;
    }

    .page-subtitle {
        font-size: .9rem;
        color: #4b5563;
    }

    /* STATISTIK */
    .stat-card-borrow {
        border-radius: 14px;
        border: 1px solid var(--gray-border);
        background: #ffffff;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.04);
        transition: transform .12s ease, box-shadow .12s ease, border-color .12s ease;
    }

    .stat-card-borrow:hover {
        transform: translateY(-3px);
        box-shadow: 0 16px 34px rgba(15, 23, 42, 0.10);
        border-color: var(--blue-soft-2);
    }

    .stat-label {
        font-size: .78rem;
        text-transform: uppercase;
        letter-spacing: .08em;
        font-weight: 600;
        color: #6b7280;
    }

    .stat-value {
        font-size: 1.9rem;
        font-weight: 700;
    }

    .small-muted {
        font-size: .8rem;
        color: #6b7280;
    }

    /* BADGE STATUS */
    .badge-status {
        border-radius: 999px;
        font-size: .75rem;
        padding: .28rem .8rem;
        display: inline-flex;
        align-items: center;
        gap: .35rem;
    }

    .badge-status span.dot {
        width: 8px;
        height: 8px;
        border-radius: 999px;
        display: inline-block;
    }

    .badge-status.pinjam {
        background: #fff3e0;
        color: #ef6c00;
    }
    .badge-status.pinjam span.dot {
        background: var(--orange-main);
    }

    .badge-status.kembali {
        background: #e8f5e9;
        color: #2e7d32;
    }
    .badge-status.kembali span.dot {
        background: var(--green-main);
    }

    .badge-status.terlambat {
        background: #ffebee;
        color: #c62828;
    }
    .badge-status.terlambat span.dot {
        background: var(--red-main);
    }

    /* BUTTON KECIL */
    .btn-pill-sm {
        border-radius: 999px;
        font-size: .8rem;
        padding: .3rem .85rem;
    }

    /* MENU TAB */
    .tab-menu-card {
        border-radius: 16px;
        border: 1px solid var(--gray-border);
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.05);
        background: #ffffff;
    }

    .tab-menu-header {
        border-bottom: 1px solid var(--gray-border);
        padding: 12px 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        background: #f9fafb;
        border-radius: 16px 16px 0 0;
    }

    .section-title {
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: .4rem;
    }

    .section-title span.emoji {
        font-size: 1.3rem;
    }

    .table-report thead th {
        background: #eff6ff;
        border-bottom: 1px solid var(--gray-border);
        font-size: .84rem;
    }

    .table-report tbody tr:hover {
        background: #f9fafb;
    }
</style>

@php
    use App\Models\Borrowing;

    // status di DB: 'Dipinjam', 'Kembali', 'Terlambat'
    $totalBorrow    = Borrowing::count();
    $activeBorrow   = Borrowing::where('status', 'Dipinjam')->count();
    $returnedBorrow = Borrowing::where('status', 'Kembali')->count();
    $overdueBorrow  = Borrowing::where('status', 'Dipinjam')
                        ->whereDate('due_date', '<', now())
                        ->count();
@endphp

<div class="container py-4">

    {{-- HEADER HALAMAN --}}
    <div class="page-header-borrow">
        <div class="page-header-left">
            <div class="page-icon">
                📘
            </div>
            <div>
                <div class="page-title">Kelola Peminjaman & Pengembalian</div>
                <div class="page-subtitle">
                    Halaman khusus petugas perpustakaan untuk mengelola transaksi peminjaman, pengembalian,
                    dan data peminjam.
                </div>
            </div>
        </div>
        <div class="d-flex flex-column flex-md-row gap-2">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                ← Kembali ke Dashboard
            </a>
            <a href="{{ route('borrowings.create') }}" class="btn btn-primary btn-sm">
                + Tambah Peminjaman
            </a>
        </div>
    </div>

    {{-- STATISTIK RINGKAS --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card stat-card-borrow h-100">
                <div class="card-body">
                    <div class="stat-label mb-1">Total Peminjaman</div>
                    <div class="stat-value text-primary mb-1">{{ $totalBorrow }}</div>
                    <div class="small-muted">Seluruh transaksi peminjaman tercatat di sistem.</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card-borrow h-100">
                <div class="card-body">
                    <div class="stat-label mb-1">Aktif</div>
                    <div class="stat-value mb-1" style="color: var(--orange-main);">{{ $activeBorrow }}</div>
                    <div class="small-muted">Buku yang saat ini masih dipinjam siswa.</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card-borrow h-100">
                <div class="card-body">
                    <div class="stat-label mb-1">Sudah Kembali</div>
                    <div class="stat-value mb-1" style="color: var(--green-main);">{{ $returnedBorrow }}</div>
                    <div class="small-muted">Transaksi peminjaman yang sudah selesai.</div>
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

    {{-- MENU TAB: LAPORAN vs DETAIL --}}
    <div class="tab-menu-card">
        <div class="tab-menu-header">
            <div>
                <div class="section-title mb-0">
                    <span class="emoji">📂</span>
                    <span>Menu Tampilan Data Peminjaman</span>
                </div>
                <div class="small-muted">
                    Pilih tampilan yang diinginkan: laporan data peminjam (siap cetak) atau detail peminjaman buku.
                </div>
            </div>
            <div class="d-none d-md-block small-muted">
                Klik salah satu tab di sebelah bawah untuk mengganti kategori.
            </div>
        </div>

        <div class="px-3 pt-2">
            {{-- NAV TAB --}}
            <ul class="nav nav-pills mb-3 mt-1" id="borrowTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button
                        class="nav-link active"
                        id="laporan-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#laporan"
                        type="button"
                        role="tab"
                        aria-controls="laporan"
                        aria-selected="true">
                        📄 Laporan Data Peminjam
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button
                        class="nav-link"
                        id="detail-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#detail"
                        type="button"
                        role="tab"
                        aria-controls="detail"
                        aria-selected="false">
                        📚 Detail Peminjaman Buku
                    </button>
                </li>
            </ul>
        </div>

        <div class="tab-content pb-3 px-2">

            {{-- TAB 1: LAPORAN DATA PEMINJAM --}}
            <div class="tab-pane fade show active" id="laporan" role="tabpanel" aria-labelledby="laporan-tab">
                <div class="px-2 pb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="small-muted">
                            Tabel ringkas berisi identitas peminjam untuk keperluan laporan dan rekap.
                        </div>
                        <button type="button"
                                class="btn btn-outline-primary btn-sm"
                                onclick="window.print()">
                            🖨 Cetak Laporan
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-report align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal Pinjam</th>
                                    <th>Nama Peminjam</th>
                                    <th>NIS</th>
                                    <th>Kelas</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($borrowings as $borrowing)
                                    @php
                                        // ambil data siswa: dari kolom manual, kalau kosong fallback ke relasi member
                                        $nama  = $borrowing->student_name  ?? optional($borrowing->member)->name;
                                        $nis   = $borrowing->student_nis   ?? optional($borrowing->member)->nis;
                                        $kelas = $borrowing->student_class ?? optional($borrowing->member)->class;

                                        $isOverdue = $borrowing->status === 'Dipinjam'
                                            && $borrowing->due_date
                                            && $borrowing->due_date < now()->toDateString();
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        {{-- 12 Desember 2025 --}}
                                        <td>{{ \Carbon\Carbon::parse($borrowing->borrow_date)->locale('id')->translatedFormat('d F Y') }}</td>
                                        <td>{{ $nama ?? '—' }}</td>
                                        <td>{{ $nis ?? '-' }}</td>
                                        <td>{{ $kelas ?? '-' }}</td>
                                        <td>
                                            @if($borrowing->status === 'Dipinjam')
                                                @if($isOverdue)
                                                    <span class="badge-status terlambat">
                                                        <span class="dot"></span> Dipinjam • Terlambat
                                                    </span>
                                                @else
                                                    <span class="badge-status pinjam">
                                                        <span class="dot"></span> Dipinjam
                                                    </span>
                                                @endif
                                            @elseif($borrowing->status === 'Terlambat')
                                                <span class="badge-status terlambat">
                                                    <span class="dot"></span> Terlambat
                                                </span>
                                            @else
                                                <span class="badge-status kembali">
                                                    <span class="dot"></span> Kembali
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-3">
                                            Belum ada data peminjaman yang tercatat.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(method_exists($borrowings, 'links'))
                        <div class="mt-2">
                            {{ $borrowings->links() }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- TAB 2: DETAIL PEMINJAMAN BUKU --}}
            <div class="tab-pane fade" id="detail" role="tabpanel" aria-labelledby="detail-tab">
                <div class="px-2 pb-3">
                    <div class="small-muted mb-2">
                        Tampilan lengkap untuk mengelola peminjaman harian: judul buku, jatuh tempo, dan aksi pengembalian.
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal Pinjam</th>
                                    <th>Anggota / Siswa</th>
                                    <th>Judul Buku</th>
                                    <th>Jatuh Tempo</th>
                                    <th>Status</th>
                                    <th style="width:230px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($borrowings as $borrowing)
                                    @php
                                        $nama  = $borrowing->student_name  ?? optional($borrowing->member)->name;
                                        $nis   = $borrowing->student_nis   ?? optional($borrowing->member)->nis;
                                        $kelas = $borrowing->student_class ?? optional($borrowing->member)->class;

                                        $isOverdue = $borrowing->status === 'Dipinjam'
                                            && $borrowing->due_date
                                            && $borrowing->due_date < now()->toDateString();
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>

                                        {{-- Tanggal pinjam: 12 Desember 2025 --}}
                                        <td>{{ \Carbon\Carbon::parse($borrowing->borrow_date)->locale('id')->translatedFormat('d F Y') }}</td>

                                        <td>
                                            {{ $nama ?? '—' }}
                                            <div class="small-muted">
                                                NIS: {{ $nis ?? '-' }} | Kelas: {{ $kelas ?? '-' }}
                                            </div>
                                        </td>

                                        <td>
                                            {{ optional($borrowing->book)->title ?? '—' }}
                                            <div class="small-muted">
                                                {{ optional($borrowing->book)->author ?? '' }}
                                            </div>
                                        </td>

                                        <td>
                                            @if($borrowing->due_date)
                                                {{-- Jatuh tempo: 12 Desember 2025 --}}
                                                {{ \Carbon\Carbon::parse($borrowing->due_date)->locale('id')->translatedFormat('d F Y') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>

                                        <td>
                                            @if($borrowing->status === 'Dipinjam')
                                                @if($isOverdue)
                                                    <span class="badge-status terlambat">
                                                        <span class="dot"></span> Dipinjam • Terlambat
                                                    </span>
                                                @else
                                                    <span class="badge-status pinjam">
                                                        <span class="dot"></span> Dipinjam
                                                    </span>
                                                @endif
                                            @elseif($borrowing->status === 'Terlambat')
                                                <span class="badge-status terlambat">
                                                    <span class="dot"></span> Terlambat
                                                </span>
                                            @else
                                                <span class="badge-status kembali">
                                                    <span class="dot"></span> Kembali
                                                </span>
                                            @endif
                                        </td>

                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                {{-- Ubah --}}
                                                <a href="{{ route('borrowings.edit', $borrowing->id) }}"
                                                   class="btn btn-outline-secondary btn-pill-sm">
                                                    Ubah
                                                </a>

                                                {{-- Tandai Kembali --}}
                                                @if($borrowing->status === 'Dipinjam')
                                                    <a href="{{ route('borrowings.edit', $borrowing->id) }}"
                                                       class="btn btn-success btn-pill-sm">
                                                        Tandai Kembali
                                                    </a>
                                                @endif

                                                {{-- Hapus --}}
                                                <form action="{{ route('borrowings.destroy', $borrowing->id) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('Yakin ingin menghapus data peminjaman ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-outline-danger btn-pill-sm">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            Belum ada data peminjaman yang tercatat.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(method_exists($borrowings, 'links'))
                        <div class="mt-2">
                            {{ $borrowings->links() }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

</div>
@endsection

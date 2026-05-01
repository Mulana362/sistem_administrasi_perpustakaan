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
        border-radius:18px;
        padding:18px 22px;
        background:linear-gradient(135deg,#e3f2fd,#ffffff);
        border:1px solid var(--blue-soft-2);
        display:flex;
        flex-wrap:wrap;
        justify-content:space-between;
        gap:14px;
        align-items:center;
        margin-bottom:20px;
    }

    .page-header-left{
        display:flex;
        align-items:center;
        gap:14px;
    }

    .page-icon{
        width:50px;
        height:50px;
        border-radius:16px;
        background:var(--blue-main);
        display:flex;
        align-items:center;
        justify-content:center;
        color:white;
        font-size:1.6rem;
        box-shadow:0 14px 30px rgba(30,136,229,.45);
    }

    .page-title{
        font-size:1.4rem;
        font-weight:700;
        margin-bottom:4px;
        color:#0f172a;
    }

    .page-subtitle{
        font-size:.9rem;
        color:#4b5563;
    }

    .stat-card-borrow{
        border-radius:14px;
        border:1px solid var(--gray-border);
        background:#fff;
        box-shadow:0 8px 18px rgba(15,23,42,.04);
        transition:transform .12s ease, box-shadow .12s ease, border-color .12s ease;
        height:100%;
    }

    .stat-card-borrow:hover{
        transform:translateY(-3px);
        box-shadow:0 16px 34px rgba(15,23,42,.10);
        border-color:var(--blue-soft-2);
    }

    .stat-label{
        font-size:.78rem;
        text-transform:uppercase;
        letter-spacing:.08em;
        font-weight:600;
        color:#6b7280;
    }

    .stat-value{
        font-size:1.9rem;
        font-weight:700;
    }

    .small-muted{
        font-size:.8rem;
        color:#6b7280;
    }

    .borrow-kpi-grid{
        display:grid;
        grid-template-columns:repeat(4, minmax(0, 1fr));
        gap:16px;
        margin-bottom:24px;
    }

    .fine-kpi-grid{
        display:grid;
        grid-template-columns:repeat(2, minmax(0, 1fr));
        gap:14px;
        margin-bottom:16px;
    }

    .issue-kpi-grid{
        display:grid;
        grid-template-columns:repeat(4, minmax(0, 1fr));
        gap:14px;
        margin-bottom:16px;
    }

    @media (max-width: 1200px){
        .borrow-kpi-grid{ grid-template-columns:repeat(2, minmax(0, 1fr)); }
        .fine-kpi-grid{ grid-template-columns:repeat(2, minmax(0, 1fr)); }
        .issue-kpi-grid{ grid-template-columns:repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 768px){
        .borrow-kpi-grid,
        .fine-kpi-grid,
        .issue-kpi-grid{ grid-template-columns:repeat(1, minmax(0, 1fr)); }
    }

    .badge-status{
        border-radius:999px;
        font-size:.75rem;
        padding:.28rem .8rem;
        display:inline-flex;
        align-items:center;
        gap:.35rem;
    }

    .badge-status span.dot{
        width:8px;
        height:8px;
        border-radius:999px;
        display:inline-block;
    }

    .badge-status.diajukan{background:#fff3e0;color:var(--orange-main);}
    .badge-status.diajukan span.dot{background:var(--orange-main);}

    .badge-status.dipinjam{background:#e8f5e9;color:var(--green-main);}
    .badge-status.dipinjam span.dot{background:var(--green-main);}

    .badge-status.kembali{background:#e8f5e9;color:#2e7d32;}
    .badge-status.kembali span.dot{background:var(--green-main);}

    .badge-status.terlambat{background:#ffebee;color:#c62828;}
    .badge-status.terlambat span.dot{background:var(--red-main);}

    .badge-status.paid{background:#ecfdf5;color:#166534;}
    .badge-status.paid span.dot{background:#16a34a;}

    .badge-status.unpaid{background:#fff7ed;color:#c2410c;}
    .badge-status.unpaid span.dot{background:#f97316;}

    .badge-status.hilang{background:#ffebee;color:#c62828;}
    .badge-status.hilang span.dot{background:#c62828;}

    .badge-status.rusak{background:#fff7ed;color:#c2410c;}
    .badge-status.rusak span.dot{background:#f97316;}

    .badge-status.dilaporkan{background:#f1f5f9;color:#475569;}
    .badge-status.dilaporkan span.dot{background:#64748b;}

    .badge-status.diproses{background:#eff6ff;color:#1d4ed8;}
    .badge-status.diproses span.dot{background:#2563eb;}

    .btn-pill-sm{
        border-radius:999px;
        font-size:.8rem;
        padding:.3rem .85rem;
    }

    .action-dropdown .btn-action-main{
        border-radius:999px 0 0 999px;
        font-size:.8rem;
        font-weight:600;
        padding:.38rem .85rem;
        white-space:nowrap;
        box-shadow:0 10px 22px rgba(37,99,235,.18);
    }

    .action-dropdown .btn-action-more{
        border-radius:0 999px 999px 0;
        font-size:.8rem;
        padding:.38rem .62rem;
        box-shadow:0 10px 22px rgba(37,99,235,.18);
    }

    .action-dropdown .dropdown-menu{
        min-width:235px;
        border:1px solid #e5e7eb;
        border-radius:14px;
        padding:8px;
        box-shadow:0 18px 44px rgba(15,23,42,.16);
    }

    .action-dropdown .dropdown-header{
        font-size:.72rem;
        font-weight:700;
        letter-spacing:.06em;
        text-transform:uppercase;
        color:#64748b;
        padding:6px 10px 8px;
    }

    .action-dropdown .dropdown-item,
    .action-dropdown .dropdown-action-btn{
        width:100%;
        border:0;
        background:transparent;
        border-radius:10px;
        padding:8px 10px;
        font-size:.85rem;
        color:#0f172a;
        text-align:left;
        display:flex;
        align-items:center;
        gap:8px;
        text-decoration:none;
        line-height:1.25;
    }

    .action-dropdown .dropdown-item:hover,
    .action-dropdown .dropdown-action-btn:hover{
        background:#f1f5f9;
        color:#0f172a;
    }

    .action-dropdown .dropdown-action-btn.action-warning,
    .action-dropdown .dropdown-item.action-warning{
        color:#b45309;
    }

    .action-dropdown .dropdown-action-btn.action-success,
    .action-dropdown .dropdown-item.action-success{
        color:#15803d;
    }

    .action-dropdown .dropdown-action-btn.action-danger,
    .action-dropdown .dropdown-item.action-danger{
        color:#dc2626;
    }

    .action-dropdown .dropdown-action-btn.action-primary,
    .action-dropdown .dropdown-item.action-primary{
        color:#2563eb;
    }

    .action-dropdown form{
        margin:0;
    }

    .tab-menu-card{
        border-radius:16px;
        border:1px solid var(--gray-border);
        box-shadow:0 6px 18px rgba(15,23,42,.05);
        background:#fff;
    }

    .tab-menu-header{
        border-bottom:1px solid var(--gray-border);
        padding:12px 16px;
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:12px;
        background:#f9fafb;
        border-radius:16px 16px 0 0;
    }

    .section-title{
        font-weight:600;
        display:flex;
        align-items:center;
        gap:.4rem;
    }

    .section-title span.emoji{
        font-size:1.3rem;
    }

    .table-report thead th{
        background:#eff6ff;
        border-bottom:1px solid var(--gray-border);
        font-size:.84rem;
    }

    .table-report tbody tr:hover{
        background:#f9fafb;
    }

    .filter-box{
        padding:12px 14px;
        border:1px solid #e5e7eb;
        border-radius:14px;
        background:#f8fafc;
        margin-bottom:14px;
    }

    .print-audit-header,
    .print-terlambat-header,
    .print-aktif-header,
    .print-issue-header{
        display:none;
    }

    .print-note-box{
        border:1px solid #d1d5db;
        border-radius:10px;
        padding:10px 12px;
        margin-top:10px;
        font-size:13px;
        line-height:1.55;
        background:#f9fafb;
    }

    .pagination{
        gap:6px;
        margin:10px 0 0;
        flex-wrap:wrap;
    }

    .pagination .page-item .page-link{
        border-radius:12px !important;
        padding:6px 10px !important;
        font-size:.85rem !important;
        line-height:1 !important;
        border:1px solid #e5e7eb !important;
        color:#0f172a !important;
        background:#fff !important;
        box-shadow:0 6px 16px rgba(15,23,42,.06);
        min-width:38px;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        text-decoration:none;
    }

    .pagination .page-item.active .page-link{
        background:linear-gradient(135deg, #2563eb, #4f46e5) !important;
        border-color:transparent !important;
        color:#fff !important;
        box-shadow:0 10px 22px rgba(37,99,235,.35);
    }

    .pagination .page-item.disabled .page-link{
        opacity:.55;
        box-shadow:none;
        pointer-events:none;
    }

    .pagination .page-link:hover{
        background:#f3f4f6 !important;
        border-color:#d1d5db !important;
    }

    @media print {
        .page-header-borrow,
        .borrow-kpi-grid,
        .tab-menu-header,
        #borrowTab,
        .btn,
        .dropdown,
        .btn-group,
        .pagination,
        .small-muted,
        .filter-box,
        .fine-kpi-grid,
        .issue-kpi-grid {
            display:none !important;
        }

        .tab-pane { display:none !important; }
        .tab-pane.active { display:block !important; }

        .print-audit-header,
        .print-terlambat-header,
        .print-aktif-header,
        .print-issue-header{
            display:block !important;
            margin-bottom:14px;
        }

        .print-hide{
            display:none !important;
        }

        body { background:#fff !important; }
        .tab-menu-card{ border:none !important; box-shadow:none !important; }
        .stat-card-borrow{ box-shadow:none !important; }

        .table-report,
        .table{
            width:100% !important;
        }

        .table-report thead th,
        .table-report tbody td,
        .table thead th,
        .table tbody td{
            font-size:12px !important;
            padding:6px 8px !important;
            vertical-align:top !important;
        }
    }
</style>

@php
    use App\Models\Borrowing;
    use App\Models\BookIssue;

    $filterStart = request('fine_start');
    $filterEnd   = request('fine_end');
    $activeTab   = request('active_tab', 'pengajuan');
    $searchPengajuan = request('search_pengajuan');
    $searchAktif = request('search_aktif');
    $searchTerlambat = request('search_terlambat');
    $searchIssue = request('search_issue');

    $openIssueBorrowingIds = BookIssue::whereIn('status', ['Dilaporkan', 'Diproses'])
        ->pluck('borrowing_id')
        ->toArray();

    $countIssueTotal = BookIssue::count();
    $countIssueLost = BookIssue::where('issue_type', 'Hilang')->count();
    $countIssueDamaged = BookIssue::where('issue_type', 'Rusak')->count();
    $countIssueOpen = BookIssue::whereIn('status', ['Dilaporkan', 'Diproses'])->count();

    $totalBorrow = Borrowing::whereIn('status', ['Dipinjam', 'Kembali', 'Terlambat'])->count();
    $countDiajukan = Borrowing::where('status', 'Diajukan')->count();

    $activeBorrow = Borrowing::where('status', 'Dipinjam')
        ->whereNull('return_date')
        ->count();

    $overdueBorrow = Borrowing::where('status', 'Terlambat')
        ->whereNull('return_date')
        ->count();

    $fineBaseQuery = Borrowing::with(['book','member'])
        ->where('fine_amount', '>', 0)
        ->where('fine_paid', true)
        ->when($filterStart, function ($q) use ($filterStart) {
            $q->whereDate('updated_at', '>=', $filterStart);
        })
        ->when($filterEnd, function ($q) use ($filterEnd) {
            $q->whereDate('updated_at', '<=', $filterEnd);
        });

    $countFineCases = (clone $fineBaseQuery)->count();
    $totalFinePaidOnly = (clone $fineBaseQuery)->sum('fine_amount');

    $pengajuan = Borrowing::with(['book', 'member'])
        ->where('status', 'Diajukan')
        ->when($searchPengajuan, function ($q) use ($searchPengajuan) {
            $q->where(function ($sub) use ($searchPengajuan) {
                $sub->where('student_name', 'like', '%' . $searchPengajuan . '%')
                    ->orWhere('student_nis', 'like', '%' . $searchPengajuan . '%')
                    ->orWhereHas('book', function ($book) use ($searchPengajuan) {
                        $book->where('book_code', 'like', '%' . $searchPengajuan . '%')
                            ->orWhere('title', 'like', '%' . $searchPengajuan . '%');
                    });
            });
        })
        ->orderByDesc('created_at')
        ->paginate(10, ['*'], 'pengajuan_page')
        ->appends(request()->query());

    $aktif = Borrowing::with(['book', 'member'])
        ->where('status', 'Dipinjam')
        ->whereNull('return_date')
        ->when($searchAktif, function ($q) use ($searchAktif) {
            $q->where(function ($sub) use ($searchAktif) {
                $sub->where('student_name', 'like', '%' . $searchAktif . '%')
                    ->orWhere('student_nis', 'like', '%' . $searchAktif . '%')
                    ->orWhereHas('book', function ($book) use ($searchAktif) {
                        $book->where('book_code', 'like', '%' . $searchAktif . '%')
                            ->orWhere('title', 'like', '%' . $searchAktif . '%');
                    });
            });
        })
        ->orderByDesc('created_at')
        ->paginate(10, ['*'], 'aktif_page')
        ->appends(request()->query());

    $terlambat = Borrowing::with(['book','member'])
        ->where('status', 'Terlambat')
        ->whereNull('return_date')
        ->when($searchTerlambat, function ($q) use ($searchTerlambat) {
            $q->where(function ($sub) use ($searchTerlambat) {
                $sub->where('student_name', 'like', '%' . $searchTerlambat . '%')
                    ->orWhere('student_nis', 'like', '%' . $searchTerlambat . '%')
                    ->orWhereHas('book', function ($book) use ($searchTerlambat) {
                        $book->where('book_code', 'like', '%' . $searchTerlambat . '%')
                            ->orWhere('title', 'like', '%' . $searchTerlambat . '%');
                    });
            });
        })
        ->orderByDesc('created_at')
        ->paginate(10, ['*'], 'terlambat_page')
        ->appends(request()->query());

    $laporanDenda = (clone $fineBaseQuery)
        ->orderByDesc('updated_at')
        ->paginate(10, ['*'], 'denda_page')
        ->appends(request()->query());

    $riwayat = Borrowing::with(['book', 'member'])
        ->where('status', 'Kembali')
        ->orderByDesc('created_at')
        ->paginate(10, ['*'], 'riwayat_page')
        ->appends(request()->query());

    $bookIssues = BookIssue::with(['book', 'borrowing'])
        ->when($searchIssue, function ($q) use ($searchIssue) {
            $q->where(function ($sub) use ($searchIssue) {
                $sub->where('student_name', 'like', '%' . $searchIssue . '%')
                    ->orWhere('student_nis', 'like', '%' . $searchIssue . '%')
                    ->orWhereHas('book', function ($book) use ($searchIssue) {
                        $book->where('book_code', 'like', '%' . $searchIssue . '%')
                            ->orWhere('title', 'like', '%' . $searchIssue . '%');
                    });
            });
        })
        ->orderByDesc('created_at')
        ->paginate(10, ['*'], 'issue_page')
        ->appends(request()->query());

    $renderBorrowPagination = function ($paginator, $activeTab = 'pengajuan') {
        if (!$paginator || !$paginator->hasPages()) {
            return '';
        }

        $html = '<nav aria-label="Pagination"><ul class="pagination">';

        if ($paginator->onFirstPage()) {
            $html .= '<li class="page-item disabled"><span class="page-link">&lsaquo;</span></li>';
        } else {
            $prevUrl = $paginator->previousPageUrl();
            $prevUrl .= (parse_url($prevUrl, PHP_URL_QUERY) ? '&' : '?') . 'active_tab=' . urlencode($activeTab);
            $prevUrl .= '#' . $activeTab;
            $html .= '<li class="page-item"><a class="page-link" href="' . $prevUrl . '">&lsaquo;</a></li>';
        }

        foreach ($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url) {
            $url .= (parse_url($url, PHP_URL_QUERY) ? '&' : '?') . 'active_tab=' . urlencode($activeTab);
            $url .= '#' . $activeTab;

            if ($page == $paginator->currentPage()) {
                $html .= '<li class="page-item active"><span class="page-link">' . $page . '</span></li>';
            } else {
                $html .= '<li class="page-item"><a class="page-link" href="' . $url . '">' . $page . '</a></li>';
            }
        }

        if ($paginator->hasMorePages()) {
            $nextUrl = $paginator->nextPageUrl();
            $nextUrl .= (parse_url($nextUrl, PHP_URL_QUERY) ? '&' : '?') . 'active_tab=' . urlencode($activeTab);
            $nextUrl .= '#' . $activeTab;
            $html .= '<li class="page-item"><a class="page-link" href="' . $nextUrl . '">&rsaquo;</a></li>';
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link">&rsaquo;</span></li>';
        }

        $html .= '</ul></nav>';

        return $html;
    };
@endphp

<div class="container py-4">

    <div class="page-header-borrow">
        <div class="page-header-left">
            <div class="page-icon">📘</div>
            <div>
                <div class="page-title">Kelola Peminjaman & Pengembalian</div>
                <div class="page-subtitle">
                    Halaman khusus petugas perpustakaan untuk mengelola pengajuan, peminjaman aktif, riwayat pengembalian, serta buku hilang/rusak.
                </div>
            </div>
        </div>

        <div class="d-flex flex-column flex-md-row gap-2">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">← Kembali ke Dashboard</a>
            <a href="{{ route('borrowings.create', ['active_tab' => 'aktif']) }}" class="btn btn-primary btn-sm">+ Tambah Peminjaman</a>
        </div>
    </div>

    <div class="borrow-kpi-grid">
        <div class="card stat-card-borrow">
            <div class="card-body">
                <div class="stat-label mb-1">Total Transaksi</div>
                <div class="stat-value text-primary mb-1">{{ $totalBorrow }}</div>
                <div class="small-muted">Transaksi yang sudah diproses (Dipinjam / Kembali / Terlambat).</div>
            </div>
        </div>

        <div class="card stat-card-borrow">
            <div class="card-body">
                <div class="stat-label mb-1">Pengajuan</div>
                <div class="stat-value mb-1" style="color: var(--orange-main);">{{ $countDiajukan }}</div>
                <div class="small-muted">Pengajuan menunggu diproses admin.</div>
            </div>
        </div>

        <div class="card stat-card-borrow">
            <div class="card-body">
                <div class="stat-label mb-1">Dipinjam (Aktif)</div>
                <div class="stat-value mb-1" style="color: var(--green-main);">{{ $activeBorrow }}</div>
                <div class="small-muted">Buku yang saat ini masih dipinjam siswa.</div>
            </div>
        </div>

        <div class="card stat-card-borrow">
            <div class="card-body">
                <div class="stat-label mb-1">Terlambat</div>
                <div class="stat-value mb-1" style="color: var(--red-main);">{{ $overdueBorrow }}</div>
                <div class="small-muted">Perlu perhatian untuk pengingat dan denda.</div>
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
                    Pengajuan, peminjaman aktif, keterlambatan, laporan denda, riwayat buku kembali, dan buku hilang/rusak.
                </div>
            </div>

            <div class="d-none d-md-block small-muted">
                Klik salah satu tab di bawah untuk mengganti kategori.
            </div>
        </div>

        <div class="px-3 pt-2">
            <ul class="nav nav-pills mb-3 mt-1" id="borrowTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'pengajuan' ? 'active' : '' }}" id="pengajuan-tab" data-bs-toggle="tab" data-bs-target="#pengajuan" type="button" role="tab">
                        🟠 Pengajuan
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'aktif' ? 'active' : '' }}" id="aktif-tab" data-bs-toggle="tab" data-bs-target="#aktif" type="button" role="tab">
                        ✅ Peminjaman Aktif
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'terlambat' ? 'active' : '' }}" id="terlambat-tab" data-bs-toggle="tab" data-bs-target="#terlambat" type="button" role="tab">
                        🚨 Terlambat
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'laporan-denda' ? 'active' : '' }}" id="laporan-denda-tab" data-bs-toggle="tab" data-bs-target="#laporan-denda" type="button" role="tab">
                        📑 Laporan Denda
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'riwayat' ? 'active' : '' }}" id="riwayat-tab" data-bs-toggle="tab" data-bs-target="#riwayat" type="button" role="tab">
                        📄 Riwayat (Buku Kembali)
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'issue' ? 'active' : '' }}" id="issue-tab" data-bs-toggle="tab" data-bs-target="#issue" type="button" role="tab">
                        📦 Buku Hilang / Rusak
                    </button>
                </li>
            </ul>
        </div>

        <div class="tab-content pb-3 px-2">

            {{-- PENGAJUAN --}}
            <div class="tab-pane fade {{ $activeTab === 'pengajuan' ? 'show active' : '' }}" id="pengajuan" role="tabpanel">
                <div class="px-2 pb-3">
                    <div class="small-muted mb-2">Daftar pengajuan menunggu diproses (setujui/tolak).</div>

                    <div class="filter-box">
                        <form method="GET" action="{{ route('borrowings.index') }}">
                            <input type="hidden" name="active_tab" value="pengajuan">

                            @if(request('search_aktif'))
                                <input type="hidden" name="search_aktif" value="{{ request('search_aktif') }}">
                            @endif

                            @if(request('search_terlambat'))
                                <input type="hidden" name="search_terlambat" value="{{ request('search_terlambat') }}">
                            @endif

                            @if(request('search_issue'))
                                <input type="hidden" name="search_issue" value="{{ request('search_issue') }}">
                            @endif

                            @if(request('fine_start'))
                                <input type="hidden" name="fine_start" value="{{ request('fine_start') }}">
                            @endif

                            @if(request('fine_end'))
                                <input type="hidden" name="fine_end" value="{{ request('fine_end') }}">
                            @endif

                            <div class="row g-2 align-items-end">
                                <div class="col-md-8">
                                    <label class="form-label small mb-1">Pencarian Pengajuan</label>
                                    <input type="text" name="search_pengajuan" class="form-control form-control-sm"
                                           value="{{ $searchPengajuan }}"
                                           placeholder="Cari nama siswa / NIS / ID buku / judul buku">
                                </div>

                                <div class="col-md-4 d-flex gap-2 flex-wrap">
                                    <button type="submit" class="btn btn-primary btn-sm">Cari</button>
                                    <a href="{{ route('borrowings.index', ['active_tab' => 'pengajuan']) }}#pengajuan" class="btn btn-outline-secondary btn-sm">Reset</a>
                                </div>
                            </div>
                        </form>
                    </div>

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
                                        $bookTitle = optional($borrowing->book)->title ?? 'Buku sudah dihapus';
                                        $bookAuthor = optional($borrowing->book)->author ?? '';
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
                                            {{ $bookTitle }}
                                            <div class="small-muted">{{ $bookAuthor }}</div>
                                        </td>
                                        <td>{{ $exp->timezone(config('app.timezone'))->locale('id')->translatedFormat('d F Y H:i') }}</td>
                                        <td>
                                            <span class="badge-status diajukan"><span class="dot"></span> Diajukan</span>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                <a href="{{ route('borrowings.edit', ['borrowing' => $borrowing->id, 'active_tab' => 'pengajuan']) }}" class="btn btn-success btn-pill-sm">
                                                    Setujui / Proses
                                                </a>

                                                <form action="{{ route('borrowings.destroy', $borrowing->id) }}" method="POST"
                                                      onsubmit="return confirm('Tolak pengajuan ini? Data akan dihapus.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="active_tab" value="pengajuan">
                                                    <button class="btn btn-outline-danger btn-pill-sm">Tolak</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">Belum ada pengajuan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-2">
                        {!! $renderBorrowPagination($pengajuan, 'pengajuan') !!}
                    </div>
                </div>
            </div>

            {{-- AKTIF --}}
            <div class="tab-pane fade {{ $activeTab === 'aktif' ? 'show active' : '' }}" id="aktif" role="tabpanel">
                <div class="px-2 pb-3">

                    <div class="print-aktif-header">
                        <h4 class="mb-1">Laporan Peminjaman Aktif</h4>
                        <div style="font-size:14px; margin-bottom:8px;">
                            Tanggal cetak:
                            <strong>{{ now()->locale('id')->translatedFormat('d F Y H:i') }}</strong>
                        </div>
                        <div class="print-note-box">
                            <strong>Keterangan:</strong>
                            daftar ini menampilkan buku yang sedang dipinjam dan belum dikembalikan.
                        </div>
                        <hr>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                        <div class="small-muted">Buku yang sedang dipinjam dan belum melewati jatuh tempo.</div>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="window.print()">🖨 Cetak Laporan</button>
                    </div>

                    <div class="filter-box">
                        <form method="GET" action="{{ route('borrowings.index') }}">
                            <input type="hidden" name="active_tab" value="aktif">

                            @if(request('search_pengajuan'))
                                <input type="hidden" name="search_pengajuan" value="{{ request('search_pengajuan') }}">
                            @endif

                            @if(request('search_terlambat'))
                                <input type="hidden" name="search_terlambat" value="{{ request('search_terlambat') }}">
                            @endif

                            @if(request('search_issue'))
                                <input type="hidden" name="search_issue" value="{{ request('search_issue') }}">
                            @endif

                            @if(request('fine_start'))
                                <input type="hidden" name="fine_start" value="{{ request('fine_start') }}">
                            @endif

                            @if(request('fine_end'))
                                <input type="hidden" name="fine_end" value="{{ request('fine_end') }}">
                            @endif

                            <div class="row g-2 align-items-end">
                                <div class="col-md-8">
                                    <label class="form-label small mb-1">Pencarian Peminjaman Aktif</label>
                                    <input type="text" name="search_aktif" class="form-control form-control-sm"
                                           value="{{ $searchAktif }}"
                                           placeholder="Cari nama siswa / NIS / ID buku / judul buku">
                                </div>

                                <div class="col-md-4 d-flex gap-2 flex-wrap">
                                    <button type="submit" class="btn btn-primary btn-sm">Cari</button>
                                    <a href="{{ route('borrowings.index', ['active_tab' => 'aktif']) }}#aktif" class="btn btn-outline-secondary btn-sm">Reset</a>
                                </div>
                            </div>
                        </form>
                    </div>

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
                                    <th class="print-hide" style="width:190px;">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($aktif as $borrowing)
                                    @php
                                        $nama  = $borrowing->student_name ?? optional($borrowing->member)->name;
                                        $nis   = $borrowing->student_nis ?? optional($borrowing->member)->nis;
                                        $kelas = $borrowing->student_class ?? optional($borrowing->member)->class;
                                        $bookCode = optional($borrowing->book)->book_code ?? '-';
                                        $bookTitle = optional($borrowing->book)->title ?? 'Buku sudah dihapus';
                                        $bookAuthor = optional($borrowing->book)->author ?? '';
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
                                            {{ $bookTitle }}
                                            <div class="small-muted">{{ $bookAuthor }}</div>
                                        </td>
                                        <td>
                                            @if($borrowing->due_date)
                                                {{ \Carbon\Carbon::parse($borrowing->due_date)->locale('id')->translatedFormat('d F Y') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge-status dipinjam"><span class="dot"></span> Dipinjam</span>
                                        </td>
                                        <td class="print-hide">
                                            <div class="btn-group action-dropdown">
                                                <button type="button" class="btn btn-primary btn-action-main dropdown-toggle" data-bs-toggle="dropdown" data-bs-boundary="viewport" aria-expanded="false">
                                                    Kelola
                                                </button>

                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <div class="dropdown-header">Aksi Peminjaman</div>

                                                    <a href="{{ route('borrowings.edit', ['borrowing' => $borrowing->id, 'active_tab' => 'aktif']) }}" class="dropdown-item action-success">
                                                        ✅ Tandai Kembali
                                                    </a>

                                                    <div class="dropdown-divider"></div>

                                                    @if(in_array($borrowing->id, $openIssueBorrowingIds))
                                                        <button type="button" class="dropdown-action-btn text-muted" disabled>
                                                            📌 Sudah Dilaporkan
                                                        </button>
                                                    @else
                                                        <a href="{{ route('book-issues.create', ['borrowing_id' => $borrowing->id]) }}" class="dropdown-item action-danger">
                                                            ⚠️ Laporkan Hilang/Rusak
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">Belum ada peminjaman aktif.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-2">
                        {!! $renderBorrowPagination($aktif, 'aktif') !!}
                    </div>
                </div>
            </div>

            {{-- TERLAMBAT --}}
            <div class="tab-pane fade {{ $activeTab === 'terlambat' ? 'show active' : '' }}" id="terlambat" role="tabpanel">
                <div class="px-2 pb-3">

                    <div class="print-terlambat-header">
                        <h4 class="mb-1">Laporan Peminjaman Terlambat</h4>
                        <div style="font-size:14px; margin-bottom:8px;">
                            Tanggal cetak:
                            <strong>{{ now()->locale('id')->translatedFormat('d F Y H:i') }}</strong>
                        </div>
                        <div class="print-note-box">
                            <strong>Ketentuan Peminjaman:</strong>
                            keterlambatan dikenakan denda <strong>Rp 2.000 / hari</strong>.
                        </div>
                        <hr>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                        <div class="small-muted">
                            Tab ini khusus tindakan petugas. Fokus untuk konfirmasi pembayaran denda dan proses pengembalian buku.
                        </div>
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="window.print()">🖨 Cetak Laporan</button>
                    </div>

                    <div class="filter-box">
                        <form method="GET" action="{{ route('borrowings.index') }}">
                            <input type="hidden" name="active_tab" value="terlambat">

                            @if(request('search_pengajuan'))
                                <input type="hidden" name="search_pengajuan" value="{{ request('search_pengajuan') }}">
                            @endif

                            @if(request('search_aktif'))
                                <input type="hidden" name="search_aktif" value="{{ request('search_aktif') }}">
                            @endif

                            @if(request('search_issue'))
                                <input type="hidden" name="search_issue" value="{{ request('search_issue') }}">
                            @endif

                            @if(request('fine_start'))
                                <input type="hidden" name="fine_start" value="{{ request('fine_start') }}">
                            @endif

                            @if(request('fine_end'))
                                <input type="hidden" name="fine_end" value="{{ request('fine_end') }}">
                            @endif

                            <div class="row g-2 align-items-end">
                                <div class="col-md-8">
                                    <label class="form-label small mb-1">Pencarian Keterlambatan</label>
                                    <input type="text" name="search_terlambat" class="form-control form-control-sm"
                                           value="{{ $searchTerlambat }}"
                                           placeholder="Cari nama siswa / NIS / ID buku / judul buku">
                                </div>

                                <div class="col-md-4 d-flex gap-2 flex-wrap">
                                    <button type="submit" class="btn btn-primary btn-sm">Cari</button>
                                    <a href="{{ route('borrowings.index', ['active_tab' => 'terlambat']) }}#terlambat" class="btn btn-outline-secondary btn-sm">Reset</a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-report align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width:70px;">No</th>
                                    <th style="width:130px;">ID Buku</th>
                                    <th>Judul Buku</th>
                                    <th>Nama Peminjam</th>
                                    <th style="width:130px;">NIS</th>
                                    <th style="width:90px;">Kelas</th>
                                    <th style="width:170px;">Jatuh Tempo</th>
                                    <th style="width:100px;">Hari Telat</th>
                                    <th style="width:140px;">Denda</th>
                                    <th style="width:140px;">Pembayaran</th>
                                    <th style="width:140px;">Status</th>
                                    <th class="print-hide" style="width:190px;">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($terlambat as $borrowing)
                                    @php
                                        $nama  = $borrowing->student_name ?? optional($borrowing->member)->name;
                                        $nis   = $borrowing->student_nis ?? optional($borrowing->member)->nis;
                                        $kelas = $borrowing->student_class ?? optional($borrowing->member)->class;
                                        $bookCode = optional($borrowing->book)->book_code ?? '-';
                                        $judul = optional($borrowing->book)->title ?? 'Buku sudah dihapus';
                                    @endphp

                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $bookCode }}</td>
                                        <td>{{ $judul }}</td>
                                        <td>{{ $nama ?? '—' }}</td>
                                        <td>{{ $nis ?? '-' }}</td>
                                        <td>{{ $kelas ?? '-' }}</td>
                                        <td>
                                            @if($borrowing->due_date)
                                                {{ \Carbon\Carbon::parse($borrowing->due_date)->locale('id')->translatedFormat('d F Y') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $borrowing->late_days ?? 0 }} hari</td>
                                        <td>Rp {{ number_format($borrowing->fine_amount ?? 0, 0, ',', '.') }}</td>
                                        <td>
                                            @if($borrowing->fine_paid)
                                                <span class="badge-status paid"><span class="dot"></span> Lunas</span>
                                            @else
                                                <span class="badge-status unpaid"><span class="dot"></span> Belum Bayar</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge-status terlambat"><span class="dot"></span> Terlambat</span>
                                        </td>
                                        <td class="print-hide">
                                            <div class="btn-group action-dropdown">
                                                <button type="button" class="btn btn-primary btn-action-main dropdown-toggle" data-bs-toggle="dropdown" data-bs-boundary="viewport" aria-expanded="false">
                                                    Tindakan
                                                </button>

                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <div class="dropdown-header">Aksi Keterlambatan</div>

                                                    @if(!$borrowing->fine_paid && ($borrowing->fine_amount ?? 0) > 0)
                                                        <form action="{{ route('borrowings.fine.paid', $borrowing->id) }}" method="POST"
                                                              onsubmit="return confirm('Konfirmasi denda sudah dibayar?');">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="active_tab" value="terlambat">
                                                            <input type="hidden" name="redirect_tab" value="terlambat">
                                                            <input type="hidden" name="fine_start" value="{{ $filterStart }}">
                                                            <input type="hidden" name="fine_end" value="{{ $filterEnd }}">
                                                            <button type="submit" class="dropdown-action-btn action-warning">
                                                                💰 Konfirmasi Lunas
                                                            </button>
                                                        </form>
                                                    @elseif($borrowing->fine_paid)
                                                        <form action="{{ route('borrowings.fine.unpaid', $borrowing->id) }}" method="POST"
                                                              onsubmit="return confirm('Ubah status jadi belum bayar lagi?');">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="active_tab" value="terlambat">
                                                            <input type="hidden" name="redirect_tab" value="terlambat">
                                                            <input type="hidden" name="fine_start" value="{{ $filterStart }}">
                                                            <input type="hidden" name="fine_end" value="{{ $filterEnd }}">
                                                            <button type="submit" class="dropdown-action-btn action-warning">
                                                                ↩️ Batalkan Lunas
                                                            </button>
                                                        </form>
                                                    @endif

                                                    <a href="{{ route('borrowings.edit', ['borrowing' => $borrowing->id, 'active_tab' => 'terlambat']) }}" class="dropdown-item action-success">
                                                        ✅ Tandai Kembali
                                                    </a>

                                                    <div class="dropdown-divider"></div>

                                                    @if(in_array($borrowing->id, $openIssueBorrowingIds))
                                                        <button type="button" class="dropdown-action-btn text-muted" disabled>
                                                            📌 Sudah Dilaporkan
                                                        </button>
                                                    @else
                                                        <a href="{{ route('book-issues.create', ['borrowing_id' => $borrowing->id]) }}" class="dropdown-item action-danger">
                                                            ⚠️ Laporkan Hilang/Rusak
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-center text-muted py-3">Belum ada data keterlambatan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-2">
                        {!! $renderBorrowPagination($terlambat, 'terlambat') !!}
                    </div>
                </div>
            </div>

            {{-- LAPORAN DENDA --}}
            <div class="tab-pane fade {{ $activeTab === 'laporan-denda' ? 'show active' : '' }}" id="laporan-denda" role="tabpanel">
                <div class="px-2 pb-3">

                    <div class="print-audit-header">
                        <h4 class="mb-1">Laporan Audit Denda Lunas</h4>
                        <div style="font-size: 14px;">
                            Periode:
                            <strong>
                                {{ $filterStart ? \Carbon\Carbon::parse($filterStart)->translatedFormat('d F Y') : 'Semua data' }}
                                s/d
                                {{ $filterEnd ? \Carbon\Carbon::parse($filterEnd)->translatedFormat('d F Y') : 'Sekarang' }}
                            </strong>
                        </div>

                        <div class="print-note-box">
                            <strong>Ketentuan Peminjaman:</strong>
                            keterlambatan dikenakan denda <strong>Rp 2.000 / hari</strong>.
                        </div>

                        <hr>

                        <div class="row" style="margin-bottom: 14px;">
                            <div class="col-6">
                                <strong>Total Kasus Lunas:</strong><br>
                                {{ $countFineCases }}
                            </div>

                            <div class="col-6">
                                <strong>Total Nominal Lunas:</strong><br>
                                Rp {{ number_format($totalFinePaidOnly, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>

                    <div class="small-muted mb-3">
                        Tab ini khusus audit denda yang <strong>sudah lunas</strong>. Data yang belum lunas tetap dipantau di tab <strong>Terlambat</strong>.
                    </div>

                    <div class="filter-box">
                        <form method="GET" action="{{ route('borrowings.index') }}">
                            <input type="hidden" name="active_tab" value="laporan-denda">

                            <div class="row g-2 align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label small mb-1">Tanggal Awal</label>
                                    <input type="date" name="fine_start" class="form-control form-control-sm" value="{{ $filterStart }}">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label small mb-1">Tanggal Akhir</label>
                                    <input type="date" name="fine_end" class="form-control form-control-sm" value="{{ $filterEnd }}">
                                </div>

                                <div class="col-md-6 d-flex gap-2 flex-wrap">
                                    <button type="submit" class="btn btn-dark btn-sm">Terapkan Filter</button>
                                    <a href="{{ route('borrowings.index', ['active_tab' => 'laporan-denda']) }}#laporan-denda" class="btn btn-outline-secondary btn-sm">Reset Filter</a>
                                    <button type="button" class="btn btn-outline-dark btn-sm" onclick="window.print()">🖨 Cetak Audit Denda</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="fine-kpi-grid">
                        <div class="card stat-card-borrow">
                            <div class="card-body">
                                <div class="stat-label mb-1">Kasus Denda Lunas (Periode)</div>
                                <div class="stat-value text-primary mb-1">{{ $countFineCases }}</div>
                                <div class="small-muted">Jumlah transaksi denda lunas sesuai filter tanggal.</div>
                            </div>
                        </div>

                        <div class="card stat-card-borrow">
                            <div class="card-body">
                                <div class="stat-label mb-1">Nominal Denda Lunas (Periode)</div>
                                <div class="stat-value mb-1" style="color: var(--green-main);">
                                    Rp {{ number_format($totalFinePaidOnly, 0, ',', '.') }}
                                </div>
                                <div class="small-muted">Total uang denda lunas pada periode yang dipilih.</div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-report align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width:70px;">No</th>
                                    <th style="width:130px;">ID Buku</th>
                                    <th>Judul Buku</th>
                                    <th>Nama Peminjam</th>
                                    <th style="width:130px;">NIS</th>
                                    <th style="width:90px;">Kelas</th>
                                    <th style="width:150px;">Jatuh Tempo</th>
                                    <th style="width:150px;">Tanggal Kembali</th>
                                    <th style="width:100px;">Hari Telat</th>
                                    <th style="width:120px;">Tarif/Hari</th>
                                    <th style="width:140px;">Total Denda</th>
                                    <th style="width:140px;">Pembayaran</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($laporanDenda as $borrowing)
                                    @php
                                        $nama  = $borrowing->student_name ?? optional($borrowing->member)->name;
                                        $nis   = $borrowing->student_nis ?? optional($borrowing->member)->nis;
                                        $kelas = $borrowing->student_class ?? optional($borrowing->member)->class;
                                        $bookCode = optional($borrowing->book)->book_code ?? '-';
                                        $judul = optional($borrowing->book)->title ?? 'Buku sudah dihapus';
                                    @endphp

                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $bookCode }}</td>
                                        <td>{{ $judul }}</td>
                                        <td>{{ $nama ?? '—' }}</td>
                                        <td>{{ $nis ?? '-' }}</td>
                                        <td>{{ $kelas ?? '-' }}</td>
                                        <td>
                                            @if($borrowing->due_date)
                                                {{ \Carbon\Carbon::parse($borrowing->due_date)->locale('id')->translatedFormat('d F Y') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($borrowing->return_date)
                                                {{ \Carbon\Carbon::parse($borrowing->return_date)->locale('id')->translatedFormat('d F Y') }}
                                            @else
                                                <span class="text-muted">Belum kembali</span>
                                            @endif
                                        </td>
                                        <td>{{ $borrowing->late_days ?? 0 }} hari</td>
                                        <td>Rp 2.000</td>
                                        <td>Rp {{ number_format($borrowing->fine_amount ?? 0, 0, ',', '.') }}</td>
                                        <td>
                                            <span class="badge-status paid"><span class="dot"></span> Lunas</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-center text-muted py-3">Belum ada data denda lunas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-2">
                        {!! $renderBorrowPagination($laporanDenda, 'laporan-denda') !!}
                    </div>
                </div>
            </div>

            {{-- RIWAYAT --}}
            <div class="tab-pane fade {{ $activeTab === 'riwayat' ? 'show active' : '' }}" id="riwayat" role="tabpanel">
                <div class="px-2 pb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                        <div class="small-muted">Riwayat buku yang sudah dikembalikan untuk laporan/cetak.</div>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="window.print()">🖨 Cetak Laporan</button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-report align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width:70px;">No</th>
                                    <th style="width:130px;">ID Buku</th>
                                    <th>Judul Buku</th>
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
                                        $judul = optional($borrowing->book)->title ?? 'Buku sudah dihapus';
                                    @endphp

                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $bookCode }}</td>
                                        <td>{{ $judul }}</td>
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
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-3">Belum ada riwayat pengembalian.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-2">
                        {!! $renderBorrowPagination($riwayat, 'riwayat') !!}
                    </div>
                </div>
            </div>

            {{-- ISSUE --}}
            <div class="tab-pane fade {{ $activeTab === 'issue' ? 'show active' : '' }}" id="issue" role="tabpanel">
                <div class="px-2 pb-3">

                    <div class="print-issue-header">
                        <h4 class="mb-1">Laporan Buku Hilang / Rusak</h4>
                        <div style="font-size:14px; margin-bottom:8px;">
                            Tanggal cetak:
                            <strong>{{ now()->locale('id')->translatedFormat('d F Y H:i') }}</strong>
                        </div>
                        <div class="print-note-box">
                            <strong>Keterangan:</strong>
                            daftar ini menampilkan kasus buku hilang atau rusak yang dilaporkan dari transaksi peminjaman aktif maupun terlambat.
                        </div>
                        <hr>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                        <div class="small-muted">
                            Data laporan buku hilang dan rusak yang berasal dari transaksi peminjaman aktif atau terlambat.
                        </div>
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="window.print()">🖨 Cetak Laporan</button>
                    </div>

                    <div class="filter-box">
                        <form method="GET" action="{{ route('borrowings.index') }}">
                            <input type="hidden" name="active_tab" value="issue">

                            @if(request('search_pengajuan'))
                                <input type="hidden" name="search_pengajuan" value="{{ request('search_pengajuan') }}">
                            @endif

                            @if(request('search_aktif'))
                                <input type="hidden" name="search_aktif" value="{{ request('search_aktif') }}">
                            @endif

                            @if(request('search_terlambat'))
                                <input type="hidden" name="search_terlambat" value="{{ request('search_terlambat') }}">
                            @endif

                            @if(request('fine_start'))
                                <input type="hidden" name="fine_start" value="{{ request('fine_start') }}">
                            @endif

                            @if(request('fine_end'))
                                <input type="hidden" name="fine_end" value="{{ request('fine_end') }}">
                            @endif

                            <div class="row g-2 align-items-end">
                                <div class="col-md-8">
                                    <label class="form-label small mb-1">Pencarian Buku Hilang / Rusak</label>
                                    <input type="text" name="search_issue" class="form-control form-control-sm"
                                           value="{{ $searchIssue }}"
                                           placeholder="Cari nama siswa / NIS / ID buku / judul buku">
                                </div>

                                <div class="col-md-4 d-flex gap-2 flex-wrap">
                                    <button type="submit" class="btn btn-primary btn-sm">Cari</button>
                                    <a href="{{ route('borrowings.index', ['active_tab' => 'issue']) }}#issue" class="btn btn-outline-secondary btn-sm">Reset</a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="issue-kpi-grid">
                        <div class="card stat-card-borrow">
                            <div class="card-body">
                                <div class="stat-label mb-1">Total Kasus</div>
                                <div class="stat-value text-primary mb-1">{{ $countIssueTotal }}</div>
                                <div class="small-muted">Semua laporan buku hilang dan rusak.</div>
                            </div>
                        </div>

                        <div class="card stat-card-borrow">
                            <div class="card-body">
                                <div class="stat-label mb-1">Buku Hilang</div>
                                <div class="stat-value mb-1" style="color: var(--red-main);">{{ $countIssueLost }}</div>
                                <div class="small-muted">Kasus buku yang dinyatakan hilang.</div>
                            </div>
                        </div>

                        <div class="card stat-card-borrow">
                            <div class="card-body">
                                <div class="stat-label mb-1">Buku Rusak</div>
                                <div class="stat-value mb-1" style="color: #f97316;">{{ $countIssueDamaged }}</div>
                                <div class="small-muted">Kasus buku yang rusak dan perlu tindak lanjut.</div>
                            </div>
                        </div>

                        <div class="card stat-card-borrow">
                            <div class="card-body">
                                <div class="stat-label mb-1">Belum Selesai</div>
                                <div class="stat-value mb-1" style="color: var(--green-main);">{{ $countIssueOpen }}</div>
                                <div class="small-muted">Status Dilaporkan atau Diproses.</div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-report align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width:70px;">No</th>
                                    <th style="width:140px;">Tanggal</th>
                                    <th>Nama Siswa</th>
                                    <th style="width:130px;">NIS</th>
                                    <th style="width:100px;">Kelas</th>
                                    <th>Judul Buku</th>
                                    <th style="width:120px;">Jenis</th>
                                    <th style="width:170px;">Denda / Penggantian</th>
                                    <th style="width:130px;">Status</th>
                                    <th class="print-hide" style="width:190px;">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($bookIssues as $issue)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ optional($issue->reported_at)->translatedFormat('d F Y') }}</td>
                                        <td>{{ $issue->student_name }}</td>
                                        <td>{{ $issue->student_nis }}</td>
                                        <td>{{ $issue->student_class }}</td>
                                        <td>{{ optional($issue->book)->title ?? 'Buku sudah dihapus' }}</td>
                                        <td>
                                            @if($issue->issue_type === 'Hilang')
                                                <span class="badge-status hilang"><span class="dot"></span> Hilang</span>
                                            @else
                                                <span class="badge-status rusak"><span class="dot"></span> Rusak</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $isBookReplaced =
                                                    (int) ($issue->replacement_required ?? 0) === 1 &&
                                                    (float) ($issue->fine_amount ?? 0) <= 0;
                                            @endphp

                                            @if($isBookReplaced)
                                                <span class="badge-status paid">
                                                    <span class="dot"></span> Buku Diganti
                                                </span>
                                            @else
                                                Rp {{ number_format($issue->fine_amount ?? 0, 0, ',', '.') }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($issue->status === 'Dilaporkan')
                                                <span class="badge-status dilaporkan"><span class="dot"></span> Dilaporkan</span>
                                            @elseif($issue->status === 'Diproses')
                                                <span class="badge-status diproses"><span class="dot"></span> Diproses</span>
                                            @else
                                                <span class="badge-status paid"><span class="dot"></span> Selesai</span>
                                            @endif
                                        </td>
                                        <td class="print-hide">
                                            <div class="btn-group action-dropdown">
                                                <button type="button" class="btn btn-primary btn-action-main dropdown-toggle" data-bs-toggle="dropdown" data-bs-boundary="viewport" aria-expanded="false">
                                                    Kelola
                                                </button>

                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <div class="dropdown-header">Aksi Kasus Buku</div>

                                                    <a href="{{ route('book-issues.edit', $issue->id) }}" class="dropdown-item action-primary">
                                                        ✏️ Edit Kasus
                                                    </a>

                                                    @if($issue->status === 'Dilaporkan')
                                                        <form action="{{ route('book-issues.processing', $issue->id) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="dropdown-action-btn action-warning">
                                                                🔄 Tandai Diproses
                                                            </button>
                                                        </form>
                                                    @endif

                                                    @if($issue->status !== 'Selesai')
                                                        <form action="{{ route('book-issues.finished', $issue->id) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="dropdown-action-btn action-success">
                                                                ✅ Tandai Selesai
                                                            </button>
                                                        </form>
                                                    @endif

                                                    <div class="dropdown-divider"></div>

                                                    <form action="{{ route('book-issues.destroy', $issue->id) }}" method="POST"
                                                          onsubmit="return confirm('Hapus kasus ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-action-btn action-danger">
                                                            🗑️ Hapus Kasus
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center text-muted py-4">Belum ada data buku hilang / rusak.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-2">
                        {!! $renderBorrowPagination($bookIssues, 'issue') !!}
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    function activateTabFromQueryOrHash() {
        const url = new URL(window.location.href);
        const activeTab = url.searchParams.get('active_tab');
        const hash = window.location.hash;

        let target = '#pengajuan';

        if (activeTab) {
            target = '#' + activeTab;
        } else if (hash) {
            target = hash;
        }

        const tabBtn = document.querySelector(`[data-bs-target="${target}"]`);
        if (tabBtn) {
            const tab = new bootstrap.Tab(tabBtn);
            tab.show();
        }
    }

    document.querySelectorAll('#borrowTab button[data-bs-toggle="tab"]').forEach(btn => {
        btn.addEventListener('shown.bs.tab', function (e) {
            const target = e.target.getAttribute('data-bs-target');
            if (!target) return;

            const tabName = target.replace('#', '');
            const url = new URL(window.location.href);
            url.searchParams.set('active_tab', tabName);
            url.hash = tabName;
            history.replaceState({}, '', url.toString());

            patchPaginationLinks();
            patchForms();
        });
    });

    function patchPaginationLinks() {
        const currentUrl = new URL(window.location.href);
        const activeTab = currentUrl.searchParams.get('active_tab') || 'pengajuan';

        document.querySelectorAll('.pagination a.page-link').forEach(a => {
            const pageUrl = new URL(a.href);

            pageUrl.searchParams.set('active_tab', activeTab);

            const fineStart = currentUrl.searchParams.get('fine_start');
            const fineEnd = currentUrl.searchParams.get('fine_end');
            const searchPengajuan = currentUrl.searchParams.get('search_pengajuan');
            const searchAktif = currentUrl.searchParams.get('search_aktif');
            const searchTerlambat = currentUrl.searchParams.get('search_terlambat');
            const searchIssue = currentUrl.searchParams.get('search_issue');

            if (fineStart) {
                pageUrl.searchParams.set('fine_start', fineStart);
            } else {
                pageUrl.searchParams.delete('fine_start');
            }

            if (fineEnd) {
                pageUrl.searchParams.set('fine_end', fineEnd);
            } else {
                pageUrl.searchParams.delete('fine_end');
            }

            if (searchPengajuan) {
                pageUrl.searchParams.set('search_pengajuan', searchPengajuan);
            } else {
                pageUrl.searchParams.delete('search_pengajuan');
            }

            if (searchAktif) {
                pageUrl.searchParams.set('search_aktif', searchAktif);
            } else {
                pageUrl.searchParams.delete('search_aktif');
            }

            if (searchTerlambat) {
                pageUrl.searchParams.set('search_terlambat', searchTerlambat);
            } else {
                pageUrl.searchParams.delete('search_terlambat');
            }

            if (searchIssue) {
                pageUrl.searchParams.set('search_issue', searchIssue);
            } else {
                pageUrl.searchParams.delete('search_issue');
            }

            pageUrl.hash = activeTab;
            a.href = pageUrl.toString();
        });
    }

    function patchForms() {
        const currentUrl = new URL(window.location.href);
        const activeTab = currentUrl.searchParams.get('active_tab') || 'pengajuan';

        document.querySelectorAll('form').forEach(form => {
            let input = form.querySelector('input[name="active_tab"]');

            if (!input) {
                input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'active_tab';
                form.appendChild(input);
            }

            if (!form.querySelector('input[name="redirect_tab"]')) {
                input.value = activeTab;
            }
        });
    }

    activateTabFromQueryOrHash();
    patchPaginationLinks();
    patchForms();
});
</script>
@endsection

@extends('layouts.app')

@section('title', 'Pengajuan Kadaluarsa')

@section('content')
@php
    // $expired dari controller
@endphp

<style>
    /* ====== PAGE LOOK (modern, clean) ====== */
    body {
        background:
            radial-gradient(circle at top left, #dbeafe 0, #eff6ff 28%, transparent 55%),
            radial-gradient(circle at bottom right, #e5e7eb 0, #f9fafb 40%, #e5e7eb 100%);
    }

    .page-wrap {
        max-width: 1150px;
        margin: 22px auto 40px;
        padding: 0 10px;
    }

    /* HEADER */
    .hero {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        border-radius: 18px;
        padding: 18px 20px;
        color: #fff;
        box-shadow: 0 16px 38px rgba(37, 99, 235, 0.32);
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        position: relative;
        overflow: hidden;
        margin-bottom: 14px;
    }
    .hero::after{
        content:"";
        position:absolute;
        width:280px;height:280px;
        border-radius:999px;
        background:rgba(255,255,255,.14);
        right:-90px; top:-90px;
    }
    .hero-left { position: relative; z-index: 2; display:flex; gap:12px; align-items:center; }
    .hero-icon {
        width:48px;height:48px;border-radius:16px;
        background:rgba(15,23,42,.18);
        display:flex;align-items:center;justify-content:center;
        font-size:1.6rem;
    }
    .hero-title { font-weight:800; font-size:1.4rem; margin-bottom:2px; }
    .hero-sub { opacity:.95; font-size:.9rem; max-width:700px; }
    .hero-right { position: relative; z-index: 2; text-align:right; }
    .hero-badge {
        display:inline-flex; gap:.45rem; align-items:center;
        background: rgba(15,23,42,.16);
        padding: 6px 12px; border-radius:999px;
        font-size:.8rem;
        margin-bottom:6px;
    }

    /* CARD */
    .card-box {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 10px 22px rgba(15,23,42,.06);
        overflow: hidden;
    }

    .card-head {
        padding: 14px 16px;
        border-bottom: 1px solid #eef2f7;
        display: flex;
        justify-content: space-between;
        gap: 10px;
        align-items: center;
    }

    .head-left .h {
        font-weight: 800;
        font-size: 1.05rem;
        color: #111827;
        margin: 0;
        display:flex;
        gap:.45rem;
        align-items:center;
    }
    .head-left .p {
        margin: 4px 0 0;
        font-size: .85rem;
        color: #6b7280;
    }

    .head-actions { display:flex; gap:8px; align-items:center; flex-wrap:wrap; }

    .btnx {
        border: none;
        border-radius: 12px;
        padding: 10px 12px;
        font-weight: 700;
        font-size: .9rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        cursor: pointer;
        transition: transform .12s ease, filter .12s ease, box-shadow .12s ease;
        box-shadow: 0 10px 18px rgba(15,23,42,.08);
    }
    .btnx:hover { transform: translateY(-1px); filter: brightness(1.03); text-decoration:none; }

    .btnx-back { background: linear-gradient(135deg, #e5e7eb, #f3f4f6); color:#111827; }
    .btnx-refresh { background: linear-gradient(135deg, #22c55e, #16a34a); color:#052e16; }

    /* ✅ tombol hapus semua */
    .btnx-danger {
        background: linear-gradient(135deg, #ef4444, #b91c1c);
        color: #fff;
    }

    /* ALERT */
    .alertx {
        margin: 14px 16px 0;
        padding: 12px 14px;
        border-radius: 14px;
        font-weight: 600;
        font-size: .9rem;
        border: 1px solid transparent;
    }
    .alertx-success { background:#ecfdf5; border-color:#bbf7d0; color:#065f46; }
    .alertx-danger { background:#fef2f2; border-color:#fecaca; color:#991b1b; }

    /* TABLE */
    .table-wrap { padding: 14px 16px 16px; }
    .table-modern {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        overflow: hidden;
        border-radius: 14px;
        border: 1px solid #e5e7eb;
    }
    .table-modern thead th {
        background: #f9fafb;
        color: #374151;
        font-size: .78rem;
        text-transform: uppercase;
        letter-spacing: .06em;
        padding: 12px 12px;
        border-bottom: 1px solid #e5e7eb;
        white-space: nowrap;
    }
    .table-modern tbody td {
        padding: 12px 12px;
        border-bottom: 1px solid #eef2f7;
        vertical-align: top;
        color: #111827;
        font-size: .9rem;
    }
    .table-modern tbody tr:hover { background: #fafafa; }
    .muted { color:#6b7280; font-size:.82rem; }

    /* BADGE */
    .badgex {
        display:inline-flex; align-items:center; gap:.35rem;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: .78rem;
        font-weight: 800;
        border: 1px solid transparent;
        white-space: nowrap;
    }
    .badgex-expired { background:#fef2f2; border-color:#fecaca; color:#991b1b; }
    .badgex-soft { background:#fffbeb; border-color:#fde68a; color:#92400e; }

    /* ACTION BUTTONS inside table */
    .btn-mini {
        border: none;
        padding: 8px 10px;
        border-radius: 10px;
        font-weight: 800;
        font-size: .82rem;
        cursor: pointer;
        display: inline-flex;
        gap: .35rem;
        align-items: center;
        text-decoration: none;
        transition: transform .12s ease, filter .12s ease;
    }
    .btn-mini:hover { transform: translateY(-1px); filter: brightness(1.03); text-decoration:none; }

    .btn-restore { background: linear-gradient(135deg, #dbeafe, #bfdbfe); color:#1d4ed8; }
    .btn-delete { background: linear-gradient(135deg, #fee2e2, #fecaca); color:#b91c1c; }

    .actions-inline { display:flex; gap:8px; flex-wrap:wrap; }

    /* EMPTY STATE */
    .empty {
        border: 1px dashed #d1d5db;
        background: #f9fafb;
        border-radius: 16px;
        padding: 30px 16px;
        text-align: center;
        color: #6b7280;
    }
    .empty .big { font-size: 2rem; margin-bottom: 6px; }
    .empty .t { font-weight: 900; color:#111827; margin-bottom: 4px; }
    .empty .s { font-size:.9rem; }

    /* PAGINATION */
    .pager { margin-top: 14px; display:flex; justify-content:flex-end; }
</style>

<div class="page-wrap">

    <div class="hero">
        <div class="hero-left">
            <div class="hero-icon">🗂️</div>
            <div>
                <div class="hero-title">Data Pengajuan Kadaluarsa</div>
                <div class="hero-sub">
                    Ini adalah pengajuan status <b>Diajukan</b> yang lewat <b>5 hari</b> dan sudah masuk <b>Soft Delete</b>.
                </div>
            </div>
        </div>
        <div class="hero-right">
            {{-- ✅ TETAP TAMPIL TANGGAL (tanpa jam) --}}
            <div class="hero-badge">🗓️ {{ now()->translatedFormat('d F Y') }}</div>
            <a href="{{ route('admin.dashboard') }}" class="btnx btnx-back">⬅️ Kembali</a>
        </div>
    </div>

    <div class="card-box">

        <div class="card-head">
            <div class="head-left">
                <p class="h">📌 Ringkasan</p>
                <p class="p">
                    Total data kadaluarsa: <b>{{ $expired->total() }}</b>
                    <span class="muted">• data ini bisa kamu pulihkan (restore) atau hapus permanen</span>
                </p>
            </div>

            <div class="head-actions">
                {{-- ✅ tombol hapus semua (muncul hanya kalau ada data) --}}
                @if($expired->total() > 0)
                    <form
                        action="{{ route('admin.borrowings.expired.forceDeleteAll') }}"
                        method="POST"
                        onsubmit="return confirm('Yakin hapus SEMUA pengajuan kadaluarsa? Ini tidak bisa dibatalkan!');"
                        style="display:inline;"
                    >
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btnx btnx-danger">🗑️ Hapus Semua</button>
                    </form>
                @endif

                <a class="btnx btnx-refresh" href="{{ url()->current() }}">🔄 Refresh</a>
            </div>
        </div>

        @if (session('success'))
            <div class="alertx alertx-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alertx alertx-danger">{{ session('error') }}</div>
        @endif

        <div class="table-wrap">
            @if($expired->count() === 0)
                <div class="empty">
                    <div class="big">✅</div>
                    <div class="t">Tidak ada pengajuan kadaluarsa</div>
                    <div class="s">Kalau ada yang lewat 2 hari, data akan otomatis masuk ke sini.</div>
                </div>
            @else
                <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:12px;">
                    <span class="badgex badgex-expired">⛔ Kadaluarsa</span>
                    <span class="badgex badgex-soft">🧺 Soft Deleted</span>
                </div>

                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Siswa</th>
                            <th>Buku</th>
                            <th>Status</th>
                            <th>Dihapus</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expired as $row)
                            <tr>
                                <td><b>#{{ $row->id }}</b></td>
                                <td>
                                    <div style="font-weight:800;">{{ $row->student_name }}</div>
                                    <div class="muted">NIS: {{ $row->student_nis }} • Kelas: {{ $row->student_class }}</div>
                                </td>
                                <td>
                                    <div style="font-weight:800;">
                                        {{ optional($row->book)->title ?? '-' }}
                                    </div>
                                    <div class="muted">
                                        ID Buku: {{ $row->book_id ?? '-' }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badgex badgex-expired">Diajukan</span>
                                </td>
                                <td>
                                    <div style="font-weight:800;">
                                        {{ optional($row->deleted_at)->translatedFormat('d F Y') ?? '-' }}
                                    </div>
                                    {{-- ✅ JAM DIHILANGKAN --}}
                                </td>
                                <td>
                                    <div class="actions-inline">
                                        {{-- RESTORE --}}
                                        <form action="{{ route('admin.borrowings.expired.restore', $row->id) }}" method="POST">
                                            @csrf
                                            <button class="btn-mini btn-restore" type="submit">♻️ Restore</button>
                                        </form>

                                        {{-- DELETE PERMANEN --}}
                                        <form action="{{ route('admin.borrowings.expired.forceDelete', $row->id) }}" method="POST"
                                              onsubmit="return confirm('Yakin hapus permanen data ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn-mini btn-delete" type="submit">🗑️ Hapus Permanen</button>
                                        </form>
                                    </div>
                                    <div class="muted" style="margin-top:6px;">
                                        * Restore akan mengembalikan data ke daftar peminjaman.
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="pager">
                    {{ $expired->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection

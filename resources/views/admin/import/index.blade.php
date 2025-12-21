{{-- resources/views/admin/import/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Import Data dari Excel')

@section('content')
<style>
    body {
        background: radial-gradient(circle at top left, #dbeafe 0, #eff6ff 35%, #f9fafb 100%);
    }

    .import-wrapper {
        max-width: 1200px;
        margin: 24px auto 40px;
        padding: 0 16px;
    }

    .import-hero {
        border-radius: 22px;
        padding: 18px 24px;
        background: linear-gradient(135deg, #4f46e5, #6366f1, #06b6d4);
        color: #fff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 18px;
        box-shadow: 0 22px 45px rgba(15, 23, 42, 0.35);
        margin-bottom: 26px;
    }

    .import-hero-left {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .hero-icon {
        width: 60px;
        height: 60px;
        border-radius: 20px;
        background: radial-gradient(circle at 30% 0, #e0f2fe 0, #38bdf8 45%, #0f172a 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        box-shadow: 0 18px 38px rgba(8, 47, 73, .75);
    }

    .hero-title {
        font-size: 1.6rem;
        font-weight: 700;
        letter-spacing: .03em;
    }

    .hero-subtitle {
        font-size: .92rem;
        opacity: .95;
    }

    .import-btn-back {
        border-radius: 999px;
        padding: .4rem 1.3rem;
        border: none;
        background: rgba(15, 23, 42, .13);
        color: #e5e7eb;
        font-size: .85rem;
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        text-decoration: none;
    }

    .import-btn-back:hover {
        background: rgba(15, 23, 42, .22);
        color: #fff;
    }

    .import-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 18px;
        margin-bottom: 22px;
    }

    .import-card {
        border-radius: 18px;
        border: 1px solid #e5e7eb;
        background: #ffffff;
        box-shadow: 0 14px 30px rgba(15, 23, 42, .05);
        padding: 18px 18px 16px;
        position: relative;
        overflow: hidden;
    }

    .import-card::before {
        content: "";
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at top right, rgba(96, 165, 250,.18), transparent 60%);
        opacity: .6;
        pointer-events: none;
    }

    .import-card-header {
        position: relative;
        z-index: 1;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .import-card-title {
        font-weight: 600;
        font-size: 1.05rem;
        display: flex;
        align-items: center;
        gap: .45rem;
        color: #0f172a;
    }

    .import-card-title span.icon {
        font-size: 1.2rem;
    }

    .import-card-tag {
        font-size: .72rem;
        text-transform: uppercase;
        letter-spacing: .08em;
        padding: .12rem .6rem;
        border-radius: 999px;
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }

    .import-card-body {
        position: relative;
        z-index: 1;
    }

    .import-note {
        font-size: .78rem;
        color: #6b7280;
        margin-bottom: 10px;
    }

    .import-note strong {
        color: #111827;
    }

    .btn-pill {
        border-radius: 999px;
        padding-inline: 1rem;
        font-size: .85rem;
    }

    .logs-card {
        border-radius: 18px;
        border: 1px solid #e5e7eb;
        background: #ffffff;
        box-shadow: 0 12px 26px rgba(15, 23, 42, .06);
        padding: 14px 16px 10px;
    }

    .logs-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .logs-title {
        font-weight: 600;
        font-size: .95rem;
        display: flex;
        align-items: center;
        gap: .4rem;
        color: #0f172a;
    }

    .badge-type {
        border-radius: 999px;
        padding: .12rem .6rem;
        font-size: .72rem;
        font-weight: 500;
    }

    .badge-books {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .badge-members {
        background: #dcfce7;
        color: #166534;
    }

    .logs-table td {
        font-size: .82rem;
        vertical-align: middle;
    }

    .logs-table small {
        color: #6b7280;
    }

    .btn-soft-danger {
        border-radius: 999px;
        padding: .24rem .75rem;
        font-size: .78rem;
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .btn-soft-danger:hover {
        background: #fee2e2;
        color: #991b1b;
    }
</style>

<div class="import-wrapper">

    {{-- HERO --}}
    <div class="import-hero">
        <div class="import-hero-left">
            <div class="hero-icon">📂</div>
            <div>
                <div class="hero-title">Import Data dari Excel</div>
                <div class="hero-subtitle">
                    Upload file Excel untuk menambah atau memperbarui data buku dan anggota perpustakaan.
                </div>
            </div>
        </div>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="import-btn-back">
                ← Kembali ke Dashboard
            </a>
        </div>
    </div>

    {{-- FLASH MESSAGE --}}
    @if(session('success'))
        <div class="alert alert-success py-2">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger py-2">
            {{ session('error') }}
        </div>
    @endif

    {{-- GRID IMPORT --}}
    <div class="import-grid">

        {{-- CARD BUKU --}}
        <div class="import-card">
            <div class="import-card-header">
                <div class="import-card-title">
                    <span class="icon">📘</span>
                    <span>Import Data Buku</span>
                </div>
                <span class="import-card-tag">Sheet Buku</span>
            </div>
            <div class="import-card-body">
                <p class="import-note">
                    Kolom disarankan:<br>
                    <strong>A</strong> No (optional), <strong>B</strong> Cover (nama file atau URL),<br>
                    <strong>C</strong> Judul, <strong>D</strong> Deskripsi,<br>
                    <strong>E</strong> Pengarang, <strong>F</strong> Penerbit,<br>
                    <strong>G</strong> Tahun, <strong>H</strong> Stok.
                </p>

                <form method="POST" action="{{ route('admin.import.books') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-2">
                        <input type="file" name="file_books" class="form-control form-control-sm" required>
                        @error('file_books')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>
                    <button class="btn btn-primary btn-pill" type="submit">
                        Import Buku
                    </button>
                </form>
            </div>
        </div>

        {{-- CARD ANGGOTA --}}
        <div class="import-card">
            <div class="import-card-header">
                <div class="import-card-title">
                    <span class="icon">🧑‍🎓</span>
                    <span>Import Data Anggota / Siswa</span>
                </div>
                <span class="import-card-tag">Sheet Anggota</span>
            </div>
            <div class="import-card-body">
                <p class="import-note">
                    Kolom disarankan:
                    <strong>A</strong> NIS, <strong>B</strong> Nama,
                    <strong>C</strong> Kelas, <strong>D</strong> Jenis Kelamin,
                    <strong>E</strong> No. HP, <strong>F</strong> Alamat.
                </p>

                <form method="POST" action="{{ route('admin.import.members') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-2">
                        <input type="file" name="file_members" class="form-control form-control-sm" required>
                        @error('file_members')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>
                    <button class="btn btn-success btn-pill" type="submit">
                        Import Anggota
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- RIWAYAT IMPORT --}}
    <div class="logs-card mt-3">
        <div class="logs-header">
            <div class="logs-title">
                <span>🕒</span>
                <span>Riwayat Import Terakhir</span>
            </div>
            <small class="text-muted">Maks. 10 batch terakhir</small>
        </div>

        @if($logs->isEmpty())
            <p class="mb-0 text-muted" style="font-size:.82rem;">
                Belum ada riwayat import.
            </p>
        @else
            <div class="table-responsive">
                <table class="table table-sm mb-0 logs-table align-middle">
                    <tbody>
                    @foreach($logs as $log)
                        <tr>
                            <td style="width: 140px;">
                                @if($log->type === 'books')
                                    <span class="badge-type badge-books">Buku</span>
                                @else
                                    <span class="badge-type badge-members">Anggota</span>
                                @endif
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $log->file_name }}</strong>
                                </div>
                                <small>
                                    {{ $log->created_count }} data &middot;
                                    {{ $log->imported_at?->format('d M Y H:i') }}
                                </small>
                            </td>
                            <td class="text-end" style="width: 150px;">
                                <form method="POST" action="{{ route('admin.import.logs.destroy', $log->id) }}"
                                      onsubmit="return confirm('Hapus batch ini dan seluruh data yang dibuat dari Excel ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-soft-danger">
                                        Hapus Batch
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection

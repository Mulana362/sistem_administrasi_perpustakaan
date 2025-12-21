{{-- resources/views/student/borrow/status.blade.php --}}
@extends('layouts.app')

@section('title', 'Cek Status Peminjaman')

@section('content')
@php
    // biar gampang pakai Carbon::parse()
    use Carbon\Carbon;
@endphp

<style>
    body {
        background:
            radial-gradient(circle at top left, #e0f2fe 0, #eff6ff 45%, #f9fafb 100%);
    }

    .status-wrapper {
        max-width: 1200px;
        margin: 26px auto 40px;
        padding: 0 16px;
        font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    }

    /* HERO CARD */
    .status-hero {
        border-radius: 22px;
        padding: 20px 26px;
        background: linear-gradient(115deg, #1d4ed8, #4f46e5, #06b6d4);
        color: #fff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 18px;
        box-shadow: 0 22px 45px rgba(15,23,42,0.35);
        margin-bottom: 26px;
    }

    .status-hero-left {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .status-hero-icon {
        width: 64px;
        height: 64px;
        border-radius: 20px;
        background: radial-gradient(circle at 30% 0, #e0f2fe 0, #38bdf8 40%, #0f172a 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.1rem;
        box-shadow: 0 18px 40px rgba(15,23,42,.75);
    }

    .status-hero-title {
        font-size: 1.7rem;
        font-weight: 700;
        letter-spacing: .03em;
        margin-bottom: 4px;
    }

    .status-hero-sub {
        font-size: .9rem;
        opacity: .95;
    }

    .btn-hero-back {
        border-radius: 999px;
        padding: .45rem 1.4rem;
        border: none;
        background: rgba(15,23,42,.17);
        color: #e5e7eb;
        font-size: .85rem;
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        text-decoration: none;
    }

    .btn-hero-back:hover {
        background: rgba(15,23,42,.26);
        color: #fff;
    }

    /* KARTU UTAMA */
    .status-card {
        border-radius: 20px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        box-shadow: 0 16px 38px rgba(15,23,42,.08);
        padding: 18px 20px 16px;
    }

    .status-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .status-card-title {
        font-size: 1.05rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: .6rem;
        color: #0f172a;
    }

    .status-dot {
        width: 11px;
        height: 11px;
        border-radius: 999px;
        background: #22c55e;
        box-shadow: 0 0 0 6px rgba(34,197,94,.25);
    }

    .status-nis-text {
        font-size: .82rem;
        color: #6b7280;
    }

    /* FORM NIS */
    .status-form-row {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 14px;
        align-items: center;
    }

    .status-input {
        flex: 1 1 260px;
        border-radius: 999px;
        padding: .55rem 1.2rem;
        font-size: .92rem;
        border: 1px solid #d1d5db;
        outline: none;
    }

    .status-input:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 2px rgba(79,70,229,.2);
    }

    .status-btn-submit {
        border-radius: 999px;
        padding: .55rem 1.6rem;
        border: none;
        background: linear-gradient(135deg, #2563eb, #4f46e5);
        color: #fff;
        font-size: .92rem;
        font-weight: 600;
        box-shadow: 0 12px 30px rgba(37,99,235,.4);
    }

    .status-btn-submit:hover {
        filter: brightness(1.05);
    }

    .status-hint {
        font-size: .78rem;
        color: #6b7280;
        margin-bottom: 12px;
    }

    /* TABEL */
    .status-table-wrapper {
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
    }

    .status-table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: collapse;
        font-size: .86rem;
    }

    .status-table thead {
        background: linear-gradient(90deg, #eff6ff, #e0f2fe);
    }

    .status-table thead th {
        padding: .6rem .9rem;
        font-weight: 600;
        color: #4b5563;
        border-bottom: 1px solid #e5e7eb;
    }

    .status-table tbody td {
        padding: .55rem .9rem;
        border-bottom: 1px solid #f3f4f6;
        background: #ffffff;
    }

    .status-table tbody tr:nth-child(even) td {
        background: #f9fafb;
    }

    .status-table tbody tr:hover td {
        background: #eef2ff;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .22rem .8rem;
        border-radius: 999px;
        font-size: .78rem;
        font-weight: 500;
    }

    .pill-active {
        background: #dcfce7;
        color: #166534;
    }

    .pill-returned {
        background: #e5e7eb;
        color: #374151;
    }

    .pill-dot {
        width: 7px;
        height: 7px;
        border-radius: 999px;
        background: currentColor;
    }

    .status-empty-text {
        font-size: .84rem;
        color: #6b7280;
    }

    @media (max-width: 768px) {
        .status-hero {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<div class="status-wrapper">

    {{-- HERO --}}
    <div class="status-hero">
        <div class="status-hero-left">
            <div class="status-hero-icon">
                📚
            </div>
            <div>
                <div class="status-hero-title">Cek Status Peminjaman</div>
                <div class="status-hero-sub">
                    Masukkan NIS untuk melihat buku yang sedang atau pernah Anda pinjam.
                </div>
            </div>
        </div>

        <div>
            <a href="{{ route('catalog') }}" class="btn-hero-back">
                ← Kembali ke Katalog
            </a>
        </div>
    </div>

    {{-- KARTU UTAMA --}}
    <div class="status-card">
        <div class="status-card-header">
            <div class="status-card-title">
                <span class="status-dot"></span>
                <span>Riwayat Peminjaman</span>
            </div>
            @if(!empty($nis))
                <span class="status-nis-text">NIS: <strong>{{ $nis }}</strong></span>
            @endif
        </div>

        {{-- FORM NIS --}}
        <form method="GET" action="{{ route('student.borrow.status') }}">
            <div class="status-form-row">
                <input
                    type="text"
                    name="nis"
                    class="status-input"
                    placeholder="Masukkan NIS siswa"
                    value="{{ old('nis', $nis ?? '') }}"
                >
                <button type="submit" class="status-btn-submit">
                    Cek Status
                </button>
            </div>
        </form>

        <div class="status-hint">
            Tekan Enter setelah mengetik NIS, atau klik tombol <strong>Cek Status</strong>.
        </div>

        {{-- TABEL / HASIL --}}
        @if(isset($borrowings) && $borrowings->count())
            <div class="status-table-wrapper mt-2">
                <table class="status-table">
                    <thead>
                        <tr>
                            <th style="width:60px;">No</th>
                            <th>Judul Buku</th>
                            <th style="width:170px;">Tanggal Pinjam</th>
                            <th style="width:170px;">Jatuh Tempo</th>
                            <th style="width:170px;">Tanggal Kembali</th>
                            <th style="width:130px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($borrowings as $index => $borrow)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $borrow->book->title ?? '-' }}</td>

                                {{-- Tgl Pinjam --}}
                                <td>
                                    @if($borrow->borrow_date)
                                        {{ Carbon::parse($borrow->borrow_date)->translatedFormat('d F Y') }}
                                    @else
                                        -
                                    @endif
                                </td>

                                {{-- Jatuh Tempo --}}
                                <td>
                                    @if($borrow->due_date)
                                        {{ Carbon::parse($borrow->due_date)->translatedFormat('d F Y') }}
                                    @else
                                        -
                                    @endif
                                </td>

                                {{-- Tanggal Kembali --}}
                                <td>
                                    @if($borrow->return_date)
                                        {{ Carbon::parse($borrow->return_date)->translatedFormat('d F Y') }}
                                    @else
                                        -
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td>
                                    @if(is_null($borrow->return_date))
                                        <span class="status-pill pill-active">
                                            <span class="pill-dot"></span>
                                            Dipinjam
                                        </span>
                                    @else
                                        <span class="status-pill pill-returned">
                                            <span class="pill-dot"></span>
                                            Dikembalikan
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @elseif(!empty($nis))
            <p class="mt-2 status-empty-text">
                Tidak ditemukan peminjaman untuk NIS <strong>{{ $nis }}</strong>.
            </p>
        @endif

    </div>
</div>
@endsection

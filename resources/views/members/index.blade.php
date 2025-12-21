{{-- resources/views/members/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Data Anggota / Siswa')

@section('content')
<style>
    :root {
        --indigo: #4f46e5;
        --indigo-soft: #eef2ff;
        --indigo-soft2: #e0e7ff;
        --accent: #06b6d4;
        --danger-soft: #fee2e2;
        --danger: #dc2626;
        --card-bg: rgba(255, 255, 255, 0.96);
        --border-soft: #e5e7eb;
        --text-main: #0f172a;
        --text-muted: #6b7280;
    }

    body {
        background:
            radial-gradient(circle at top left, #e0f2fe 0, #eef2ff 40%, #f9fafb 100%);
    }

    /* WRAPPER */
    .member-page-wrapper {
        max-width: 1180px;
        margin: 26px auto 40px;
        padding: 0 16px;
    }

    /* HERO */
    .member-hero {
        border-radius: 24px;
        padding: 18px 24px;
        background: linear-gradient(135deg,#4f46e5,#6366f1,#06b6d4);
        color:#fff;
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:18px;
        box-shadow:0 24px 50px rgba(15,23,42,.35);
        margin-bottom:24px;
    }

    .member-hero-left {
        display:flex;
        align-items:center;
        gap:16px;
    }

    .member-hero-icon {
        width:62px;
        height:62px;
        border-radius:22px;
        background:radial-gradient(circle at 30% 0,#e0f2fe 0,#60a5fa 40%,#111827 100%);
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:2rem;
        box-shadow:0 18px 38px rgba(15,23,42,.65);
    }

    .member-hero-title {
        font-size:1.6rem;
        font-weight:700;
        letter-spacing:.02em;
    }

    .member-hero-sub {
        font-size:.9rem;
        opacity:.95;
    }

    .member-hero-actions {
        display:flex;
        flex-wrap:wrap;
        gap:.5rem;
        justify-content:flex-end;
    }

    .btn-chip {
        border-radius:999px;
        padding:.42rem 1.2rem;
        font-size:.85rem;
        border:none;
        display:inline-flex;
        align-items:center;
        gap:.4rem;
        text-decoration:none;
        cursor:pointer;
    }

    .btn-chip-light {
        background:rgba(15,23,42,.12);
        color:#e5e7eb;
    }
    .btn-chip-light:hover {
        background:rgba(15,23,42,.22);
        color:#fff;
    }

    .btn-chip-soft {
        background:rgba(239,246,255,.9);
        color:#1d4ed8;
    }
    .btn-chip-soft:hover {
        background:#e0ebff;
        color:#1e3a8a;
    }

    .btn-chip-primary {
        background:#f97316;
        color:#fff;
    }
    .btn-chip-primary:hover {
        background:#ea580c;
        color:#fff;
    }

    /* CARD TABLE */
    .members-card {
        border-radius:22px;
        background:var(--card-bg);
        border:1px solid var(--border-soft);
        box-shadow:0 20px 40px rgba(15,23,42,.12);
        overflow:hidden;
    }

    .members-card-header {
        padding:14px 20px;
        background:linear-gradient(90deg,#f9fafb,#eef2ff);
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:10px;
    }

    .members-card-title {
        display:flex;
        align-items:center;
        gap:.55rem;
        font-weight:600;
        color:var(--text-main);
    }

    .members-card-title span.icon {
        font-size:1.3rem;
    }

    .members-total {
        font-size:.88rem;
        color:var(--text-muted);
    }

    /* TABLE */
    .members-table {
        margin-bottom:0;
        font-size:.9rem;
    }

    .members-table thead th {
        background:var(--indigo-soft);
        border-bottom:1px solid var(--border-soft);
        color:#374151;
        font-size:.8rem;
        text-transform:uppercase;
        letter-spacing:.07em;
    }

    .members-table tbody tr {
        transition:background .12s ease, transform .08s ease;
    }
    .members-table tbody tr:nth-child(even) {
        background:#f9fafb;
    }
    .members-table tbody tr:hover {
        background:#e0ebff;
        transform:translateY(-1px);
    }

    .badge-class {
        display:inline-flex;
        align-items:center;
        gap:.28rem;
        padding:.16rem .7rem;
        border-radius:999px;
        background:rgba(6,182,212,.08);
        color:#0e7490;
        font-size:.75rem;
        font-weight:500;
    }
    .badge-class span.dot {
        width:7px;
        height:7px;
        border-radius:999px;
        background:#0e7490;
    }

    .btn-pill-sm {
        border-radius:999px !important;
        padding:.26rem .9rem;
        font-size:.78rem;
    }

    .btn-soft-neutral {
        background:#f3f4f6;
        border-color:#e5e7eb;
        color:#374151;
    }
    .btn-soft-neutral:hover {
        background:#e5e7eb;
        color:#111827;
    }

    .btn-soft-primary {
        background:var(--indigo-soft);
        border-color:var(--indigo-soft2);
        color:#3730a3;
    }
    .btn-soft-primary:hover {
        background:#e0e7ff;
        color:#312e81;
    }

    .btn-soft-danger {
        background:var(--danger-soft);
        border-color:#fecaca;
        color:var(--danger);
    }
    .btn-soft-danger:hover {
        background:#fecaca;
        color:#b91c1c;
    }

    .alert-custom {
        max-width:1180px;
        margin:0 auto 10px;
        padding:.55rem .9rem;
        font-size:.85rem;
    }

    @media (max-width: 768px) {
        .member-hero {
            flex-direction:column;
            align-items:flex-start;
        }
        .member-hero-actions {
            justify-content:flex-start;
        }
    }
</style>

@php
    $totalMembers = $members->count();
@endphp

<div class="member-page-wrapper">

    {{-- FLASH MESSAGE --}}
    @if(session('success'))
        <div class="alert alert-success alert-custom mb-2">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-custom mb-2">
            {{ session('error') }}
        </div>
    @endif

    {{-- HERO --}}
    <div class="member-hero">
        <div class="member-hero-left">
            <div class="member-hero-icon">🧑‍🎓</div>
            <div>
                <div class="member-hero-title">Data Anggota / Siswa</div>
                <div class="member-hero-sub">
                    Daftar siswa yang terdaftar sebagai anggota perpustakaan sekolah.
                </div>
            </div>
        </div>
        <div class="member-hero-actions">
            <a href="{{ route('admin.dashboard') }}" class="btn-chip btn-chip-light">
                ← Kembali ke Dashboard
            </a>

            {{-- Import Excel --}}
            <a href="{{ route('admin.import.index') }}" class="btn-chip btn-chip-soft">
                📥 Import dari Excel
            </a>

            {{-- Tambah anggota --}}
            <a href="{{ route('members.create') }}" class="btn-chip btn-chip-primary">
                + Tambah Anggota
            </a>
        </div>
    </div>

    {{-- CARD TABEL --}}
    <div class="members-card">
        <div class="members-card-header">
            <div class="members-card-title">
                <span class="icon">📋</span>
                <span>Daftar Anggota / Siswa</span>
            </div>
            <div class="members-total">
                Total <strong>{{ $totalMembers }}</strong> anggota terdaftar.
            </div>
        </div>

        <div class="table-responsive">
            <table class="table members-table align-middle">
                <thead>
                    <tr>
                        <th style="width:60px;">No</th>
                        <th style="width:120px;">NIS</th>
                        <th>Nama</th>
                        <th style="width:140px;">Kelas</th>
                        <th style="width:260px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members as $member)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $member->nis }}</td>
                            <td>{{ $member->name }}</td>
                            <td>
                                <span class="badge-class">
                                    <span class="dot"></span>
                                    {{ $member->class }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center flex-wrap gap-1">
                                    <a href="{{ route('members.edit', $member->id) }}"
                                       class="btn btn-soft-neutral btn-pill-sm">
                                        Edit
                                    </a>

                                    <a href="{{ route('members.cetak.kartu', $member->id) }}"
                                       target="_blank"
                                       class="btn btn-soft-primary btn-pill-sm">
                                        Cetak Kartu
                                    </a>

                                    <form action="{{ route('members.destroy', $member->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus anggota ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-soft-danger btn-pill-sm">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">
                                Belum ada data anggota yang tersimpan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

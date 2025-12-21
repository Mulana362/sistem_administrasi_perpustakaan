@extends('layouts.app')

@section('title', 'Form Kunjungan Perpustakaan')

@section('content')
<style>
    .visit-page-bg {
        min-height: calc(100vh - 80px);
        display: flex;
        align-items: center;
        justify-content: center;
        background:
            radial-gradient(circle at top left, #dbeafe 0, #eff6ff 35%, transparent 60%),
            radial-gradient(circle at bottom right, #e5e7eb 0, #f9fafb 45%, #e5e7eb 100%);
        padding: 24px 12px;
    }

    .visit-card {
        background: #ffffff;
        border-radius: 18px;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.15);
        padding: 20px 22px;
        max-width: 480px;
        width: 100%;
        border: 1px solid #e5e7eb;
    }

    .visit-title {
        font-size: 1.4rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: .5rem;
    }

    .visit-subtitle {
        font-size: .85rem;
        color: #6b7280;
        margin-bottom: 18px;
    }

    .visit-label {
        font-size: .85rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 4px;
    }

    .visit-input,
    .visit-select,
    .visit-textarea {
        width: 100%;
        border-radius: 10px;
        border: 1px solid #d1d5db;
        padding: 9px 11px;
        font-size: .9rem;
        background: #f9fafb;
        margin-bottom: 10px;
    }

    .visit-input:focus,
    .visit-select:focus,
    .visit-textarea:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, .15);
        background: #ffffff;
    }

    .visit-textarea {
        min-height: 70px;
        resize: vertical;
    }

    .visit-btn {
        width: 100%;
        border-radius: 999px;
        border: none;
        padding: 10px 0;
        font-weight: 600;
        font-size: .95rem;
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: #fff;
        box-shadow: 0 12px 30px rgba(37, 99, 235, 0.35);
        cursor: pointer;
        margin-top: 6px;
    }

    .visit-btn:hover {
        filter: brightness(1.05);
    }

    /* TOMBOL KEMBALI */
    .btn-back {
        display: block;
        width: 100%;
        border-radius: 999px;
        padding: 10px 0;
        margin-top: 12px;
        font-weight: 600;
        text-align: center;
        background: linear-gradient(135deg, #6b7280, #4b5563);
        color: #fff;
        text-decoration: none;
        box-shadow: 0 12px 28px rgba(75,85,99,0.35);
    }

    .btn-back:hover {
        filter: brightness(1.08);
        text-decoration: none;
        color: #fff;
    }

    .visit-footer-note {
        font-size: .75rem;
        color: #9ca3af;
        margin-top: 8px;
        text-align: center;
    }

    .text-error {
        color:#dc2626;
        font-size:.78rem;
        margin-top:-6px;
        margin-bottom:6px;
    }
</style>

<div class="visit-page-bg">
    <div class="visit-card">

        {{-- ALERT SUKSES --}}
        @if (session('success'))
            <div class="alert alert-success py-2">
                {{ session('success') }}
            </div>
        @endif

        {{-- ERROR VALIDASI --}}
        @if ($errors->any())
            <div class="alert alert-danger py-2">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li style="font-size:.85rem;">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mb-2 text-center">
            <div class="visit-title">
                <span>📋</span>
                <span>Buku Tamu Perpustakaan</span>
            </div>
            <div class="visit-subtitle">
                Silakan isi data kunjungan dengan lengkap.
            </div>
        </div>

        <form method="POST" action="{{ route('visit.store') }}">
            @csrf

            {{-- Nama --}}
            <label class="visit-label">Nama Lengkap</label>
            <input type="text" name="name" class="visit-input"
                   placeholder="Contoh: Ahmad Rizky Pratama" value="{{ old('name') }}" required>

            {{-- NIS --}}
            <label class="visit-label">NIS</label>
            <input type="text" name="nis" class="visit-input"
                   placeholder="Masukkan NIS siswa" value="{{ old('nis') }}" required>

            {{-- Kelas --}}
            <label class="visit-label">Kelas</label>
            <input type="text" name="class" class="visit-input"
                   placeholder="Contoh: 7A / 8B / 9C" value="{{ old('class') }}" required>

            {{-- Keperluan --}}
            <label class="visit-label">Keperluan Kunjungan</label>
            <select name="purpose" class="visit-select" required>
                <option value="">-- Pilih Keperluan --</option>
                <option value="Membaca di perpustakaan">Membaca di perpustakaan</option>
                <option value="Mengerjakan tugas">Mengerjakan tugas</option>
                <option value="Meminjam / mengembalikan buku">Meminjam / mengembalikan buku</option>
                <option value="Lainnya">Lainnya</option>
            </select>

            {{-- Keterangan opsional --}}
            <label class="visit-label">Keterangan Tambahan (opsional)</label>
            <textarea name="note" class="visit-textarea"
                      placeholder="Contoh: Mencari referensi buku IPA, menunggu guru, dst.">{{ old('note') }}</textarea>

            {{-- Tombol Simpan --}}
            <button type="submit" class="visit-btn">Simpan Kunjungan</button>

            {{-- Tombol Kembali --}}
            <a href="{{ route('admin.dashboard') }}" class="btn-back">
                ← Kembali ke Dashboard
            </a>

            <div class="visit-footer-note">
                Data kunjungan otomatis masuk ke menu <b>Rekap Kunjungan</b> admin.
            </div>
        </form>
    </div>
</div>
@endsection

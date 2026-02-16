@extends('layouts.app')

@section('content')
<style>
    .page-wrapper {
        max-width: 650px;
        margin: auto;
        background: #ffffff;
        padding: 30px;
        border-radius: 14px;
        box-shadow: 0 10px 28px rgba(0,0,0,0.12);
        margin-top: 40px;
    }

    .page-title {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 20px;
        color: #2d3436;
    }

    label {
        font-weight: 600;
        color: #2d3436;
    }

    .form-control {
        border-radius: 8px !important;
        padding: 12px !important;
        border: 1px solid #dcdde1;
        background: #f5f6fa;
    }

    .form-control:focus {
        border-color: #0984e3;
        box-shadow: 0 0 0 2px rgba(9,132,227,0.15);
        background: #fff;
    }

    .btn-primary {
        background-color: #0984e3 !important;
        border-radius: 8px;
        padding: 10px 16px;
        font-weight: 600;
    }

    .btn-secondary {
        border-radius: 8px;
        padding: 10px 16px;
        font-weight: 600;
    }
</style>

<div class="page-wrapper">

    <h2 class="page-title">Tambah Buku Baru</h2>

    {{-- Tampilkan pesan error --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Periksa kembali input anda:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- PENTING: tambahkan enctype --}}
    <form action="{{ route('books.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- ✅ ID BUKU --}}
        <div class="mb-3">
            <label>ID Buku</label>
            <input
                type="text"
                name="book_code"
                class="form-control"
                placeholder="Contoh: BK-001"
                value="{{ old('book_code') }}"
                required
            >
            <small class="text-muted">Gunakan format konsisten (misal: BK-001, BK-002, dst).</small>
        </div>

        {{-- JUDUL --}}
        <div class="mb-3">
            <label>Judul Buku</label>
            <input type="text" name="title" class="form-control"
                   placeholder="Contoh: Laskar Pelangi" value="{{ old('title') }}" required>
        </div>

        {{-- PENGARANG --}}
        <div class="mb-3">
            <label>Pengarang</label>
            <input type="text" name="author" class="form-control"
                   placeholder="Contoh: Andrea Hirata" value="{{ old('author') }}" required>
        </div>

        {{-- PENERBIT --}}
        <div class="mb-3">
            <label>Penerbit</label>
            <input type="text" name="publisher" class="form-control"
                   placeholder="Nama penerbit" value="{{ old('publisher') }}" required>
        </div>

        {{-- TAHUN TERBIT --}}
        <div class="mb-3">
            <label>Tahun Terbit</label>
            <input type="number" name="year" class="form-control"
                   placeholder="Contoh: 2020" min="1900" max="{{ date('Y') }}"
                   value="{{ old('year') }}" required>
        </div>

        {{-- STOK --}}
        <div class="mb-3">
            <label>Stok Buku</label>
            <input type="number" name="stock" class="form-control"
                   placeholder="Jumlah buku tersedia" min="0" value="{{ old('stock') }}" required>
        </div>

        {{-- COVER BUKU (opsional) --}}
        <div class="mb-3">
            <label>Cover Buku (opsional)</label>
            <input type="file" name="cover" class="form-control">
            <small class="text-muted">Format: jpg, jpeg, png, webp. Maks 2 MB.</small>
        </div>

        {{-- DESKRIPSI BUKU (opsional) --}}
        <div class="mb-3">
            <label>Deskripsi Buku (opsional)</label>
            <textarea name="description" class="form-control" rows="3"
                      placeholder="Ringkasan singkat isi buku">{{ old('description') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-2">
            Simpan Buku
        </button>

        <a href="{{ route('books.index') }}" class="btn btn-secondary w-100">
            Batal / Kembali
        </a>
    </form>
</div>

@endsection

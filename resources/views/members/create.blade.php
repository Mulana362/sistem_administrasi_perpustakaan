@extends('layouts.app')

@section('title', 'Tambah Anggota / Siswa')

@section('content')

<style>
    body {
        background: #eef2ff;
    }

    .member-create-wrapper {
        max-width: 520px;
        margin: 30px auto;
    }

    .member-card {
        background: #ffffff;
        padding: 28px 30px;
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        border: 1px solid #e0e7ff;
    }

    .member-title {
        font-size: 22px;
        font-weight: 700;
        text-align: center;
        margin-bottom: 4px;
        color: #1e293b;
    }

    .member-subtitle {
        text-align: center;
        color: #64748b;
        font-size: 14px;
        margin-bottom: 20px;
    }

    .form-label {
        font-weight: 600;
        margin-bottom: 6px;
        color: #334155;
        font-size: 14px;
    }

    .form-control {
        border-radius: 10px;
        padding: 11px 12px;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        font-size: 14px;
    }

    .form-control:focus {
        border-color: #6366f1 !important;
        background: #ffffff;
        box-shadow: 0 0 0 2px rgba(99,102,241,0.25);
    }

    .btn-primary {
        width: 100%;
        padding: 11px;
        font-weight: 600;
        border-radius: 10px;
        background: #4f46e5;
        border: none;
        transition: .15s;
    }

    .btn-primary:hover {
        background: #3730a3;
    }

    .btn-secondary {
        width: 100%;
        padding: 10px;
        margin-top: 10px;
        border-radius: 10px;
    }

    .text-error {
        color: #dc2626;
        font-size: 13px;
        margin-top: -8px;
        margin-bottom: 8px;
    }
</style>

<div class="member-create-wrapper">

    <div class="member-card">

        <div class="member-title">Tambah Anggota</div>
        <div class="member-subtitle">Isi data siswa/anggota perpustakaan dengan lengkap.</div>

        {{-- ERROR MESSAGE --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-1">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('members.store') }}">
            @csrf

            {{-- NIS --}}
            <label class="form-label">NIS</label>
            <input type="text" name="nis" class="form-control" value="{{ old('nis') }}" required autofocus>
            @error('nis')
                <div class="text-error">{{ $message }}</div>
            @enderror

            {{-- NAMA --}}
            <label class="form-label">Nama Lengkap</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            @error('name')
                <div class="text-error">{{ $message }}</div>
            @enderror

            {{-- KELAS --}}
            <label class="form-label">Kelas</label>
            <input type="text" name="class" class="form-control" placeholder="Contoh: 7A / 8B / 9C" value="{{ old('class') }}" required>
            @error('class')
                <div class="text-error">{{ $message }}</div>
            @enderror

            <button type="submit" class="btn btn-primary mt-2">
                Simpan Anggota
            </button>

            <a href="{{ route('members.index') }}" class="btn btn-secondary mt-2">
                Kembali
            </a>
        </form>
    </div>
</div>

@endsection

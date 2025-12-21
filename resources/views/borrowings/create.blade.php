@extends('layouts.app')

@section('content')
<style>
    body {
        background: linear-gradient(120deg, #74b9ff, #a29bfe);
    }

    .loan-form-wrapper {
        min-height: calc(100vh - 80px);
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .loan-form-box {
        background: #ffffff;
        padding: 30px 32px;
        width: 460px;
        border-radius: 14px;
        box-shadow: 0 12px 30px rgba(0,0,0,0.18);
    }

    .loan-form-title {
        text-align: center;
        margin-bottom: 22px;
        font-size: 22px;
        font-weight: 700;
        color: #2d3436;
    }

    .loan-form-label {
        font-weight: 600;
        margin-bottom: 6px;
        color: #2d3436;
    }

    .loan-form-input,
    .loan-form-select {
        width: 100%;
        padding: 11px 12px;
        border-radius: 8px;
        border: 1px solid #dfe6e9;
        background: #f7f7f7;
        font-size: 14px;
        margin-bottom: 14px;
    }

    .loan-form-input:focus,
    .loan-form-select:focus {
        outline: none;
        border-color: #0984e3;
        box-shadow: 0 0 0 2px rgba(9,132,227,0.15);
        background: #ffffff;
    }

    .loan-form-small {
        display: block;
        margin-top: -8px;
        margin-bottom: 12px;
        color: #636e72;
        font-size: 13px;
    }

    .loan-btn-primary,
    .loan-btn-secondary {
        width: 100%;
        padding: 11px;
        border-radius: 8px;
        border: none;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        margin-top: 4px;
    }

    .loan-btn-primary {
        background: #0984e3;
        color: #ffffff;
    }

    .loan-btn-primary:hover {
        background: #0768b1;
    }

    .loan-btn-secondary {
        background: #b2bec3;
        color: #2d3436;
    }

    .loan-btn-secondary:hover {
        background: #636e72;
        color: #ffffff;
    }

    .text-error {
        color: #d63031;
        font-size: 13px;
        margin-top: -10px;
        margin-bottom: 8px;
    }
</style>

<div class="loan-form-wrapper">
    <div class="loan-form-box">
        <h2 class="loan-form-title">Tambah Peminjaman Buku</h2>

        {{-- pesan sukses --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- pesan error validasi --}}
        @if ($errors->any())
            <div class="alert alert-danger mb-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('borrowings.store') }}">
            @csrf

            {{-- DATA SISWA --}}
            <label class="loan-form-label">NIS</label>
            <input type="text"
                   name="student_nis"
                   class="loan-form-input"
                   value="{{ old('student_nis') }}"
                   required>
            @error('student_nis')
                <div class="text-error">{{ $message }}</div>
            @enderror

            <label class="loan-form-label">Nama Siswa</label>
            <input type="text"
                   name="student_name"
                   class="loan-form-input"
                   value="{{ old('student_name') }}"
                   required>
            @error('student_name')
                <div class="text-error">{{ $message }}</div>
            @enderror

            <label class="loan-form-label">Kelas</label>
            <input type="text"
                   name="student_class"
                   class="loan-form-input"
                   placeholder="contoh: 9A / 8B"
                   value="{{ old('student_class') }}"
                   required>
            @error('student_class')
                <div class="text-error">{{ $message }}</div>
            @enderror

            {{-- PILIH BUKU --}}
            <label class="loan-form-label">Pilih Buku</label>
            <select name="book_id" class="loan-form-select" required>
                <option value="">— Pilih Buku —</option>

                @forelse ($books as $b)
                    <option value="{{ $b->id }}" {{ old('book_id') == $b->id ? 'selected' : '' }}>
                        {{ $b->title }} — {{ $b->author }}
                    </option>
                @empty
                    <option value="" disabled>Belum ada data buku di perpustakaan</option>
                @endforelse
            </select>
            @error('book_id')
                <div class="text-error">{{ $message }}</div>
            @enderror

            {{-- TANGGAL PINJAM --}}
            <label class="loan-form-label">Tanggal Pinjam</label>
            <input type="date"
                   id="tanggal_pinjam"
                   name="borrow_date"
                   class="loan-form-input"
                   value="{{ old('borrow_date', date('Y-m-d')) }}"
                   onchange="hitungJatuhTempo()"
                   required>
            @error('borrow_date')
                <div class="text-error">{{ $message }}</div>
            @enderror

            {{-- DURASI --}}
            <label class="loan-form-label">Lama Peminjaman (hari)</label>
            <select id="durasi"
                    name="duration"
                    class="loan-form-select"
                    onchange="hitungJatuhTempo()"
                    required>
                <option value="">— Pilih Durasi —</option>
                @for ($i = 1; $i <= 7; $i++)
                    <option value="{{ $i }}" {{ old('duration') == $i ? 'selected' : '' }}>
                        {{ $i }} Hari
                    </option>
                @endfor
            </select>
            <small class="loan-form-small">
                Maksimal peminjaman adalah <b>7 hari</b> dari tanggal pinjam.
            </small>
            @error('duration')
                <div class="text-error">{{ $message }}</div>
            @enderror

            {{-- JATUH TEMPO --}}
            <label class="loan-form-label">Tanggal Jatuh Tempo</label>
            <input type="date"
                   id="tanggal_jatuh_tempo"
                   name="due_date"
                   class="loan-form-input"
                   value="{{ old('due_date') }}"
                   readonly
                   required>
            @error('due_date')
                <div class="text-error">{{ $message }}</div>
            @enderror

            <button type="submit" class="loan-btn-primary">
                Simpan Peminjaman
            </button>

            <button type="button"
                    class="loan-btn-secondary"
                    onclick="window.location.href='{{ route('borrowings.index') }}'">
                Batal / Kembali
            </button>
        </form>
    </div>
</div>

<script>
    function hitungJatuhTempo() {
        let hari      = document.getElementById("durasi").value;
        let tglPinjam = document.getElementById("tanggal_pinjam").value;

        if (hari && tglPinjam) {
            let date = new Date(tglPinjam);
            date.setDate(date.getDate() + parseInt(hari));

            let yyyy = date.getFullYear();
            let mm   = String(date.getMonth() + 1).padStart(2, "0");
            let dd   = String(date.getDate()).padStart(2, "0");

            document.getElementById("tanggal_jatuh_tempo").value = `${yyyy}-${mm}-${dd}`;
        } else {
            document.getElementById("tanggal_jatuh_tempo").value = "";
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        hitungJatuhTempo();
    });
</script>
@endsection

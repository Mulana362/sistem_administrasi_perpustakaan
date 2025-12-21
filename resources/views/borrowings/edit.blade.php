
@extends('layouts.app')

@section('content')
<style>
    body {
        background: #f3f5ff;
    }

    .edit-borrow-wrapper {
        max-width: 1180px;
        margin: 24px auto 40px;
        display: grid;
        grid-template-columns: minmax(0, 1.1fr) minmax(0, 1fr);
        gap: 24px;
    }

    .edit-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 20px 24px 24px;
        box-shadow: 0 12px 30px rgba(0,0,0,0.08);
    }

    .edit-title {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 16px;
        color: #2d3436;
    }

    .edit-subtitle {
        font-size: 13px;
        color: #636e72;
        margin-bottom: 18px;
    }

    .edit-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 5px;
        color: #2d3436;
    }

    .edit-input, .edit-select {
        width: 100%;
        padding: 10px 11px;
        border-radius: 9px;
        border: 1px solid #dfe6e9;
        background: #f7f8fd;
        font-size: 14px;
        margin-bottom: 10px;
    }

    .edit-input:focus, .edit-select:focus {
        outline: none;
        border-color: #2980b9;
        box-shadow: 0 0 0 2px rgba(41, 128, 185, .18);
        background: #ffffff;
    }

    .edit-row-2 {
        display: grid;
        grid-template-columns: repeat(2, minmax(0,1fr));
        gap: 14px;
    }

    .edit-help {
        font-size: 12px;
        color: #7f8c8d;
        margin-bottom: 12px;
    }

    .edit-btn-row {
        margin-top: 14px;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn-main {
        border: none;
        padding: 9px 18px;
        border-radius: 999px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-primary {
        background: #0984e3;
        color: #ffffff;
    }
    .btn-primary:hover { background:#0768b1; }

    .btn-outline {
        background: #ffffff;
        color: #2d3436;
        border: 1px solid #dfe6e9;
    }
    .btn-outline:hover {
        background:#ecf0f1;
    }

    .btn-secondary {
        background:#b2bec3;
        color:#2d3436;
    }
    .btn-secondary:hover {
        background:#636e72;
        color:#ffffff;
    }

    .text-error {
        color:#e74c3c;
        font-size:12px;
        margin-top:-6px;
        margin-bottom:6px;
    }

    /* ===== SLIP PEMINJAMAN (KANAN) ===== */
    .slip-header-title {
        font-size: 18px;
        font-weight: 700;
        text-align:center;
        text-transform: uppercase;
    }
    .slip-header-sub {
        font-size: 16px;
        font-weight: 600;
        text-align:center;
        margin-bottom: 12px;
        text-transform: uppercase;
    }

    .slip-table {
        width: 100%;
        font-size: 13px;
        margin-bottom: 18px;
    }
    .slip-table td:first-child {
        width: 110px;
        vertical-align: top;
    }
    .slip-table td:nth-child(2) {
        width: 10px;
    }

    .slip-section-title {
        font-weight: 700;
        margin-bottom: 6px;
        font-size: 14px;
    }

    .slip-list {
        font-size: 13px;
        padding-left: 18px;
        margin-top: 0;
        margin-bottom: 16px;
    }

    .slip-footer-date {
        font-size: 13px;
        margin-top: 8px;
        margin-bottom: 40px;
    }

    .slip-signature {
        display: flex;
        justify-content: space-between;
        margin-top: 24px;
        font-size: 13px;
        gap: 40px;
    }

    .slip-signature-col {
        flex: 1;
        text-align: center;
    }

    .sign-line {
        margin: 50px auto 4px;
        border-bottom: 1px solid #2d3436;
        width: 160px;
    }

    .sign-label {
        margin-top: 2px;
    }

    .sign-name {
        font-weight: 600;
        margin-top: 2px;
    }

    /* ===== PRINT MODE ===== */
    .no-print {
        /* elemen yang tidak ikut tercetak */
    }

    @media print {
        body {
            background: #ffffff !important;
        }

        .no-print {
            display: none !important;
        }

        .edit-borrow-wrapper {
            max-width: 100%;
            margin: 0;
            display: block;
        }

        .edit-card {
            box-shadow: none;
            border-radius: 0;
            padding: 0;
        }

        .slip-print-only {
            page-break-after: auto;
        }
    }
</style>

<div class="edit-borrow-wrapper">
    {{-- KOLOM KIRI: FORM PERUBAHAN (tidak ikut tercetak) --}}
    <div class="edit-card no-print">
        <div class="edit-title">Form Perubahan</div>
        <div class="edit-subtitle">
            Perbarui data peminjaman, termasuk tanggal jatuh tempo dan status buku.
        </div>

        @if ($errors->any())
            <div class="alert alert-danger mb-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('borrowings.update', $borrowing->id) }}" method="POST">
            @csrf
            @method('PUT')

            <label class="edit-label">Nama Siswa</label>
            <input type="text"
                   class="edit-input"
                   name="student_name"
                   value="{{ old('student_name', $borrowing->student_name) }}"
                   required>
            @error('student_name')
                <div class="text-error">{{ $message }}</div>
            @enderror

            <div class="edit-row-2">
                <div>
                    <label class="edit-label">NIS</label>
                    <input type="text"
                           class="edit-input"
                           name="student_nis"
                           value="{{ old('student_nis', $borrowing->student_nis) }}"
                           required>
                    @error('student_nis')
                        <div class="text-error">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label class="edit-label">Kelas</label>
                    <input type="text"
                           class="edit-input"
                           name="student_class"
                           value="{{ old('student_class', $borrowing->student_class) }}"
                           required>
                    @error('student_class')
                        <div class="text-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <label class="edit-label">Buku</label>
            <select name="book_id" class="edit-select" required>
                @foreach ($books as $b)
                    <option value="{{ $b->id }}"
                        {{ old('book_id', $borrowing->book_id) == $b->id ? 'selected' : '' }}>
                        {{ $b->title }} — {{ $b->author }}
                    </option>
                @endforeach
            </select>
            @error('book_id')
                <div class="text-error">{{ $message }}</div>
            @enderror

            <div class="edit-row-2">
                <div>
                    <label class="edit-label">Tanggal Pinjam</label>
                    <input type="date"
                           id="tanggal_pinjam"
                           name="borrow_date"
                           class="edit-input"
                           value="{{ old('borrow_date', \Carbon\Carbon::parse($borrowing->borrow_date)->format('Y-m-d')) }}"
                           required>
                    @error('borrow_date')
                        <div class="text-error">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label class="edit-label">Tanggal Jatuh Tempo</label>
                    <input type="date"
                           id="tanggal_jatuh_tempo"
                           name="due_date"
                           class="edit-input"
                           value="{{ old('due_date', \Carbon\Carbon::parse($borrowing->due_date)->format('Y-m-d')) }}"
                           required>
                    @error('due_date')
                        <div class="text-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <label class="edit-label">Status Peminjaman</label>
            <select name="status" class="edit-select" required>
                @php
                    $currentStatus = old('status', $borrowing->status);
                @endphp
                <option value="Dipinjam" {{ $currentStatus == 'Dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                <option value="Kembali" {{ $currentStatus == 'Kembali' ? 'selected' : '' }}>Kembali</option>
                <option value="Terlambat" {{ $currentStatus == 'Terlambat' ? 'selected' : '' }}>Terlambat</option>
            </select>
            <div class="edit-help">
                Ubah menjadi <b>Kembali</b> jika buku sudah benar-benar diterima petugas.
            </div>

            <div class="edit-btn-row">
                <button type="submit" class="btn-main btn-primary">
                    Simpan Perubahan
                </button>

                <a href="{{ route('borrowings.index') }}" class="btn-main btn-outline">
                    Kembali ke Daftar Peminjaman
                </a>

                <button type="button"
                        id="btnPrintSlip"
                        class="btn-main btn-secondary">
                    Cetak Slip Peminjaman
                </button>
            </div>
        </form>
    </div>

    {{-- KOLOM KANAN: SLIP PEMINJAMAN (INI YANG DICETAK) --}}
    <div class="edit-card slip-print-only">
        @php
            $borrowDate = \Carbon\Carbon::parse($borrowing->borrow_date)
                            ->locale('id')
                            ->translatedFormat('d F Y');
            $dueDate    = \Carbon\Carbon::parse($borrowing->due_date)
                            ->locale('id')
                            ->translatedFormat('d F Y');
            $today      = now()->locale('id')->translatedFormat('d F Y');
        @endphp

        <div class="slip-header-title">PERPUSTAKAAN SMPN 1 BANDUNG</div>
        <div class="slip-header-sub">SLIP PEMINJAMAN BUKU</div>

        <table class="slip-table">
            <tr>
                <td>Nama Siswa</td><td>:</td>
                <td>{{ $borrowing->student_name }}</td>
            </tr>
            <tr>
                <td>NIS</td><td>:</td>
                <td>{{ $borrowing->student_nis }}</td>
            </tr>
            <tr>
                <td>Kelas</td><td>:</td>
                <td>{{ $borrowing->student_class }}</td>
            </tr>
            <tr>
                <td>Judul Buku</td><td>:</td>
                <td>{{ $borrowing->book->title }}</td>
            </tr>
            <tr>
                <td>Pengarang</td><td>:</td>
                <td>{{ $borrowing->book->author }}</td>
            </tr>
            <tr>
                <td>Tanggal Pinjam</td><td>:</td>
                <td>{{ $borrowDate }}</td>
            </tr>
            <tr>
                <td>Tgl Jatuh Tempo</td><td>:</td>
                <td>{{ $dueDate }}</td>
            </tr>
            <tr>
                <td>Status</td><td>:</td>
                <td>{{ $borrowing->status }}</td>
            </tr>
        </table>

        <!-- KETENTUAN RAPI -->
        <div class="slip-section-title">Ketentuan Peminjaman:</div>
        <ul class="slip-list">
            <li>Buku harus dijaga dengan baik dan tidak boleh dicoret, dilipat berlebihan, atau dirusak.</li>
            <li>Buku wajib dikembalikan paling lambat pada tanggal jatuh tempo yang tercantum.</li>
            <li>Keterlambatan pengembalian dikenakan denda berdasarkan aturan perpustakaan sekolah.</li>
            <li>Buku yang hilang atau rusak berat wajib diganti dengan buku yang sama atau setara.</li>
            <li>Slip ini wajib dibawa dan ditunjukkan kepada petugas saat pengembalian buku.</li>
        </ul>

        <div class="slip-footer-date">
            Bandung, {{ $today }}
        </div>

        <div class="slip-signature">
            <div class="slip-signature-col">
                <div>Petugas Perpustakaan</div>
                <div class="sign-line"></div>
            </div>
            <div class="slip-signature-col">
                <div>Peminjam</div>
                <div class="sign-line"></div>
                <div class="sign-name">{{ $borrowing->student_name }}</div>
            </div>
        </div>
    </div>
</div>

<script>
    // tombol cetak slip -> dialog print (bisa pilih Save as PDF)
    document.getElementById('btnPrintSlip')?.addEventListener('click', function () {
        window.print();
    });
</script>
@endsection


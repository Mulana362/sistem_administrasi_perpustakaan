{{-- resources/views/student/books/catalog.blade.php --}}
@extends('layouts.app')

@section('title', 'Katalog Buku Perpustakaan')

@section('content')
<style>
    body {
        background:
            radial-gradient(circle at top left, #0f172a 0, #1e293b 25%, transparent 60%),
            radial-gradient(circle at bottom right, #e5e7eb 0, #f9fafb 45%, #e5e7eb 100%);
    }

    .catalog-wrapper {
        max-width: 1100px;
        margin: 24px auto 40px;
        padding: 0 12px;
        font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    }

    .catalog-header-bar {
        background: linear-gradient(135deg, #1d4ed8, #2563eb, #22d3ee);
        color: #fff;
        border-radius: 16px;
        padding: 14px 20px;
        box-shadow: 0 18px 40px rgba(37, 99, 235, 0.55);
        margin-bottom: 22px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
    }

    .catalog-header-bar-title {
        font-size: 1.05rem;
        font-weight: 600;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .catalog-header-bar-sub {
        font-size: .8rem;
        opacity: .96;
    }

    .catalog-main-card {
        background-color: #ffffff;
        border-radius: 18px;
        padding: 18px 20px 20px;
        box-shadow: 0 16px 40px rgba(15, 23, 42, 0.16);
        border: 1px solid #e5e7eb;
    }

    .catalog-main-title {
        font-size: 1.6rem;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .catalog-main-sub {
        font-size: .88rem;
        color: #6b7280;
        margin-bottom: 16px;
    }

    .catalog-search-row {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
        margin-bottom: 12px;
        flex-wrap: wrap;
    }

    .catalog-search-row input[type="text"] {
        max-width: 260px;
        border-radius: 999px;
        font-size: .85rem;
    }

    .catalog-search-row button {
        border-radius: 999px;
        padding-inline: 16px;
        font-size: .85rem;
    }

    /* TABEL */
    .catalog-table thead th {
        background: #020617;
        color: #f9fafb;
        font-size: .8rem;
        text-transform: uppercase;
        letter-spacing: .06em;
        border-bottom: none;
    }

    .catalog-table thead th:first-child {
        border-top-left-radius: 10px;
    }

    .catalog-table thead th:last-child {
        border-top-right-radius: 10px;
    }

    .catalog-table tbody tr:nth-child(even) {
        background: #f9fafb;
    }

    .catalog-table tbody tr:hover {
        background: #eef2ff;
    }

    .catalog-table tbody td {
        font-size: .86rem;
    }

    .badge-status {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: .75rem;
        font-weight: 600;
    }

    .badge-success {
        background: #16a34a;
        color: #ecfdf5;
    }

    .badge-danger {
        background: #b91c1c;
        color: #fee2e2;
    }

    .stock-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 52px;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: .8rem;
        font-weight: 600;
        border: 1px solid #d1d5db;
        background: #f9fafb;
        color: #111827;
    }

    .stock-available {
        background: #ecfdf5;
        border-color: #16a34a;
        color: #166534;
    }

    .stock-empty {
        background: #fef2f2;
        border-color: #fca5a5;
        color: #b91c1c;
    }

    .book-cover-thumb {
        width: 54px;
        height: 72px;
        border-radius: 6px;
        object-fit: cover;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.4);
    }

    .book-title-cell {
        font-weight: 600;
    }

    .book-desc {
        font-size: .78rem;
        color: #6b7280;
        margin-top: 2px;
    }

    .catalog-footer-link a {
        font-size: .85rem;
        color: #2563eb;
        text-decoration: none;
    }

    .catalog-footer-link a:hover {
        text-decoration: underline;
    }

    /* MODAL “NEON” */
    .modal-neon .modal-dialog {
        max-width: 720px;
    }

    .modal-neon .modal-content {
        border-radius: 20px;
        border: 1px solid rgba(56, 189, 248, 0.8);
        background: radial-gradient(circle at top left, #0f172a 0, #020617 55%);
        box-shadow:
            0 0 25px rgba(56, 189, 248, 0.65),
            0 0 80px rgba(15, 23, 42, 0.95);
        color: #e5e7eb;
        overflow: hidden;
        padding: 0;
    }

    .modal-neon .modal-header,
    .modal-neon .modal-footer {
        border: none;
        padding: 14px 18px;
    }

    .modal-neon .modal-body {
        padding: 0 18px 16px;
    }

    .neon-layout {
        display: grid;
        grid-template-columns: minmax(0, 2.2fr) minmax(0, 3fr);
        min-height: 320px;
    }

    .neon-left {
        position: relative;
        padding: 18px 18px 18px 22px;
        background:
            linear-gradient(135deg, rgba(15, 23, 42, 0.1) 0, rgba(30, 64, 175, 0.85) 35%, #0f172a 70%),
            radial-gradient(circle at top left, #1d4ed8, transparent 55%);
        border-right: 1px solid rgba(37, 99, 235, 0.4);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .neon-left::after {
        content: "";
        position: absolute;
        inset: 0;
        border-right: 2px solid rgba(56, 189, 248, 0.8);
        border-radius: 0 20px 20px 0;
        opacity: 0.25;
        pointer-events: none;
    }

    .neon-welcome {
        font-size: .85rem;
        letter-spacing: .2em;
        text-transform: uppercase;
        color: #93c5fd;
        margin-bottom: 6px;
    }

    .neon-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .neon-sub {
        font-size: .82rem;
        color: #d1d5db;
    }

    .neon-book-info {
        margin-top: 18px;
        display: flex;
        gap: 12px;
        align-items: flex-start;
    }

    .neon-book-cover {
        width: 64px;
        height: 88px;
        border-radius: 8px;
        object-fit: cover;
        box-shadow: 0 6px 20px rgba(15, 23, 42, 0.85);
        border: 1px solid rgba(191, 219, 254, 0.35);
    }

    .neon-book-title {
        font-weight: 600;
        font-size: .95rem;
        margin-bottom: 4px;
    }

    .neon-book-desc {
        font-size: .78rem;
        color: #e5e7eb;
        opacity: .9;
    }

    .neon-small-note {
        font-size: .75rem;
        color: #9ca3af;
        margin-top: 12px;
    }

    .neon-right {
        padding: 18px 18px 18px 16px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .borrow-info-box {
        font-size: .78rem;
        color: #cbd5f5;
        background: linear-gradient(90deg, rgba(15, 23, 42, 0.9), rgba(30, 64, 175, 0.5));
        border-radius: 999px;
        padding: 7px 12px;
        margin-bottom: 12px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .borrow-info-dot {
        width: 6px;
        height: 6px;
        border-radius: 999px;
        background: #22c55e;
        box-shadow: 0 0 10px rgba(34, 197, 94, 0.85);
    }

    .modal-neon label.form-label {
        font-size: .78rem;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #9ca3af;
        margin-bottom: 4px;
    }

    .modal-neon .form-control,
    .modal-neon .form-select {
        background: #020617;
        border-radius: 999px;
        border: 1px solid #1f2937;
        font-size: .85rem;
        color: #e5e7eb;
        padding-inline: 14px;
    }

    .modal-neon .form-control:focus,
    .modal-neon .form-select:focus {
        border-color: #38bdf8;
        box-shadow: 0 0 0 1px rgba(56, 189, 248, 0.35);
        background: #020617;
        color: #e5e7eb;
    }

    .modal-neon .form-control::placeholder {
        color: #6b7280;
    }

    .modal-neon .btn-primary {
        border-radius: 999px;
        padding-inline: 20px;
        font-size: .85rem;
        background: linear-gradient(90deg, #22c55e, #16a34a);
        border: none;
        box-shadow: 0 0 18px rgba(16, 185, 129, 0.7);
    }

    .modal-neon .btn-primary:hover {
        filter: brightness(1.05);
    }

    .modal-neon .btn-light {
        border-radius: 999px;
        font-size: .82rem;
    }

    @media (max-width: 768px) {
        .neon-layout {
            grid-template-columns: 1fr;
        }
        .neon-left {
            border-right: none;
            border-bottom: 1px solid rgba(37, 99, 235, 0.4);
        }
        .neon-left::after {
            border-right: none;
            border-bottom: 2px solid rgba(56, 189, 248, 0.8);
            border-radius: 0 0 20px 20px;
        }
    }
</style>

<div class="catalog-wrapper">

    {{-- BAR ATAS --}}
    <div class="catalog-header-bar">
        <div>
            <div class="catalog-header-bar-title">Perpustakaan SMPN 1 Bandung</div>
            <div class="catalog-header-bar-sub">
                Jelajahi koleksi buku dan temukan bacaan terbaik untuk mendukung belajar Anda.
            </div>
        </div>
        <div class="text-end">
            <a href="{{ route('student.borrow.status') }}" class="btn btn-light btn-sm">
                🎫 Cek Status Peminjaman
            </a>
        </div>
    </div>

    {{-- KONTEN UTAMA --}}
    <div class="catalog-main-card">
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
            <div>
                <div class="catalog-main-title">Katalog Buku Perpustakaan</div>
                <div class="catalog-main-sub">
                    Jelajahi &amp; lihat ketersediaan koleksi buku Perpustakaan SMPN 1 Bandung.
                </div>
            </div>

            {{-- FORM PENCARIAN --}}
            <form class="catalog-search-row" method="GET" action="{{ route('catalog') }}">
                <input
                    type="text"
                    name="q"
                    class="form-control form-control-sm"
                    placeholder="Cari judul / pengarang / penerbit / tahun"
                    value="{{ $q }}"
                >
                <button class="btn btn-sm btn-primary" type="submit">Cari</button>
            </form>
        </div>

        {{-- ALERT --}}
        @if (session('success'))
            <div class="alert alert-success py-2">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger py-2">{{ session('error') }}</div>
        @endif

        {{-- TABEL BUKU --}}
        <div class="table-responsive">
            <table class="table table-sm catalog-table align-middle mb-1">
                <thead>
                <tr>
                    <th style="width:80px;">Cover</th>
                    <th>Judul & Deskripsi</th>
                    <th>Pengarang</th>
                    <th>Penerbit</th>
                    <th style="width:90px;">Tahun</th>
                    <th style="width:110px;">Status</th>
                    <th style="width:90px;">Stok</th>
                    <th style="width:120px;">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($books as $book)
                    @php $tersedia = $book->stock > 0; @endphp
                    <tr>
                        {{-- COVER --}}
                        <td>
                            @if($book->cover)
                                <img src="{{ asset('storage/'.$book->cover) }}"
                                     alt="Cover {{ $book->title }}"
                                     class="book-cover-thumb">
                            @else
                                <div class="book-cover-thumb d-flex align-items-center justify-content-center"
                                     style="background:#e5e7eb;color:#6b7280;font-size:.75rem;">
                                    No Cover
                                </div>
                            @endif
                        </td>

                        {{-- JUDUL + DESKRIPSI --}}
                        <td>
                            <div class="book-title-cell">{{ $book->title }}</div>
                            @if($book->description)
                                <div class="book-desc">
                                    {{ \Illuminate\Support\Str::limit($book->description, 80) }}
                                </div>
                            @endif
                        </td>

                        <td>{{ $book->author ?: '-' }}</td>
                        <td>{{ $book->publisher ?: '-' }}</td>
                        <td>{{ $book->year ?: '-' }}</td>

                        <td>
                            @if ($tersedia)
                                <span class="badge-status badge-success">Tersedia</span>
                            @else
                                <span class="badge-status badge-danger">Habis</span>
                            @endif
                        </td>

                        <td>
                            <span class="stock-pill {{ $tersedia ? 'stock-available' : 'stock-empty' }}">
                                {{ $book->stock ?? 0 }}
                            </span>
                        </td>

                        <td>
                            <button
                                class="btn btn-sm btn-primary"
                                @if(!$tersedia) disabled @endif
                                data-bs-toggle="modal"
                                data-bs-target="#borrowModal"
                                data-book-id="{{ $book->id }}"
                                data-book-title="{{ $book->title }}"
                                data-book-cover="{{ $book->cover ? asset('storage/'.$book->cover) : '' }}"
                                data-book-desc="{{ $book->description ?? '' }}"
                            >
                                Pinjam Buku
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-3">
                            Belum ada data buku yang tercatat.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINASI --}}
        <div class="d-flex justify-content-between align-items-center mt-1">
            <small class="text-muted">
                Menampilkan {{ $books->firstItem() ?? 0 }}–{{ $books->lastItem() ?? 0 }}
                dari {{ $books->total() }} buku.
            </small>
            <div>
                {{ $books->appends(['q' => $q])->onEachSide(1)->links('vendor.pagination.custom-modern') }}
            </div>
        </div>

        {{-- LINK KEMBALI --}}
        <div class="catalog-footer-link mt-2">
            <a href="{{ route('home') }}">&larr; Kembali ke Beranda</a>
        </div>
    </div>
</div>

{{-- MODAL FORM PEMINJAMAN (NEON) --}}
<div class="modal fade modal-neon" id="borrowModal" tabindex="-1" aria-labelledby="borrowModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="POST" action="{{ route('student.borrow.store') }}">
            @csrf
            <input type="hidden" name="book_id" id="borrowBookId">

            <div class="neon-layout">
                {{-- PANEL KIRI --}}
                <div class="neon-left">
                    <div>
                        <div class="neon-welcome">PEMINJAMAN</div>
                        <div class="neon-title">Form Peminjaman Buku</div>
                        <div class="neon-sub">
                            Lengkapi data di samping untuk mengajukan peminjaman.
                            Data akan langsung tersimpan di sistem perpustakaan.
                        </div>

                        <div class="neon-book-info">
                            <img id="borrowBookCoverPreview"
                                 src=""
                                 alt="Cover"
                                 class="neon-book-cover d-none">
                            <div>
                                <div class="neon-book-title" id="borrowBookTitleText">Judul Buku</div>
                                <div class="neon-book-desc" id="borrowBookDescText">
                                    Deskripsi singkat buku akan tampil di sini.
                                </div>
                            </div>
                        </div>

                        <div class="neon-small-note">
                            *Pastikan NIS sesuai dengan data anggota perpustakaan.
                        </div>
                    </div>
                </div>

                {{-- PANEL KANAN --}}
                <div class="neon-right">
                    <div>
                        <div class="borrow-info-box">
                            <span class="borrow-info-dot"></span>
                            <span>
                                Maksimal <strong>3 buku aktif</strong> per siswa. Tanggal pinjam dari hari ini,
                                jatuh tempo otomatis dari lama pinjam.
                            </span>
                        </div>

                        {{-- NAMA --}}
                        <div class="mb-2">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="student_name" class="form-control" required>
                        </div>

                        {{-- NIS --}}
                        <div class="mb-2">
                            <label class="form-label">NIS</label>
                            <input type="text" name="student_nis" class="form-control" required>
                        </div>

                        {{-- KELAS --}}
                        <div class="mb-2">
                            <label class="form-label">Kelas</label>
                            <input type="text" name="student_class" class="form-control"
                                   placeholder="Contoh: 7A / 8B / 9C" required>
                        </div>

                        {{-- LAMA PINJAM --}}
                        <div class="mb-2">
                            <label class="form-label">Lama Pinjam</label>
                            <select name="duration" class="form-select" required>
                                @for($i = 1; $i <= 7; $i++)
                                    <option value="{{ $i }}">{{ $i }} hari</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-3">
                        <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm">
                            Kirim Permintaan Pinjam
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- SCRIPT UNTUK ISI DATA MODAL + AUTO-FILL NIS --}}
@push('scripts')
<script>
    // Isi data buku ke dalam modal saat tombol "Pinjam Buku" diklik
    const borrowModal = document.getElementById('borrowModal');

    borrowModal.addEventListener('show.bs.modal', function (event) {
        const button   = event.relatedTarget;
        const bookId   = button.getAttribute('data-book-id');
        const title    = button.getAttribute('data-book-title');
        const coverUrl = button.getAttribute('data-book-cover');
        const desc     = button.getAttribute('data-book-desc') || '';

        document.getElementById('borrowBookId').value = bookId;
        document.getElementById('borrowBookTitleText').innerText = title;

        const descEl = document.getElementById('borrowBookDescText');
        descEl.innerText = desc || 'Tidak ada deskripsi buku.';

        const imgEl = document.getElementById('borrowBookCoverPreview');
        if (coverUrl) {
            imgEl.src = coverUrl;
            imgEl.classList.remove('d-none');
        } else {
            imgEl.classList.add('d-none');
        }
    });

    // AUTO-FILL: ketika NIS diubah, ambil data anggota dan isi nama + kelas
    document.addEventListener('DOMContentLoaded', function () {
        const nisInput = document.querySelector('input[name="student_nis"]');
        if (!nisInput) return;

        nisInput.addEventListener('change', function () {
            const nis = this.value.trim();
            if (!nis) return;

            fetch('/api/member/' + encodeURIComponent(nis))
                .then(res => res.ok ? res.json() : Promise.reject())
                .then(data => {
                    if (data.not_found) {
                        alert('NIS tidak ditemukan!');
                        return;
                    }

                    const nameInput  = document.querySelector('input[name="student_name"]');
                    const classInput = document.querySelector('input[name="student_class"]');

                    if (nameInput)  nameInput.value  = data.name  || '';
                    if (classInput) classInput.value = data.class || '';
                })
                .catch(() => {
                    alert('Gagal mengambil data siswa. Coba lagi nanti.');
                });
        });
    });
</script>
@endpush
@endsection

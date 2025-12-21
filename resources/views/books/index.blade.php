@extends('layouts.app')

@section('content')
<style>
    .page-header-box {
        border-radius: 14px;
        background: linear-gradient(135deg, #1e88ff, #5479f7);
        color: #fff;
        padding: 18px 22px;
        margin-bottom: 20px;
        box-shadow: 0 8px 18px rgba(0,0,0,0.15);
    }

    .page-header-box h3 {
        margin: 0;
        font-weight: 700;
    }

    .page-header-box p {
        margin: 4px 0 0 0;
        opacity: .9;
    }

    .img-cover {
        width: 55px;
        height: 75px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #ddd;
    }
</style>

<div class="container py-4">

    {{-- Header --}}
    <div class="page-header-box d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h3>📚 Data Buku Perpustakaan</h3>
            <p>Kelola koleksi buku yang tersedia di perpustakaan.</p>
        </div>
        <div>

            {{-- 🔵 Tambahan: Tombol Import Excel --}}
            <a href="{{ route('books.import.form') }}" class="btn btn-info me-2">
                📥 Import Excel
            </a>

            <a href="{{ route('admin.dashboard') }}" class="btn btn-light btn-sm me-2">
                ← Kembali ke Dashboard
            </a>

            <a href="{{ route('books.create') }}" class="btn btn-warning">
                + Tambah Buku
            </a>
        </div>
    </div>

    {{-- pesan sukses --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- card tabel --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;">No.</th>
                            <th>Cover</th>
                            <th>Judul</th>
                            <th>Pengarang</th>
                            <th>Penerbit</th>
                            <th style="width: 100px;">Tahun</th>
                            <th>Deskripsi</th>
                            <th class="text-center" style="width: 90px;">Stok</th>
                            <th class="text-center" style="width: 170px;">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($books as $book)
                            <tr>
                                <td>{{ $loop->iteration }}</td>

                                <td>
                                    @if ($book->cover)
                                        <img src="{{ asset('storage/' . $book->cover) }}" class="img-cover">
                                    @else
                                        <span class="text-muted">Tidak ada</span>
                                    @endif
                                </td>

                                <td>{{ $book->title }}</td>
                                <td>{{ $book->author }}</td>
                                <td>{{ $book->publisher }}</td>
                                <td>{{ $book->year }}</td>

                                <td>{{ Str::limit($book->description, 50, '...') }}</td>

                                <td class="text-center">
                                    @if ($book->stock > 0)
                                        <span class="badge bg-success">{{ $book->stock }}</span>
                                    @else
                                        <span class="badge bg-secondary">Habis</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <a href="{{ route('books.edit', $book) }}" class="btn btn-sm btn-warning me-1">
                                        Ubah
                                    </a>

                                    <form action="{{ route('books.destroy', $book) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Yakin ingin menghapus buku ini?');">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    Belum ada data buku.
                                    <strong>Tambah Buku</strong> untuk mulai menambahkan data.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container py-5">

    <div class="mb-4">
        <h3 class="mb-0">Riwayat Peminjaman</h3>
        <small class="text-muted">
            Riwayat peminjaman atas nama {{ session('student_name') }} (NIS: {{ session('student_id') }})
        </small>
    </div>

    @if ($history->isEmpty())
        <div class="alert alert-info">
            Belum ada riwayat peminjaman yang tercatat untuk akun ini.
        </div>
    @else
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0">Daftar Riwayat Peminjaman</h6>
                    <small class="text-muted">
                        Menampilkan semua peminjaman buku yang pernah kamu lakukan.
                    </small>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Judul Buku</th>
                            <th>Tanggal Pinjam</th>
                            <th>Tanggal Kembali</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($history as $index => $borrowing)
                            @php
                                // ambil data buku (cara sederhana)
                                $book = \App\Models\Book::find($borrowing->book_id);

                                $borrowDate = \Carbon\Carbon::parse($borrowing->borrow_date);
                                $returnDate = $borrowing->return_date
                                    ? \Carbon\Carbon::parse($borrowing->return_date)
                                    : null;

                                // cek apakah terlambat
                                $dueDate = $borrowDate->copy()->addDays(7);
                                $isLate = $borrowing->status === 'dipinjam'
                                    ? $dueDate->isPast()
                                    : false;
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="fw-semibold">
                                    {{ $book->title ?? 'Tidak diketahui' }}
                                </td>
                                <td>{{ $borrowDate->format('d/m/Y') }}</td>
                                <td>
                                    @if ($returnDate)
                                        {{ $returnDate->format('d/m/Y') }}
                                    @else
                                        <span class="text-muted">Belum kembali</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($borrowing->status === 'dipinjam')
                                        <span class="badge bg-warning text-dark">Dipinjam</span>
                                    @elseif ($borrowing->status === 'kembali')
                                        <span class="badge bg-success">Kembali</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($borrowing->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($borrowing->status === 'dipinjam')
                                        @if ($isLate)
                                            <span class="text-danger small">
                                                Melewati jatuh tempo ({{ $dueDate->format('d/m/Y') }})
                                            </span>
                                        @else
                                            <span class="text-muted small">
                                                Jatuh tempo {{ $dueDate->format('d/m/Y') }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-muted small">
                                            Selesai
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <a href="{{ route('student.dashboard') }}" class="btn btn-link mt-3">
        &larr; Kembali ke Dashboard
    </a>
</div>
@endsection

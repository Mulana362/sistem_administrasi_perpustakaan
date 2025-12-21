@extends('layouts.app')

@section('content')
<div class="container py-5">

    {{-- FLASH MESSAGE --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="mb-4">
        <h3 class="mb-0">Buku yang Sedang Dipinjam</h3>
        <small class="text-muted">
            Data peminjaman aktif atas nama {{ session('student_name') }} (NIS: {{ session('student_id') }})
        </small>
    </div>

    @if ($activeBorrowings->isEmpty())
        <div class="alert alert-info">
            Saat ini kamu <strong>belum memiliki peminjaman aktif</strong>.
        </div>
    @else
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0">Daftar Peminjaman Aktif</h6>
                    <small class="text-muted">Buku yang masih tercatat “dipinjam” di sistem</small>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Judul Buku</th>
                            <th>Tanggal Pinjam</th>
                            <th>Jatuh Tempo</th>
                            <th>Status</th>
                            <th>Perkiraan Denda</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($activeBorrowings as $index => $borrowing)
                            @php
                                $book = \App\Models\Book::find($borrowing->book_id);

                                $borrowDate = \Carbon\Carbon::parse($borrowing->borrow_date);
                                $dueDate    = $borrowDate->copy()->addDays(7);

                                $isLate   = $dueDate->isPast();
                                $daysLate = $isLate ? $dueDate->diffInDays(now()) : 0;

                                $finePerDay    = 1000;
                                $estimatedFine = $daysLate * $finePerDay;
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="fw-semibold">
                                    {{ $book->title ?? 'Tidak diketahui' }}
                                </td>
                                <td>{{ $borrowDate->format('d/m/Y') }}</td>
                                <td>{{ $dueDate->format('d/m/Y') }}</td>
                                <td>
                                    @if ($isLate)
                                        <span class="badge bg-danger">
                                            Terlambat {{ $daysLate }} hari
                                        </span>
                                    @else
                                        <span class="badge bg-success">
                                            On Time
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if ($isLate && $estimatedFine > 0)
                                        Rp {{ number_format($estimatedFine, 0, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('student.borrowings.return', $borrowing->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Yakin ingin mengembalikan buku ini?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary">
                                            Kembalikan Buku
                                        </button>
                                    </form>
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

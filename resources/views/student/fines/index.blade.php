@extends('layouts.app')

@section('content')
<div class="container py-5">

    <div class="mb-4">
        <h3 class="mb-0">Cek Denda Peminjaman</h3>
        <small class="text-muted">
            Denda keterlambatan atas nama {{ session('student_name') }} (NIS: {{ session('student_id') }})
        </small>
    </div>

    @php
        use Carbon\Carbon;

        $finePerDay = 1000; // Rp1.000/hari/buku
        $totalEstimatedFine = 0;
    @endphp

    @if ($borrowings->isEmpty())
        <div class="alert alert-info">
            Belum ada peminjaman yang tercatat, sehingga <strong>tidak ada denda</strong>.
        </div>
    @else
        @php
            // hitung total denda dari semua peminjaman yang terlambat
            foreach ($borrowings as $borrowing) {
                $borrowDate = Carbon::parse($borrowing->borrow_date);
                $dueDate = $borrowDate->copy()->addDays(7);

                // kalau masih dipinjam dan sudah lewat jatuh tempo → denda berjalan
                if ($borrowing->status === 'dipinjam' && $dueDate->isPast()) {
                    $daysLate = $dueDate->diffInDays(now());
                    $totalEstimatedFine += $daysLate * $finePerDay;
                }

                // kalau sudah kembali dan kembali setelah jatuh tempo → denda (riwayat)
                if ($borrowing->status === 'kembali' && $borrowing->return_date) {
                    $returnDate = Carbon::parse($borrowing->return_date);
                    if ($returnDate->greaterThan($dueDate)) {
                        $daysLate = $dueDate->diffInDays($returnDate);
                        $totalEstimatedFine += $daysLate * $finePerDay;
                    }
                }
            }
        @endphp

        {{-- Ringkasan total denda --}}
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm border-0 bg-gradient-danger text-white">
                    <div class="card-body">
                        <h6 class="text-uppercase small fw-bold mb-2">Perkiraan Total Denda</h6>
                        <div class="display-5 fw-semibold">
                            @if ($totalEstimatedFine > 0)
                                Rp {{ number_format($totalEstimatedFine, 0, ',', '.') }}
                            @else
                                Rp 0
                            @endif
                        </div>
                        <small class="opacity-75">
                            Perhitungan denda berdasarkan keterlambatan pengembalian buku
                            (Rp{{ number_format($finePerDay, 0, ',', '.') }} / hari / buku).
                            Nominal akhir akan dikonfirmasi oleh petugas perpustakaan.
                        </small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Detail peminjaman & denda --}}
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0">Rincian Denda per Peminjaman</h6>
                    <small class="text-muted">
                        Menampilkan status peminjaman, keterlambatan, dan estimasi denda.
                    </small>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Judul Buku</th>
                            <th>Tgl Pinjam</th>
                            <th>Jatuh Tempo</th>
                            <th>Tgl Kembali</th>
                            <th>Status</th>
                            <th>Keterlambatan</th>
                            <th>Estimasi Denda</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($borrowings as $index => $borrowing)
                            @php
                                $book = \App\Models\Book::find($borrowing->book_id);

                                $borrowDate = Carbon::parse($borrowing->borrow_date);
                                $dueDate = $borrowDate->copy()->addDays(7);
                                $returnDate = $borrowing->return_date
                                    ? Carbon::parse($borrowing->return_date)
                                    : null;

                                $daysLate = 0;
                                $fine = 0;
                                $lateText = '-';

                                if ($borrowing->status === 'dipinjam') {
                                    if ($dueDate->isPast()) {
                                        $daysLate = $dueDate->diffInDays(now());
                                        $fine = $daysLate * $finePerDay;
                                        $lateText = $daysLate . ' hari (berjalan)';
                                    }
                                } elseif ($borrowing->status === 'kembali' && $returnDate) {
                                    if ($returnDate->greaterThan($dueDate)) {
                                        $daysLate = $dueDate->diffInDays($returnDate);
                                        $fine = $daysLate * $finePerDay;
                                        $lateText = $daysLate . ' hari (selesai)';
                                    }
                                }

                                $fineFormatted = $fine > 0 ? 'Rp ' . number_format($fine, 0, ',', '.') : '-';
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="fw-semibold">{{ $book->title ?? 'Tidak diketahui' }}</td>
                                <td>{{ $borrowDate->format('d/m/Y') }}</td>
                                <td>{{ $dueDate->format('d/m/Y') }}</td>
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
                                    @if ($daysLate > 0)
                                        <span class="text-danger small">{{ $lateText }}</span>
                                    @else
                                        <span class="text-muted small">Tidak terlambat</span>
                                    @endif
                                </td>
                                <td>{{ $fineFormatted }}</td>
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

@extends('layouts.app')

@section('title', 'Data Buku Hilang & Rusak')

@section('content')
<div class="container py-4">

    {{-- NOTIF --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">📦 Data Buku Hilang / Rusak</h4>
            <small class="text-muted">Daftar laporan kasus buku dari peminjaman</small>
        </div>

        <a href="{{ route('borrowings.index', ['active_tab' => 'issue']) }}" class="btn btn-secondary btn-sm">
            ← Kembali
        </a>
    </div>

    {{-- TABLE --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Nama</th>
                            <th>NIS</th>
                            <th>Kelas</th>
                            <th>Buku</th>
                            <th>Jenis</th>
                            <th>Denda</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookIssues as $issue)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ \Carbon\Carbon::parse($issue->reported_at)->format('d/m/Y') }}</td>
                                <td>{{ $issue->student_name }}</td>
                                <td>{{ $issue->student_nis }}</td>
                                <td>{{ $issue->student_class }}</td>
                                <td>{{ optional($issue->book)->title }}</td>

                                <td>
                                    @if($issue->issue_type == 'Hilang')
                                        <span class="badge bg-danger">Hilang</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Rusak</span>
                                    @endif
                                </td>

                                <td>Rp {{ number_format($issue->fine_amount ?? 0,0,',','.') }}</td>

                                <td>
                                    @if($issue->status == 'Dilaporkan')
                                        <span class="badge bg-secondary">Dilaporkan</span>
                                    @elseif($issue->status == 'Diproses')
                                        <span class="badge bg-primary">Diproses</span>
                                    @else
                                        <span class="badge bg-success">Selesai</span>
                                    @endif
                                </td>

                                <td class="d-flex gap-1">
                                    <a href="{{ route('book-issues.edit', $issue->id) }}" class="btn btn-sm btn-outline-primary">
                                        Edit
                                    </a>

                                    @if($issue->status == 'Dilaporkan')
                                        <form action="{{ route('book-issues.processing', $issue->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn btn-sm btn-warning">Proses</button>
                                        </form>
                                    @endif

                                    @if($issue->status != 'Selesai')
                                        <form action="{{ route('book-issues.finished', $issue->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn btn-sm btn-success">Selesai</button>
                                        </form>
                                    @endif

                                    <form action="{{ route('book-issues.destroy', $issue->id) }}" method="POST"
                                          onsubmit="return confirm('Hapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">
                                    Belum ada data buku hilang / rusak
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

@extends('layouts.app')

@section('title', 'Status Peminjaman')

@section('content')
<div class="container py-4">
    <h3 class="mb-3">📚 Status Peminjaman Buku</h3>

    @if ($borrowings->isEmpty())
        <div class="alert alert-warning">Tidak ada peminjaman yang ditemukan untuk NIS ini.</div>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Tanggal Pinjam</th>
                    <th>Buku</th>
                    <th>Jatuh Tempo</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($borrowings as $b)
                    <tr>
                        <td>{{ $b->borrow_date }}</td>
                        <td>{{ optional($b->book)->title }}</td>
                        <td>{{ $b->due_date }}</td>
                        <td>{{ $b->status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <a href="{{ route('catalog') }}" class="btn btn-secondary mt-3">← Kembali ke Katalog</a>
</div>
@endsection

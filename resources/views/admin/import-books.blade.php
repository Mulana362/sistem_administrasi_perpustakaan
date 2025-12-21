@extends('layouts.app')

@section('title', 'Import Data Buku')

@section('content')
<div class="container py-4" style="max-width: 600px;">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-2">📥 Import Data Buku</h4>
            <p class="text-muted">
                Upload file Excel (.xlsx / .xls / .csv) dengan format kolom:<br>
                <strong>Judul | Pengarang | Penerbit | Tahun | Stok | Deskripsi</strong>
            </p>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('books.import') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Pilih File Excel</label>
                    <input type="file" name="file" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-2">
                    Import Buku
                </button>

                <a href="{{ route('books.index') }}" class="btn btn-secondary w-100">
                    Kembali ke Data Buku
                </a>
            </form>
        </div>
    </div>
</div>
@endsection

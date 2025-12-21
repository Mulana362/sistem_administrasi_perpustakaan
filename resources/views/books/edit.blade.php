@extends('layouts.app')

@section('content')
<style>
    .page-wrapper {
        max-width: 650px;
        margin: auto;
        background: #ffffff;
        padding: 30px;
        border-radius: 14px;
        box-shadow: 0 10px 28px rgba(0,0,0,0.12);
        margin-top: 40px;
    }

    .page-title {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 20px;
        color: #2d3436;
    }

    label {
        font-weight: 600;
        color: #2d3436;
    }

    .form-control {
        border-radius: 8px !important;
        padding: 12px !important;
        border: 1px solid #dcdde1;
        background: #f5f6fa;
    }

    .form-control:focus {
        border-color: #0984e3;
        box-shadow: 0 0 0 2px rgba(9,132,227,0.15);
        background: #fff;
    }

    .btn-primary {
        background-color: #0984e3 !important;
        border-radius: 8px;
        padding: 10px 16px;
        font-weight: 600;
    }

    .btn-secondary {
        border-radius: 8px;
        padding: 10px 16px;
        font-weight: 600;
    }
</style>

<div class="page-wrapper">

    <h2 class="page-title">Edit Data Buku</h2>

    {{-- ERROR MESSAGE --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Periksa input anda:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('books.update', $book->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- JUDUL --}}
        <div class="mb-3">
            <label>Judul Buku</label>
            <input type="text" name="title" class="form-control"
                   value="{{ $book->title }}" required>
        </div>

        {{-- PENGARANG --}}
        <div class="mb-3">
            <label>Pengarang</label>
            <input type="text" name="author" class="form-control"
                   value="{{ $book->author }}" required>
        </div>

        {{-- PENERBIT --}}
        <div class="mb-3">
            <label>Penerbit</label>
            <input type="text" name="publisher" class="form-control"
                   value="{{ $book->publisher }}" required>
        </div>

        {{-- TAHUN --}}
        <div class="mb-3">
            <label>Tahun Terbit</label>
            <input type="number" name="year" class="form-control"
                   min="1900" max="{{ date('Y') }}"
                   value="{{ $book->year }}" required>
        </div>

        {{-- STOK --}}
        <div class="mb-3">
            <label>Stok Buku</label>
            <input type="number" name="stock" class="form-control"
                   min="0" value="{{ $book->stock }}" required>
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-2">
            Update Buku
        </button>

        <a href="{{ route('books.index') }}" class="btn btn-secondary w-100">
            Batal / Kembali
        </a>
    </form>

</div>
@endsection

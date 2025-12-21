@extends('layouts.app')

@section('content')
<h2>Edit Tamu</h2>
<form action="{{ route('visitors.update', $visitor->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="mb-2">
        <label>Nama</label>
        <input type="text" name="name" value="{{ $visitor->name }}" class="form-control">
    </div>
    <div class="mb-2">
        <label>Kelas</label>
        <input type="text" name="class" value="{{ $visitor->class }}" class="form-control">
    </div>
    <div class="mb-2">
        <label>Tanggal Hadir</label>
        <input type="date" name="visit_date" value="{{ $visitor->visit_date }}" class="form-control">
    </div>
    <button type="submit" class="btn btn-success">Update</button>
</form>
@endsection

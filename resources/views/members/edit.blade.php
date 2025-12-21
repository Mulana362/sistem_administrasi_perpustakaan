@extends('layouts.app')

@section('content')
<h2>Edit Anggota</h2>
<form action="{{ route('members.update', $member->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="mb-2">
        <label>NIS</label>
        <input type="text" name="nis" value="{{ $member->nis }}" class="form-control">
    </div>
    <div class="mb-2">
        <label>Nama</label>
        <input type="text" name="name" value="{{ $member->name }}" class="form-control">
    </div>
    <div class="mb-2">
        <label>Kelas</label>
        <input type="text" name="class" value="{{ $member->class }}" class="form-control">
    </div>
    <button type="submit" class="btn btn-success">Update</button>
</form>
@endsection

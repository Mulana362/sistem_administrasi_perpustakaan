@extends('layouts.app') {{-- sesuaikan layout kamu kalau beda --}}
@section('title', 'Edit Anggota')

@section('content')
<style>
  /* ====== Page background ====== */
  .page-wrap{
    min-height: calc(100vh - 80px);
    background:
      radial-gradient(900px 400px at 10% -10%, rgba(13,110,253,.18), transparent 60%),
      radial-gradient(700px 350px at 100% 10%, rgba(32,201,151,.14), transparent 55%),
      #f6f8fb;
    padding: 32px 0;
  }

  /* ====== Top title ====== */
  .page-title{
    font-weight: 800;
    letter-spacing: -.2px;
    margin: 0;
  }
  .page-subtitle{
    color: #667085;
    margin: 0;
    font-size: .95rem;
  }

  /* ====== Card ====== */
  .form-card{
    border: 0;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 18px 45px rgba(16,24,40,.10);
    background: #fff;
  }

  /* ====== Header ====== */
  .form-card .card-header{
    border: 0;
    padding: 18px 22px;
    background: linear-gradient(135deg, #0d6efd 0%, #20c997 100%);
    color: #fff;
  }
  .header-subtitle{
    opacity: .92;
    font-size: .95rem;
  }

  /* ====== Inputs ====== */
  .form-label{
    font-weight: 700;
    color: #344054;
  }
  .form-control{
    border-radius: 12px;
    padding: 12px 14px;
    border-color: #e5e7eb;
  }
  .form-control:focus{
    border-color: rgba(13,110,253,.45);
    box-shadow: 0 0 0 .2rem rgba(13,110,253,.12);
  }
  .hint{
    color: #667085;
    font-size: .9rem;
  }

  /* ====== Buttons ====== */
  .btn-pill{
    border-radius: 999px;
    padding: 10px 18px;
    font-weight: 700;
  }
  .btn-soft{
    background: rgba(13,110,253,.10);
    color: #0d6efd;
    border: 1px solid rgba(13,110,253,.15);
  }
  .btn-soft:hover{
    background: rgba(13,110,253,.14);
    color: #0b5ed7;
  }

  /* ====== Small badge icon ====== */
  .header-icon{
    width: 42px;
    height: 42px;
    border-radius: 14px;
    background: rgba(255,255,255,.18);
    display:flex;
    align-items:center;
    justify-content:center;
    font-size: 20px;
  }
</style>

<div class="page-wrap">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-md-9 col-lg-7 col-xl-6">

        {{-- Header page --}}
        <div class="d-flex align-items-start align-items-sm-center justify-content-between gap-2 mb-3">
          <div>
            <p class="page-subtitle">Perpustakaan SMPN 1 Bandung</p>
            <h2 class="page-title">Edit Anggota</h2>
          </div>

          {{-- tombol kembali --}}
          <a href="{{ route('members.index') }}" class="btn btn-soft btn-pill">
            ← Kembali
          </a>
        </div>

        {{-- Alert error global (opsional tapi membantu) --}}
        @if ($errors->any())
          <div class="alert alert-danger">
            <strong>Periksa lagi ya!</strong> Ada beberapa input yang belum valid.
          </div>
        @endif

        <div class="card form-card">
          <div class="card-header">
            <div class="d-flex align-items-center gap-2">
              <div class="header-icon">✏️</div>
              <div>
                <div style="font-weight:800; font-size:1.05rem;">Form Edit Data Anggota</div>
                <div class="header-subtitle">Perbarui NIS, nama, dan kelas dengan benar.</div>
              </div>
            </div>
          </div>

          <div class="card-body p-4">
            <form action="{{ route('members.update', $member->id) }}" method="POST" class="vstack gap-3">
              @csrf
              @method('PUT')

              {{-- NIS --}}
              <div>
                <label class="form-label">NIS</label>
                <input
                  type="text"
                  name="nis"
                  value="{{ old('nis', $member->nis) }}"
                  class="form-control @error('nis') is-invalid @enderror"
                  placeholder="Contoh: 14022019"
                  autocomplete="off"
                >
                @error('nis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <div class="hint mt-1">Gunakan angka sesuai data siswa.</div>
              </div>

              {{-- Nama --}}
              <div>
                <label class="form-label">Nama</label>
                <input
                  type="text"
                  name="name"
                  value="{{ old('name', $member->name) }}"
                  class="form-control @error('name') is-invalid @enderror"
                  placeholder="Contoh: Agus"
                >
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              {{-- Kelas --}}
              <div>
                <label class="form-label">Kelas</label>
                <input
                  type="text"
                  name="class"
                  value="{{ old('class', $member->class) }}"
                  class="form-control @error('class') is-invalid @enderror"
                  placeholder="Contoh: 9B"
                >
                @error('class') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <hr class="my-2">

              <div class="d-flex flex-column flex-sm-row gap-2">
                <button type="submit" class="btn btn-success btn-pill w-100">
                  ✅ Update Anggota
                </button>
                <a href="{{ route('members.index') }}" class="btn btn-outline-secondary btn-pill w-100">
                  Batal
                </a>
              </div>
            </form>
          </div>
        </div>

        <div class="text-center mt-3 text-muted" style="font-size:.85rem;">
          Tip: Pastikan NIS tidak duplikat dan nama sesuai data siswa.
        </div>

      </div>
    </div>
  </div>
</div>
@endsection

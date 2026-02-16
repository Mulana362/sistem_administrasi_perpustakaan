@extends('layouts.app')

@section('title', 'Form Pengajuan Peminjaman Buku')

@section('content')
<style>
    body { background: radial-gradient(circle at top left, #0f172a 0, #020617 55%, #000 100%); }
    .borrow-page-wrapper{min-height:calc(100vh - 80px);display:flex;align-items:center;justify-content:center;padding:32px 12px;}
    .borrow-shell{position:relative;max-width:980px;width:100%;border-radius:26px;padding:2px;background:linear-gradient(135deg,rgba(59,130,246,.9),rgba(20,184,166,.9));box-shadow:0 0 0 1px rgba(15,23,42,.6),0 28px 90px rgba(15,23,42,.9);}
    .borrow-card{border-radius:24px;background:radial-gradient(circle at top left,#020617 0,#020617 40%,#020617 100%);display:grid;grid-template-columns:minmax(260px,320px) minmax(0,1fr);overflow:hidden;}
    @media (max-width:768px){.borrow-card{grid-template-columns:1fr;}}
    .borrow-left{position:relative;padding:26px 24px;background:radial-gradient(circle at 0 0,rgba(59,130,246,.28),transparent 60%),radial-gradient(circle at 100% 100%,rgba(20,184,166,.25),transparent 60%);border-right:1px solid rgba(15,23,42,.8);display:flex;flex-direction:column;gap:18px;}
    @media (max-width:768px){.borrow-left{border-right:none;border-bottom:1px solid rgba(15,23,42,.8);}}
    .borrow-chip{display:inline-flex;align-items:center;gap:.6rem;padding:.25rem .75rem;border-radius:999px;background:rgba(15,23,42,.8);color:#e5e7eb;font-size:.75rem;letter-spacing:.08em;text-transform:uppercase;}
    .borrow-chip span.icon{display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;border-radius:999px;background:linear-gradient(135deg,#38bdf8,#4f46e5);box-shadow:0 0 0 1px rgba(15,23,42,.7);font-size:.9rem;}
    .borrow-title{font-size:1.65rem;font-weight:700;color:#f9fafb;letter-spacing:.03em;}
    .borrow-subtitle{font-size:.9rem;color:#cbd5f5;}
    .borrow-book-card{margin-top:8px;display:flex;gap:14px;align-items:center;padding:10px 12px;border-radius:18px;background:radial-gradient(circle at top left,rgba(56,189,248,.16),rgba(15,23,42,.9));box-shadow:0 12px 35px rgba(15,23,42,.85);}
    .borrow-book-cover{width:70px;height:96px;border-radius:12px;overflow:hidden;flex-shrink:0;box-shadow:0 10px 25px rgba(15,23,42,.9);background:#020617;}
    .borrow-book-cover img{width:100%;height:100%;object-fit:cover;}
    .borrow-book-meta-title{color:#e5e7eb;font-weight:600;margin-bottom:2px;}
    .borrow-book-meta-sub{font-size:.8rem;color:#9ca3af;}
    .borrow-book-tags{margin-top:4px;display:flex;flex-wrap:wrap;gap:4px;}
    .borrow-tag-pill{border-radius:999px;padding:.1rem .55rem;font-size:.7rem;background:rgba(15,23,42,.9);color:#a5b4fc;border:1px solid rgba(129,140,248,.6);}
    .borrow-note-small{margin-top:auto;font-size:.78rem;color:#9ca3af;}

    .borrow-right{padding:24px 26px 22px;position:relative;background:radial-gradient(circle at top right,rgba(37,99,235,.42),#020617 55%);}
    .borrow-form-group{margin-bottom:14px;}
    .borrow-form-label{font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:#9ca3af;margin-bottom:6px;}
    .borrow-input-wrap{position:relative;}
    .borrow-input-wrap span.fi{position:absolute;inset-block:0;left:10px;display:flex;align-items:center;font-size:.95rem;color:#64748b;}
    .borrow-input{background:rgba(15,23,42,.85);border-radius:999px;border:1px solid rgba(51,65,85,.9);padding:.55rem 1rem .55rem 2.3rem;color:#e5e7eb;font-size:.9rem;width:100%;outline:none!important;box-shadow:0 0 0 1px transparent;transition:border-color .14s ease,box-shadow .14s ease,background .14s ease;}
    .borrow-input::placeholder{color:#6b7280;}
    .borrow-input:focus{border-color:#38bdf8;box-shadow:0 0 0 1px rgba(56,189,248,.8),0 0 20px rgba(37,99,235,.4);background:rgba(15,23,42,.96);}
    .borrow-input[readonly]{opacity:.95;cursor:not-allowed;}
    .borrow-select{appearance:none;-webkit-appearance:none;background-image:none;cursor:pointer;}
    .borrow-input-wrap span.arrow{position:absolute;right:14px;inset-block:0;display:flex;align-items:center;font-size:.8rem;color:#64748b;pointer-events:none;}
    .borrow-btn-row{margin-top:10px;display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap;align-items:center;}
    .borrow-btn-primary{border-radius:999px;padding:.55rem 1.45rem;border:none;background:radial-gradient(circle at top left,#4f46e5,#22c55e);color:#f9fafb;font-weight:600;letter-spacing:.06em;text-transform:uppercase;font-size:.8rem;display:inline-flex;align-items:center;gap:.45rem;box-shadow:0 0 0 1px rgba(15,23,42,.7),0 12px 35px rgba(22,163,74,.6);}
    .borrow-btn-primary[disabled]{opacity:.55;cursor:not-allowed;filter:saturate(.6);}
    .borrow-btn-secondary{border-radius:999px;padding:.45rem 1.2rem;font-size:.8rem;border:1px solid rgba(148,163,184,.5);background:rgba(15,23,42,.85);color:#e5e7eb;display:inline-flex;align-items:center;gap:.4rem;}
    .borrow-btn-secondary:hover{background:rgba(15,23,42,1);}
    .borrow-meta-text{font-size:.76rem;color:#9ca3af;margin-top:4px;}

    .borrow-summary{margin-top:18px;padding:12px 14px;border-radius:18px;background:rgba(15,23,42,.9);border:1px solid rgba(55,65,81,.9);}
    .borrow-summary-title{font-size:.8rem;text-transform:uppercase;letter-spacing:.08em;color:#9ca3af;margin-bottom:6px;}
    .borrow-summary-table{width:100%;border-collapse:collapse;font-size:.82rem;color:#e5e7eb;}
    .borrow-summary-table th,.borrow-summary-table td{padding:4px 6px;}
    .borrow-summary-table th{font-weight:500;color:#9ca3af;}
    .borrow-badge-available{display:inline-flex;align-items:center;gap:.3rem;padding:.15rem .6rem;border-radius:999px;font-size:.72rem;background:rgba(22,163,74,.2);color:#bbf7d0;border:1px solid rgba(34,197,94,.7);}
    .borrow-badge-available span.dot{width:6px;height:6px;border-radius:999px;background:#22c55e;}

    /* status NIS */
    .nis-status{margin-top:8px;font-size:.78rem;color:#9ca3af;display:flex;align-items:center;gap:.45rem;}
    .nis-pill{display:inline-flex;align-items:center;gap:.35rem;padding:.12rem .55rem;border-radius:999px;font-size:.72rem;border:1px solid rgba(148,163,184,.45);background:rgba(15,23,42,.85);}
    .nis-pill.ok{border-color:rgba(34,197,94,.6);color:#bbf7d0;}
    .nis-pill.bad{border-color:rgba(239,68,68,.6);color:#fecaca;}
</style>

<div class="borrow-page-wrapper">
    <div class="borrow-shell">
        <div class="borrow-card">

            {{-- PANEL KIRI – info buku --}}
            <div class="borrow-left">
                <div class="borrow-chip">
                    <span class="icon">📚</span>
                    <span>Form Pengajuan</span>
                </div>

                <h1 class="borrow-title">Pengajuan Peminjaman Buku</h1>
                <p class="borrow-subtitle">
                    Isi data dengan benar. Maksimal <strong>3 buku aktif</strong> per siswa.
                    Pengajuan akan dicek admin sebelum jadi peminjaman.
                </p>

                <div class="borrow-book-card">
                    <div class="borrow-book-cover">
                        @if(!empty($book->cover))
                            <img src="{{ asset('storage/'.$book->cover) }}" alt="{{ $book->title }}">
                        @else
                            <img src="https://via.placeholder.com/140x200?text=BOOK" alt="Cover Buku">
                        @endif
                    </div>
                    <div>
                        <div class="borrow-book-meta-title">{{ $book->title ?? 'Judul Buku' }}</div>
                        <div class="borrow-book-meta-sub">
                            {{ $book->author ?? 'Nama Pengarang' }} &middot; {{ $book->publisher ?? 'Penerbit' }}
                        </div>
                        <div class="borrow-book-tags">
                            @if(!empty($book->year))
                                <span class="borrow-tag-pill">Terbit {{ $book->year }}</span>
                            @endif
                            <span class="borrow-tag-pill">Stok: {{ $book->stock ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <p class="borrow-note-small">
                    Dengan menekan tombol <strong>Kirim Pengajuan</strong>,
                    kamu menyetujui aturan perpustakaan sekolah.
                </p>
            </div>

            {{-- PANEL KANAN – form --}}
            <div class="borrow-right">

                @if(session('error'))
                    <div class="alert alert-danger py-2 mb-3">{{ session('error') }}</div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success py-2 mb-3">{{ session('success') }}</div>
                @endif

                <form method="POST" action="{{ route('student.borrow.store') }}">
                    @csrf
                    <input type="hidden" name="book_id" value="{{ $book->id ?? '' }}">

                    {{-- NIS --}}
                    <div class="borrow-form-group">
                        <label class="borrow-form-label">NIS</label>
                        <div class="borrow-input-wrap">
                            <span class="fi">🧾</span>
                            <input
                                id="nis"
                                type="text"
                                name="nis"
                                class="borrow-input"
                                placeholder="Contoh: 14022001"
                                value="{{ old('nis') }}"
                                required>
                        </div>
                        @error('nis')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror

                        <div id="nisStatus" class="nis-status" style="display:none;">
                            <span id="nisPill" class="nis-pill">-</span>
                            <span id="nisMsg"></span>
                        </div>
                    </div>

                    {{-- NAMA --}}
                    <div class="borrow-form-group">
                        <label class="borrow-form-label">Nama Lengkap</label>
                        <div class="borrow-input-wrap">
                            <span class="fi">👤</span>
                            <input
                                id="name"
                                type="text"
                                name="name"
                                class="borrow-input"
                                placeholder="Otomatis terisi dari NIS"
                                value="{{ old('name') }}"
                                readonly
                                required>
                        </div>
                        @error('name')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- KELAS --}}
                    <div class="borrow-form-group">
                        <label class="borrow-form-label">Kelas</label>
                        <div class="borrow-input-wrap">
                            <span class="fi">🏫</span>
                            <input
                                id="class"
                                type="text"
                                name="class"
                                class="borrow-input"
                                placeholder="Otomatis terisi dari NIS"
                                value="{{ old('class') }}"
                                readonly
                                required>
                        </div>
                        @error('class')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- LAMA PINJAM --}}
                    <div class="borrow-form-group">
                        <label class="borrow-form-label">Lama Pinjam</label>
                        <div class="borrow-input-wrap">
                            <span class="fi">⏱</span>
                            <select name="days" class="borrow-input borrow-select" required>
                                @for($i = 1; $i <= 7; $i++)
                                    <option value="{{ $i }}" {{ old('days', 3) == $i ? 'selected' : '' }}>
                                        {{ $i }} hari
                                    </option>
                                @endfor
                            </select>
                            <span class="arrow">▾</span>
                        </div>
                        @error('days')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                        <div class="borrow-meta-text">
                            Bisa dipilih 1–7 hari. Jatuh tempo dihitung otomatis.
                        </div>
                    </div>

                    <div class="borrow-btn-row">
                        <button id="submitBtn" type="submit" class="borrow-btn-primary" disabled>
                            <span>Kirim Pengajuan</span>
                            <span>➜</span>
                        </button>

                        <a href="{{ route('catalog') }}" class="borrow-btn-secondary">
                            ← Kembali ke Katalog
                        </a>
                    </div>

                    <div class="borrow-summary">
                        <div class="borrow-summary-title">Ringkasan Buku</div>
                        <table class="borrow-summary-table">
                            <tr><th style="width:28%;">Judul</th><td>{{ $book->title ?? '-' }}</td></tr>
                            <tr><th>Pengarang</th><td>{{ $book->author ?? '-' }}</td></tr>
                            <tr>
                                <th>Penerbit / Tahun</th>
                                <td>
                                    {{ $book->publisher ?? '-' }}
                                    @if(!empty($book->year)) &middot; {{ $book->year }} @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Stok</th>
                                <td>
                                    <span class="borrow-badge-available">
                                        <span class="dot"></span>
                                        {{ $book->stock ?? 0 }} tersedia
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const nisInput  = document.getElementById('nis');
    const nameInput = document.getElementById('name');
    const classInput= document.getElementById('class');
    const submitBtn = document.getElementById('submitBtn');

    const nisStatus = document.getElementById('nisStatus');
    const nisPill   = document.getElementById('nisPill');
    const nisMsg    = document.getElementById('nisMsg');

    let timer = null;

    function setStatus(type, msg){
        nisStatus.style.display = 'flex';
        nisPill.classList.remove('ok','bad');
        if(type === 'ok'){
            nisPill.classList.add('ok');
            nisPill.textContent = 'TERDAFTAR';
        }else if(type === 'bad'){
            nisPill.classList.add('bad');
            nisPill.textContent = 'TIDAK ADA';
        }else{
            nisPill.textContent = '-';
        }
        nisMsg.textContent = msg || '';
    }

    function clearStudent(){
        nameInput.value = '';
        classInput.value = '';
        submitBtn.disabled = true;
    }

    nisInput.addEventListener('input', () => {
        const nis = nisInput.value.trim();
        clearTimeout(timer);

        if(nis.length < 6){
            clearStudent();
            nisStatus.style.display = 'none';
            return;
        }

        setStatus('', 'Mengecek NIS...');
        timer = setTimeout(async () => {
            try{
                // ✅ endpoint sesuai routes/api.php kamu
                const res = await fetch(`/api/member/${encodeURIComponent(nis)}`, {
                    headers: { 'Accept': 'application/json' }
                });

                if(res.status === 404){
                    clearStudent();
                    setStatus('bad', 'NIS tidak terdaftar. Hubungi admin.');
                    return;
                }

                if(!res.ok) throw new Error('Request gagal');
                const json = await res.json();

                // ✅ response kamu pakai not_found
                if(json.not_found){
                    clearStudent();
                    setStatus('bad', 'NIS tidak terdaftar. Hubungi admin.');
                    return;
                }

                nameInput.value  = json.name ?? '';
                classInput.value = json.class ?? '';
                submitBtn.disabled = !(nameInput.value && classInput.value);

                setStatus('ok', 'Data ditemukan. Nama & kelas terisi otomatis.');
            }catch(e){
                clearStudent();
                setStatus('bad', 'Gagal cek NIS (API error).');
                console.error(e);
            }
        }, 300);
    });

    // auto cek kalau ada old nis
    if(nisInput.value.trim().length >= 6){
        nisInput.dispatchEvent(new Event('input'));
    }
});
</script>
@endpush

@extends('layouts.app')

@section('content')
<style>
    body { background: linear-gradient(120deg, #74b9ff, #a29bfe); }

    .loan-form-wrapper { min-height: calc(100vh - 80px); display: flex; justify-content: center; align-items: center; }
    .loan-form-box { background: #ffffff; padding: 30px 32px; width: 460px; border-radius: 14px; box-shadow: 0 12px 30px rgba(0,0,0,0.18); }
    .loan-form-title { text-align: center; margin-bottom: 22px; font-size: 22px; font-weight: 700; color: #2d3436; }
    .loan-form-label { font-weight: 600; margin-bottom: 6px; color: #2d3436; }

    .loan-form-input, .loan-form-select {
        width: 100%;
        padding: 11px 12px;
        border-radius: 8px;
        border: 1px solid #dfe6e9;
        background: #f7f7f7;
        font-size: 14px;
        margin-bottom: 14px;
    }
    .loan-form-input:focus, .loan-form-select:focus {
        outline: none;
        border-color: #0984e3;
        box-shadow: 0 0 0 2px rgba(9,132,227,0.15);
        background: #ffffff;
    }

    .loan-form-small { display: block; margin-top: -8px; margin-bottom: 12px; color: #636e72; font-size: 13px; }
    .loan-btn-primary, .loan-btn-secondary {
        width: 100%;
        padding: 11px;
        border-radius: 8px;
        border: none;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        margin-top: 4px;
    }
    .loan-btn-primary { background: #0984e3; color: #ffffff; }
    .loan-btn-primary:hover { background: #0768b1; }
    .loan-btn-primary:disabled { opacity: .55; cursor: not-allowed; }

    .loan-btn-secondary { background: #b2bec3; color: #2d3436; }
    .loan-btn-secondary:hover { background: #636e72; color: #ffffff; }

    .text-error { color: #d63031; font-size: 13px; margin-top: -10px; margin-bottom: 8px; }

    /* status NIS */
    .nis-status {
        margin-top: -6px;
        margin-bottom: 10px;
        font-size: 13px;
        display: none;
        align-items: center;
        gap: 8px;
        color: #636e72;
    }
    .nis-pill {
        padding: 2px 10px;
        border-radius: 999px;
        font-size: 12px;
        border: 1px solid #dfe6e9;
        background: #f7f7f7;
    }
    .nis-pill.ok { border-color: rgba(34,197,94,.6); color: #166534; background: rgba(34,197,94,.08); }
    .nis-pill.bad { border-color: rgba(239,68,68,.6); color: #991b1b; background: rgba(239,68,68,.08); }

    /* status buku */
    .book-status {
        margin-top: -6px;
        margin-bottom: 12px;
        font-size: 13px;
        display: none;
        align-items: center;
        gap: 8px;
        color: #636e72;
    }
    .book-pill {
        padding: 2px 10px;
        border-radius: 999px;
        font-size: 12px;
        border: 1px solid #dfe6e9;
        background: #f7f7f7;
        white-space: nowrap;
    }
    .book-pill.ok { border-color: rgba(34,197,94,.6); color: #166534; background: rgba(34,197,94,.08); }
    .book-pill.bad { border-color: rgba(239,68,68,.6); color: #991b1b; background: rgba(239,68,68,.08); }
</style>

<div class="loan-form-wrapper">
    <div class="loan-form-box">
        <h2 class="loan-form-title">Tambah Peminjaman Buku</h2>

        {{-- pesan sukses --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- pesan error validasi --}}
        @if ($errors->any())
            <div class="alert alert-danger mb-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('borrowings.store') }}">
            @csrf

            {{-- DATA SISWA --}}
            <label class="loan-form-label">NIS</label>
            <input
                id="student_nis"
                type="text"
                name="student_nis"
                class="loan-form-input"
                value="{{ old('student_nis') }}"
                required>

            <div id="nisStatus" class="nis-status">
                <span id="nisPill" class="nis-pill">-</span>
                <span id="nisMsg"></span>
            </div>

            @error('student_nis')
                <div class="text-error">{{ $message }}</div>
            @enderror

            <label class="loan-form-label">Nama Siswa</label>
            <input
                id="student_name"
                type="text"
                name="student_name"
                class="loan-form-input"
                value="{{ old('student_name') }}"
                placeholder="Otomatis terisi dari NIS"
                readonly
                required>
            @error('student_name')
                <div class="text-error">{{ $message }}</div>
            @enderror

            <label class="loan-form-label">Kelas</label>
            <input
                id="student_class"
                type="text"
                name="student_class"
                class="loan-form-input"
                placeholder="Otomatis terisi dari NIS"
                value="{{ old('student_class') }}"
                readonly
                required>
            @error('student_class')
                <div class="text-error">{{ $message }}</div>
            @enderror

            {{-- ✅ SINKRON BUKU: INPUT KODE/ID BUKU --}}
            <label class="loan-form-label">ID Buku / Kode Buku</label>
            <input
                id="book_code_input"
                type="text"
                class="loan-form-input"
                placeholder="Ketik / scan kode buku (contoh: BK-002 / 005.13 SAN 2022)"
                value="{{ old('book_code_input') }}">

            <div id="bookStatus" class="book-status">
                <span id="bookPill" class="book-pill">-</span>
                <span id="bookMsg"></span>
            </div>

            <small class="loan-form-small">
                Tips: Ketik/scan <b>kode buku</b> biar dropdown otomatis kepilih.
            </small>

            {{-- PILIH BUKU --}}
            <label class="loan-form-label">Pilih Buku</label>
            <select id="book_select" name="book_id" class="loan-form-select" required>
                <option value="">— Pilih Buku —</option>
                @forelse ($books as $b)
                    <option
                        value="{{ $b->id }}"
                        data-book-code="{{ $b->book_code ?? '' }}"
                        data-title="{{ $b->title ?? '' }}"
                        data-author="{{ $b->author ?? '' }}"
                        {{ old('book_id') == $b->id ? 'selected' : '' }}
                    >
                        {{ $b->title }} — {{ $b->author }}
                    </option>
                @empty
                    <option value="" disabled>Belum ada data buku di perpustakaan</option>
                @endforelse
            </select>
            @error('book_id')
                <div class="text-error">{{ $message }}</div>
            @enderror

            {{-- TANGGAL PINJAM --}}
            <label class="loan-form-label">Tanggal Pinjam</label>
            <input type="date"
                   id="tanggal_pinjam"
                   name="borrow_date"
                   class="loan-form-input"
                   value="{{ old('borrow_date', date('Y-m-d')) }}"
                   onchange="hitungJatuhTempo()"
                   required>
            @error('borrow_date')
                <div class="text-error">{{ $message }}</div>
            @enderror

            {{-- DURASI --}}
            <label class="loan-form-label">Lama Peminjaman (hari)</label>
            <select id="durasi"
                    name="duration"
                    class="loan-form-select"
                    onchange="hitungJatuhTempo()"
                    required>
                <option value="">— Pilih Durasi —</option>
                @for ($i = 1; $i <= 7; $i++)
                    <option value="{{ $i }}" {{ old('duration') == $i ? 'selected' : '' }}>
                        {{ $i }} Hari
                    </option>
                @endfor
            </select>
            <small class="loan-form-small">
                Maksimal peminjaman adalah <b>7 hari</b> dari tanggal pinjam.
            </small>
            @error('duration')
                <div class="text-error">{{ $message }}</div>
            @enderror

            {{-- JATUH TEMPO --}}
            <label class="loan-form-label">Tanggal Jatuh Tempo</label>
            <input type="date"
                   id="tanggal_jatuh_tempo"
                   name="due_date"
                   class="loan-form-input"
                   value="{{ old('due_date') }}"
                   readonly
                   required>
            @error('due_date')
                <div class="text-error">{{ $message }}</div>
            @enderror

            <button id="submitBtn" type="submit" class="loan-btn-primary" disabled>
                Simpan Peminjaman
            </button>

            <button type="button"
                    class="loan-btn-secondary"
                    onclick="window.location.href='{{ route('borrowings.index') }}'">
                Batal / Kembali
            </button>
        </form>
    </div>
</div>

<script>
    // Hitung jatuh tempo (tetap)
    function hitungJatuhTempo() {
        let hari      = document.getElementById("durasi").value;
        let tglPinjam = document.getElementById("tanggal_pinjam").value;

        if (hari && tglPinjam) {
            let date = new Date(tglPinjam);
            date.setDate(date.getDate() + parseInt(hari));

            let yyyy = date.getFullYear();
            let mm   = String(date.getMonth() + 1).padStart(2, "0");
            let dd   = String(date.getDate()).padStart(2, "0");

            document.getElementById("tanggal_jatuh_tempo").value = `${yyyy}-${mm}-${dd}`;
        } else {
            document.getElementById("tanggal_jatuh_tempo").value = "";
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        hitungJatuhTempo();

        // =========================
        // AUTO-FILL SISWA (NIS)
        // =========================
        const nisInput   = document.getElementById('student_nis');
        const nameInput  = document.getElementById('student_name');
        const classInput = document.getElementById('student_class');
        const submitBtn  = document.getElementById('submitBtn');

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
                    const res = await fetch(`/api/member/${encodeURIComponent(nis)}`, {
                        headers: { 'Accept': 'application/json' }
                    });

                    if(res.status === 404){
                        clearStudent();
                        setStatus('bad', 'NIS tidak terdaftar.');
                        return;
                    }
                    if(!res.ok) throw new Error('API error');

                    const json = await res.json();

                    if(json.not_found){
                        clearStudent();
                        setStatus('bad', 'NIS tidak terdaftar.');
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

        // auto trigger kalau ada old nis
        if(nisInput.value.trim().length >= 6){
            nisInput.dispatchEvent(new Event('input'));
        }

        // =========================
        // ✅ SINKRON BUKU (KODE <-> DROPDOWN)
        // =========================
        const bookCodeInput = document.getElementById('book_code_input');
        const bookSelect    = document.getElementById('book_select');

        const bookStatus = document.getElementById('bookStatus');
        const bookPill   = document.getElementById('bookPill');
        const bookMsg    = document.getElementById('bookMsg');

        function normalizeCode(v){
            return (v || '')
                .toString()
                .trim()
                .toLowerCase()
                .replace(/\s+/g,' '); // rapihin spasi
        }

        function setBookStatus(type, msg){
            bookStatus.style.display = 'flex';
            bookPill.classList.remove('ok','bad');
            if(type === 'ok'){
                bookPill.classList.add('ok');
                bookPill.textContent = 'DITEMUKAN';
            }else if(type === 'bad'){
                bookPill.classList.add('bad');
                bookPill.textContent = 'TIDAK ADA';
            }else{
                bookPill.textContent = '-';
            }
            bookMsg.textContent = msg || '';
        }

        function hideBookStatus(){
            bookStatus.style.display = 'none';
            bookPill.classList.remove('ok','bad');
            bookPill.textContent = '-';
            bookMsg.textContent = '';
        }

        function syncFromSelect(){
            const opt = bookSelect.options[bookSelect.selectedIndex];
            if(!opt || !opt.value){
                // kosong
                hideBookStatus();
                return;
            }

            const code   = (opt.dataset.bookCode || '').trim();
            const title  = (opt.dataset.title || '').trim();
            const author = (opt.dataset.author || '').trim();

            // isi input kode (kalau ada di DB)
            if(code) bookCodeInput.value = code;

            const info = title ? `${title}${author ? ' — ' + author : ''}` : 'Buku terpilih.';
            setBookStatus('ok', info);
        }

        function syncFromCodeInput(){
            const typed = normalizeCode(bookCodeInput.value);
            if(!typed){
                hideBookStatus();
                return;
            }

            // cari option yang kode-nya match
            let foundValue = '';
            let foundOpt = null;

            for(const opt of bookSelect.options){
                const optCode = normalizeCode(opt.dataset.bookCode || '');
                if(opt.value && optCode && optCode === typed){
                    foundValue = opt.value;
                    foundOpt = opt;
                    break;
                }
            }

            if(foundValue){
                bookSelect.value = foundValue;

                const title  = (foundOpt.dataset.title || '').trim();
                const author = (foundOpt.dataset.author || '').trim();
                const info = title ? `${title}${author ? ' — ' + author : ''}` : 'Buku ditemukan.';
                setBookStatus('ok', info);
            }else{
                // jangan paksa dropdown jadi kosong (biar admin gak ilang pilihan),
                // tapi kasih info kalo kode gak ketemu.
                setBookStatus('bad', 'Kode buku tidak ditemukan. Cek penulisan kode / pastikan buku ada.');
            }
        }

        // event: ketik kode
        let bookTimer = null;
        bookCodeInput.addEventListener('input', () => {
            clearTimeout(bookTimer);
            const v = bookCodeInput.value.trim();
            if(v.length < 2){
                hideBookStatus();
                return;
            }
            setBookStatus('', 'Mencari buku...');
            bookTimer = setTimeout(syncFromCodeInput, 250);
        });

        // event: pilih dropdown
        bookSelect.addEventListener('change', () => {
            syncFromSelect();
        });

        // init saat load (kalau ada old selected)
        if(bookSelect.value){
            syncFromSelect();
        }else if(bookCodeInput.value.trim()){
            syncFromCodeInput();
        }
    });
</script>
@endsection

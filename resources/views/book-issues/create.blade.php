@extends('layouts.app')

@section('title', 'Tambah Kasus Buku Hilang / Rusak')

@section('content')
<style>
    :root{
        --issue-blue:#2563eb;
        --issue-blue-soft:#eff6ff;
        --issue-border:#e5e7eb;
        --issue-text:#0f172a;
        --issue-muted:#64748b;
        --issue-bg:#f8fafc;
        --issue-danger:#dc2626;
        --issue-warning:#f97316;
        --issue-success:#16a34a;
    }

    body{
        background: linear-gradient(180deg, #f8fbff 0%, #f3f4f6 100%);
    }

    .issue-page-wrap{
        max-width: 1120px;
        margin: 28px auto 40px;
        padding: 0 12px;
    }

    .issue-hero{
        background: linear-gradient(135deg, #2563eb 0%, #3b82f6 55%, #60a5fa 100%);
        border-radius: 24px;
        padding: 24px 28px;
        color: #fff;
        box-shadow: 0 20px 45px rgba(37, 99, 235, .22);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 18px;
        margin-bottom: 22px;
        position: relative;
        overflow: hidden;
    }

    .issue-hero::after{
        content:'';
        position:absolute;
        width:220px;
        height:220px;
        border-radius:999px;
        background: rgba(255,255,255,.10);
        right:-70px;
        top:-60px;
    }

    .issue-hero-left{
        display:flex;
        align-items:center;
        gap:16px;
        position:relative;
        z-index:2;
    }

    .issue-hero-icon{
        width:62px;
        height:62px;
        border-radius:20px;
        background: rgba(255,255,255,.16);
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:1.8rem;
        box-shadow: inset 0 0 0 1px rgba(255,255,255,.14);
        flex:0 0 auto;
    }

    .issue-hero-title{
        font-size:1.6rem;
        font-weight:800;
        margin-bottom:4px;
        line-height:1.15;
    }

    .issue-hero-sub{
        font-size:.95rem;
        opacity:.95;
        max-width:720px;
    }

    .issue-hero-badge{
        position:relative;
        z-index:2;
        background: rgba(15,23,42,.14);
        border: 1px solid rgba(255,255,255,.16);
        border-radius:999px;
        padding:8px 14px;
        font-size:.84rem;
        font-weight:600;
        white-space:nowrap;
    }

    .issue-card{
        background:#fff;
        border:1px solid var(--issue-border);
        border-radius:24px;
        box-shadow: 0 14px 34px rgba(15,23,42,.06);
        overflow:hidden;
    }

    .issue-card-body{
        padding: 24px;
    }

    .issue-section{
        border:1px solid #edf2f7;
        border-radius:20px;
        background:#fff;
        padding:18px;
    }

    .issue-section + .issue-section{
        margin-top:16px;
    }

    .issue-section-title{
        display:flex;
        align-items:center;
        gap:.65rem;
        font-size:1.02rem;
        font-weight:800;
        color:var(--issue-text);
        margin-bottom:14px;
    }

    .issue-section-title .emoji{
        width:34px;
        height:34px;
        border-radius:12px;
        background: var(--issue-blue-soft);
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:1rem;
    }

    .summary-box{
        background:linear-gradient(180deg,#ffffff 0%,#f8fafc 100%);
        border:1px solid #eaf0f6;
        border-radius:18px;
        padding:16px;
    }

    .summary-top{
        display:flex;
        flex-wrap:wrap;
        align-items:flex-start;
        justify-content:space-between;
        gap:14px;
    }

    .summary-name{
        font-size:1.15rem;
        font-weight:800;
        color:#0f172a;
        margin-bottom:4px;
    }

    .summary-meta{
        display:flex;
        flex-wrap:wrap;
        gap:8px;
        color:#64748b;
        font-size:.86rem;
    }

    .summary-pill{
        border-radius:999px;
        padding:5px 10px;
        background:#eff6ff;
        color:#1d4ed8;
        font-weight:700;
        font-size:.78rem;
    }

    .summary-book{
        margin-top:14px;
        padding-top:14px;
        border-top:1px dashed #dbe2ea;
        display:grid;
        grid-template-columns: 1.5fr .8fr .8fr;
        gap:14px;
    }

    .summary-label{
        font-size:.72rem;
        text-transform:uppercase;
        letter-spacing:.08em;
        font-weight:800;
        color:#64748b;
        margin-bottom:5px;
    }

    .summary-value{
        font-size:.98rem;
        font-weight:750;
        color:#0f172a;
        line-height:1.35;
        word-break:break-word;
    }

    .summary-sub{
        margin-top:4px;
        font-size:.82rem;
        color:#64748b;
    }

    .auto-note{
        display:flex;
        flex-wrap:wrap;
        gap:8px;
        margin-top:14px;
    }

    .auto-note span{
        display:inline-flex;
        align-items:center;
        gap:6px;
        border-radius:999px;
        background:#f8fafc;
        border:1px solid #e5e7eb;
        padding:6px 10px;
        color:#475569;
        font-size:.8rem;
        font-weight:650;
    }

    .form-label{
        font-weight:800;
        color:#1e293b;
        margin-bottom:8px;
    }

    .form-control,
    .form-select{
        border-radius:16px;
        border:1px solid #dbe2ea;
        min-height:52px;
        padding:12px 15px;
        box-shadow:none !important;
        transition:all .15s ease;
        font-size:1rem;
    }

    .form-control:focus,
    .form-select:focus{
        border-color:#93c5fd;
        box-shadow:0 0 0 4px rgba(37,99,235,.10) !important;
    }

    textarea.form-control{
        min-height:140px;
        resize:vertical;
    }

    .issue-hint{
        font-size:.84rem;
        color:var(--issue-muted);
        margin-top:5px;
    }

    .issue-inline-note{
        border:1px dashed #fed7aa;
        background:#fff7ed;
        color:#9a3412;
        border-radius:16px;
        padding:12px 14px;
        font-size:.9rem;
        line-height:1.55;
    }

    .conditional-panel{
        display:none;
        border:1px solid #e5e7eb;
        background:#f8fafc;
        border-radius:18px;
        padding:16px;
        margin-top:2px;
    }

    .conditional-panel.show{
        display:block;
    }

    .panel-title-small{
        display:flex;
        align-items:center;
        gap:8px;
        font-weight:800;
        color:#0f172a;
        margin-bottom:12px;
    }

    .issue-actions{
        display:flex;
        flex-wrap:wrap;
        gap:10px;
        justify-content:flex-end;
        margin-top:22px;
        padding-top:16px;
        border-top:1px solid #edf2f7;
    }

    .btn-issue-primary{
        border:none;
        background: linear-gradient(135deg, #2563eb, #3b82f6);
        color:#fff;
        border-radius:999px;
        padding:12px 22px;
        font-weight:800;
        box-shadow:0 14px 28px rgba(37,99,235,.18);
    }

    .btn-issue-primary:hover{
        filter:brightness(1.03);
        color:#fff;
    }

    .btn-issue-secondary{
        border:1px solid #cbd5e1;
        background:#fff;
        color:#334155;
        border-radius:999px;
        padding:12px 22px;
        font-weight:800;
    }

    .alert{
        border-radius:18px;
    }

    @media (max-width: 900px){
        .summary-book{
            grid-template-columns:1fr;
        }
    }

    @media (max-width: 768px){
        .issue-hero{
            flex-direction:column;
            align-items:flex-start;
        }

        .issue-card-body{
            padding:18px;
        }

        .issue-actions{
            justify-content:stretch;
        }

        .issue-actions .btn{
            width:100%;
        }
    }
</style>

<div class="issue-page-wrap">
    <div class="issue-hero">
        <div class="issue-hero-left">
            <div class="issue-hero-icon">📦</div>
            <div>
                <div class="issue-hero-title">Tambah Kasus Buku Hilang / Rusak</div>
                <div class="issue-hero-sub">
                    Data transaksi otomatis dari peminjaman yang dipilih. Admin cukup mengisi jenis masalah, denda, dan catatan kasus.
                </div>
            </div>
        </div>

        <div class="issue-hero-badge">
            Sumber: Transaksi Peminjaman
        </div>
    </div>

    <div class="issue-card">
        <div class="issue-card-body">
            @if ($errors->any())
                <div class="alert alert-danger py-3 px-3 mb-4">
                    <div class="fw-bold mb-2">Data belum bisa disimpan.</div>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('book-issues.store') }}">
                @csrf

                <input type="hidden" name="borrowing_id" value="{{ $borrowing->id }}">
                <input type="hidden" name="book_id" value="{{ $borrowing->book_id }}">
                <input type="hidden" name="student_name" value="{{ $borrowing->student_name }}">
                <input type="hidden" name="student_nis" value="{{ $borrowing->student_nis }}">
                <input type="hidden" name="student_class" value="{{ $borrowing->student_class }}">

                <input type="hidden" name="reported_at" value="{{ old('reported_at', now()->toDateString()) }}">
                <input type="hidden" name="status" value="Dilaporkan">

                <div class="issue-section">
                    <div class="issue-section-title">
                        <span class="emoji">👤</span>
                        <span>Ringkasan Transaksi</span>
                    </div>

                    <div class="summary-box">
                        <div class="summary-top">
                            <div>
                                <div class="summary-label">Nama Siswa</div>
                                <div class="summary-name">{{ $borrowing->student_name }}</div>
                                <div class="summary-meta">
                                    <span>NIS: {{ $borrowing->student_nis }}</span>
                                    <span>•</span>
                                    <span>Kelas: {{ $borrowing->student_class }}</span>
                                </div>
                            </div>

                            <div class="summary-pill">
                                ID Buku: {{ optional($borrowing->book)->book_code ?? $borrowing->book_id }}
                            </div>
                        </div>

                        <div class="summary-book">
                            <div>
                                <div class="summary-label">Judul Buku</div>
                                <div class="summary-value">{{ optional($borrowing->book)->title ?? 'Buku sudah dihapus' }}</div>
                                @if(optional($borrowing->book)->author)
                                    <div class="summary-sub">{{ optional($borrowing->book)->author }}</div>
                                @endif
                            </div>

                            <div>
                                <div class="summary-label">Tanggal Pinjam</div>
                                <div class="summary-value">
                                    {{ $borrowing->borrow_date ? \Carbon\Carbon::parse($borrowing->borrow_date)->translatedFormat('d F Y') : '-' }}
                                </div>
                            </div>

                            <div>
                                <div class="summary-label">Jatuh Tempo</div>
                                <div class="summary-value">
                                    {{ $borrowing->due_date ? \Carbon\Carbon::parse($borrowing->due_date)->translatedFormat('d F Y') : '-' }}
                                </div>
                            </div>
                        </div>

                        <div class="auto-note">
                            <span>📅 Tanggal laporan otomatis: {{ now()->translatedFormat('d F Y') }}</span>
                            <span>📌 Status awal otomatis: Dilaporkan</span>
                        </div>
                    </div>
                </div>

                <div class="issue-section">
                    <div class="issue-section-title">
                        <span class="emoji">🛠️</span>
                        <span>Detail Kasus</span>
                    </div>

                    <div class="issue-inline-note mb-3">
                        Pilih jenis masalah terlebih dahulu. Field tambahan akan muncul sesuai kebutuhan kasus.
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Jenis Masalah</label>
                            <select name="issue_type" id="issue_type" class="form-select" required>
                                <option value="">-- Pilih jenis --</option>
                                <option value="Hilang" {{ old('issue_type') === 'Hilang' ? 'selected' : '' }}>Hilang</option>
                                <option value="Rusak" {{ old('issue_type') === 'Rusak' ? 'selected' : '' }}>Rusak</option>
                            </select>
                            <div class="issue-hint">Pilih apakah buku hilang atau rusak.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" id="fine_label">Denda / Estimasi Biaya</label>
                            <input type="number" step="0.01" min="0" name="fine_amount" class="form-control"
                                   value="{{ old('fine_amount', 0) }}">
                            <div class="issue-hint">Isi 0 jika belum ada atau tidak dikenakan denda.</div>
                        </div>

                        <div class="col-12">
                            <div id="lost_panel" class="conditional-panel">
                                <div class="panel-title-small">
                                    📕 Detail Penggantian Buku Hilang
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-5">
                                        <label class="form-label">Wajib Ganti Buku?</label>
                                        <select name="replacement_required" id="replacement_required" class="form-select">
                                            <option value="0" {{ old('replacement_required') == '0' ? 'selected' : '' }}>Tidak</option>
                                            <option value="1" {{ old('replacement_required', '1') == '1' ? 'selected' : '' }}>Ya</option>
                                        </select>
                                        <div class="issue-hint">Umumnya buku hilang perlu diganti atau dibayar setara.</div>
                                    </div>

                                    <div class="col-md-7">
                                        <label class="form-label">Catatan Penggantian</label>
                                        <input type="text" name="replacement_note" class="form-control"
                                               value="{{ old('replacement_note') }}"
                                               placeholder="Contoh: ganti buku yang sama / bayar setara">
                                    </div>
                                </div>
                            </div>

                            <div id="damaged_panel" class="conditional-panel">
                                <div class="panel-title-small">
                                    🔧 Detail Kondisi Buku Rusak
                                </div>

                                <div class="issue-hint mb-0">
                                    Untuk kasus buku rusak, tulis kondisi kerusakan di bagian catatan/kronologi di bawah. Contoh: sampul rusak, halaman sobek, terkena air, atau coretan berat.
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label" id="note_label">Catatan / Kronologi</label>
                            <textarea name="note" id="note" class="form-control" rows="5" placeholder="Tulis kronologi singkat, kondisi buku, atau catatan tindak lanjut...">{{ old('note') }}</textarea>
                            <div class="issue-hint" id="note_hint">
                                Jelaskan kondisi kasus secara singkat agar mudah ditindaklanjuti.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="issue-actions">
                    <a href="{{ route('borrowings.index', ['active_tab' => 'issue']) }}" class="btn btn-issue-secondary">
                        Kembali
                    </a>
                    <button type="submit" class="btn btn-issue-primary">
                        Simpan Kasus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const issueType = document.getElementById('issue_type');
    const lostPanel = document.getElementById('lost_panel');
    const damagedPanel = document.getElementById('damaged_panel');
    const replacementRequired = document.getElementById('replacement_required');
    const noteLabel = document.getElementById('note_label');
    const note = document.getElementById('note');
    const noteHint = document.getElementById('note_hint');
    const fineLabel = document.getElementById('fine_label');

    function updateIssueForm() {
        const value = issueType.value;

        lostPanel.classList.remove('show');
        damagedPanel.classList.remove('show');

        if (value === 'Hilang') {
            lostPanel.classList.add('show');

            if (replacementRequired && !replacementRequired.dataset.userChanged) {
                replacementRequired.value = '1';
            }

            fineLabel.textContent = 'Denda / Biaya Penggantian';
            noteLabel.textContent = 'Catatan / Kronologi';
            note.placeholder = 'Contoh: buku hilang saat dibawa pulang, siswa diminta mengganti buku atau membayar setara...';
            noteHint.textContent = 'Jelaskan kronologi kehilangan dan tindak lanjut penggantian buku.';
        } else if (value === 'Rusak') {
            damagedPanel.classList.add('show');

            if (replacementRequired) {
                replacementRequired.value = '0';
            }

            fineLabel.textContent = 'Estimasi Denda';
            noteLabel.textContent = 'Kondisi Kerusakan / Catatan Admin';
            note.placeholder = 'Contoh: sampul rusak, halaman sobek, terkena air, atau terdapat coretan berat...';
            noteHint.textContent = 'Jelaskan kondisi kerusakan buku dan tindak lanjut yang diperlukan.';
        } else {
            if (replacementRequired) {
                replacementRequired.value = '0';
            }

            fineLabel.textContent = 'Denda / Estimasi Biaya';
            noteLabel.textContent = 'Catatan / Kronologi';
            note.placeholder = 'Tulis kronologi singkat, kondisi buku, atau catatan tindak lanjut...';
            noteHint.textContent = 'Jelaskan kondisi kasus secara singkat agar mudah ditindaklanjuti.';
        }
    }

    if (replacementRequired) {
        replacementRequired.addEventListener('change', function () {
            replacementRequired.dataset.userChanged = '1';
        });
    }

    issueType.addEventListener('change', updateIssueForm);
    updateIssueForm();
});
</script>
@endsection

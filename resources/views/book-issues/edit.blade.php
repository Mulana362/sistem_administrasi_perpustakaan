@extends('layouts.app')

@section('title', 'Edit Kasus Buku Hilang / Rusak')

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
        max-width: 1040px;
        margin: 28px auto 40px;
        padding: 0 12px;
    }

    .issue-hero{
        background: linear-gradient(135deg, #1e293b 0%, #334155 55%, #475569 100%);
        border-radius: 24px;
        padding: 26px 28px;
        color: #fff;
        box-shadow: 0 20px 45px rgba(15, 23, 42, .18);
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
        background: rgba(255,255,255,.08);
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
        width:64px;
        height:64px;
        border-radius:20px;
        background: rgba(255,255,255,.12);
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:1.9rem;
        box-shadow: inset 0 0 0 1px rgba(255,255,255,.12);
    }

    .issue-hero-title{
        font-size:1.65rem;
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
        background: rgba(255,255,255,.10);
        border: 1px solid rgba(255,255,255,.12);
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
        padding: 26px;
    }

    .issue-section{
        border:1px solid #edf2f7;
        border-radius:20px;
        background:#fff;
        padding:18px 18px 16px;
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

    .issue-info-grid{
        display:grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap:14px;
    }

    .issue-info-card{
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        border:1px solid #eaf0f6;
        border-radius:18px;
        padding:14px 15px;
        min-height:92px;
    }

    .issue-info-card.wide{
        grid-column: span 2;
    }

    .issue-info-label{
        font-size:.76rem;
        text-transform:uppercase;
        letter-spacing:.08em;
        font-weight:700;
        color:var(--issue-muted);
        margin-bottom:8px;
    }

    .issue-info-value{
        font-size:1.04rem;
        font-weight:700;
        color:var(--issue-text);
        line-height:1.35;
        word-break:break-word;
    }

    .issue-info-sub{
        margin-top:4px;
        font-size:.82rem;
        color:var(--issue-muted);
    }

    .form-label{
        font-weight:700;
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
        min-height:120px;
        resize:vertical;
    }

    .issue-hint{
        font-size:.84rem;
        color:var(--issue-muted);
        margin-top:4px;
    }

    .issue-inline-note{
        border:1px dashed #cbd5e1;
        background:#f8fafc;
        color:#334155;
        border-radius:16px;
        padding:12px 14px;
        font-size:.9rem;
        line-height:1.55;
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
        font-weight:700;
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
        font-weight:700;
    }

    .alert{
        border-radius:18px;
    }

    .badge-mini{
        display:inline-flex;
        align-items:center;
        gap:.4rem;
        padding:7px 12px;
        border-radius:999px;
        font-size:.8rem;
        font-weight:700;
        border:1px solid transparent;
    }

    .badge-mini .dot{
        width:8px;
        height:8px;
        border-radius:999px;
        display:inline-block;
    }

    .badge-mini.hilang{
        background:#ffebee;
        color:#b91c1c;
        border-color:#fecaca;
    }

    .badge-mini.hilang .dot{
        background:#dc2626;
    }

    .badge-mini.rusak{
        background:#fff7ed;
        color:#c2410c;
        border-color:#fed7aa;
    }

    .badge-mini.rusak .dot{
        background:#f97316;
    }

    @media (max-width: 1100px){
        .issue-info-grid{
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .issue-info-card.wide{
            grid-column: span 2;
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

        .issue-info-grid{
            grid-template-columns: 1fr;
        }

        .issue-info-card.wide{
            grid-column: span 1;
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
            <div class="issue-hero-icon">📝</div>
            <div>
                <div class="issue-hero-title">Edit Kasus Buku Hilang / Rusak</div>
                <div class="issue-hero-sub">
                    Perbarui status penyelesaian, denda, kebutuhan penggantian, dan catatan tindak lanjut untuk kasus yang sudah tercatat.
                </div>
            </div>
        </div>

        <div class="issue-hero-badge">
            ID Kasus: #{{ $bookIssue->id }}
        </div>
    </div>

    <div class="issue-card">
        <div class="issue-card-body">
            @if ($errors->any())
                <div class="alert alert-danger py-3 px-3 mb-4">
                    <div class="fw-bold mb-2">Data belum bisa diperbarui.</div>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('book-issues.update', $bookIssue->id) }}">
                @csrf
                @method('PUT')

                <div class="issue-section">
                    <div class="issue-section-title">
                        <span class="emoji">📚</span>
                        <span>Informasi Kasus</span>
                    </div>

                    <div class="issue-info-grid">
                        <div class="issue-info-card">
                            <div class="issue-info-label">Nama Siswa</div>
                            <div class="issue-info-value">{{ $bookIssue->student_name }}</div>
                        </div>

                        <div class="issue-info-card">
                            <div class="issue-info-label">NIS</div>
                            <div class="issue-info-value">{{ $bookIssue->student_nis }}</div>
                        </div>

                        <div class="issue-info-card">
                            <div class="issue-info-label">Kelas</div>
                            <div class="issue-info-value">{{ $bookIssue->student_class }}</div>
                        </div>

                        <div class="issue-info-card">
                            <div class="issue-info-label">Tanggal Dilaporkan</div>
                            <div class="issue-info-value">
                                {{ $bookIssue->reported_at ? \Carbon\Carbon::parse($bookIssue->reported_at)->translatedFormat('d F Y') : '-' }}
                            </div>
                        </div>

                        <div class="issue-info-card wide">
                            <div class="issue-info-label">Judul Buku</div>
                            <div class="issue-info-value">{{ optional($bookIssue->book)->title ?? 'Buku sudah dihapus' }}</div>
                            @if(optional($bookIssue->book)->author)
                                <div class="issue-info-sub">{{ optional($bookIssue->book)->author }}</div>
                            @endif
                        </div>

                        <div class="issue-info-card">
                            <div class="issue-info-label">Jenis Masalah</div>
                            <div class="issue-info-value">
                                @if($bookIssue->issue_type === 'Hilang')
                                    <span class="badge-mini hilang"><span class="dot"></span> Hilang</span>
                                @else
                                    <span class="badge-mini rusak"><span class="dot"></span> Rusak</span>
                                @endif
                            </div>
                        </div>

                        <div class="issue-info-card">
                            <div class="issue-info-label">ID Buku</div>
                            <div class="issue-info-value">{{ optional($bookIssue->book)->book_code ?? $bookIssue->book_id }}</div>
                        </div>
                    </div>
                </div>

                <div class="issue-section">
                    <div class="issue-section-title">
                        <span class="emoji">⚙️</span>
                        <span>Update Penyelesaian</span>
                    </div>

                    <div class="issue-inline-note mb-3">
                        Gunakan bagian ini untuk memperbarui progres penanganan kasus. Untuk buku hilang, opsi penggantian buku yang sama akan memengaruhi stok saat status dibuat Selesai.
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Denda / Biaya Penggantian</label>
                            <input type="number" step="0.01" min="0" name="fine_amount" class="form-control"
                                   value="{{ old('fine_amount', $bookIssue->fine_amount) }}">
                            <div class="issue-hint">Isi 0 jika tidak ada denda atau buku diganti tanpa biaya.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="Dilaporkan" {{ old('status', $bookIssue->status) === 'Dilaporkan' ? 'selected' : '' }}>Dilaporkan</option>
                                <option value="Diproses" {{ old('status', $bookIssue->status) === 'Diproses' ? 'selected' : '' }}>Diproses</option>
                                <option value="Selesai" {{ old('status', $bookIssue->status) === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                            </select>
                            <div class="issue-hint">Ubah status sesuai progres penyelesaian kasus.</div>
                        </div>

                        @if($bookIssue->issue_type === 'Hilang')
                            <div class="col-md-6">
                                <label class="form-label">Buku Diganti dengan Buku yang Sama?</label>
                                <select name="replacement_required" class="form-select">
                                    <option value="0" {{ old('replacement_required', $bookIssue->replacement_required) == '0' ? 'selected' : '' }}>Tidak</option>
                                    <option value="1" {{ old('replacement_required', $bookIssue->replacement_required) == '1' ? 'selected' : '' }}>Ya, buku yang sama</option>
                                </select>
                                <div class="issue-hint">Pilih Ya hanya jika siswa mengganti dengan judul/eksemplar yang sama. Stok akan bertambah saat kasus selesai.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Catatan Penggantian</label>
                                <input type="text" name="replacement_note" class="form-control"
                                       value="{{ old('replacement_note', $bookIssue->replacement_note) }}"
                                       placeholder="Contoh: diganti buku yang sama / bayar denda / diganti buku lain setara">
                            </div>
                        @else
                            <input type="hidden" name="replacement_required" value="0">

                            <div class="col-12">
                                <div class="issue-inline-note">
                                    Untuk kasus buku rusak, stok bertambah saat status kasus dibuat Selesai karena buku dianggap sudah diperbaiki dan bisa digunakan kembali.
                                </div>
                            </div>
                        @endif

                        <div class="col-12">
                            <label class="form-label">Catatan Admin</label>
                            <textarea name="note" class="form-control" rows="4" placeholder="Tulis catatan tindak lanjut atau hasil penyelesaian...">{{ old('note', $bookIssue->note) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="issue-actions">
                    <a href="{{ route('borrowings.index', ['active_tab' => 'issue']) }}" class="btn btn-issue-secondary">
                        Kembali
                    </a>
                    <button type="submit" class="btn btn-issue-primary">
                        Update Kasus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

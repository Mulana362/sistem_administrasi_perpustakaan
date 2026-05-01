{{-- resources/views/visitors/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Rekap Kunjungan Perpustakaan')

@section('content')
@php
    use Carbon\Carbon;
    use App\Models\Visitor;

    // fallback kalau controller belum ngirim variabel statistik
    $todayCount      = isset($today)        ? $today        : Visitor::whereDate('visit_date', today())->count();
    $thisMonthCount  = isset($thisMonth)    ? $thisMonth    : Visitor::whereMonth('visit_date', today()->month)
                                                            ->whereYear('visit_date', today()->year)
                                                            ->count();
    $totalVisitors   = isset($totalVisitors)? $totalVisitors: Visitor::count();
@endphp

<style>
    body {
        background: radial-gradient(circle at top left, #dbeafe 0, #eff6ff 35%, transparent 60%),
                    radial-gradient(circle at bottom right, #e5e7eb 0, #f9fafb 45%, #e5e7eb 100%);
    }

    .rekap-wrapper {
        max-width: 1180px;
        margin: 24px auto 40px;
    }

    /* HEADER BESAR */
    .rekap-hero {
        background: linear-gradient(135deg, #1d4ed8, #2563eb);
        border-radius: 20px;
        padding: 20px 24px;
        color: #ffffff;
        box-shadow: 0 18px 45px rgba(37,99,235,0.45);
        display: flex;
        justify-content: space-between;
        gap: 16px;
        align-items: center;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
    }

    .rekap-hero::after {
        content: "";
        position: absolute;
        width: 260px;
        height: 260px;
        border-radius: 999px;
        background: rgba(255,255,255,0.18);
        right: -90px;
        top: -90px;
    }

    .rekap-hero-left {
        display: flex;
        align-items: center;
        gap: 14px;
        position: relative;
        z-index: 2;
    }

    .rekap-hero-icon {
        width: 56px;
        height: 56px;
        border-radius: 18px;
        background: rgba(15,23,42,0.18);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.9rem;
        box-shadow: inset 0 0 0 1px rgba(255,255,255,.12);
    }

    .rekap-hero-title {
        font-size: 1.7rem;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .rekap-hero-sub {
        font-size: .9rem;
        opacity: .92;
    }

    .rekap-hero-right {
        text-align: right;
        position: relative;
        z-index: 2;
        font-size: .85rem;
    }

    .rekap-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        background: rgba(15,23,42,0.16);
        padding: 6px 12px;
        border-radius: 999px;
        box-shadow: inset 0 0 0 1px rgba(255,255,255,.12);
    }

    /* KARTU STATISTIK */
    .rekap-stat-row {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 20px;
    }

    .rekap-card {
        border-radius: 20px;
        padding: 18px 18px 16px;
        color: #ffffff;
        box-shadow: 0 12px 30px rgba(15,23,42,0.15);
        position: relative;
        overflow: hidden;
        min-height: 150px;
    }

    .rekap-card::after {
        content: "";
        position: absolute;
        width: 120px;
        height: 120px;
        border-radius: 999px;
        right: -28px;
        top: -28px;
        background: rgba(255,255,255,.14);
    }

    .rekap-card-1 { background: linear-gradient(135deg, #22c55e, #16a34a); }
    .rekap-card-2 { background: linear-gradient(135deg, #8b5cf6, #6366f1); }
    .rekap-card-3 { background: linear-gradient(135deg, #06b6d4, #0ea5e9); }

    .rekap-card-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 10px;
        position: relative;
        z-index: 2;
    }

    .rekap-card-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        border-radius: 14px;
        background: rgba(15,23,42,.14);
        font-size: 1.2rem;
        box-shadow: inset 0 0 0 1px rgba(255,255,255,.14);
    }

    .rekap-card-label {
        font-size: .76rem;
        text-transform: uppercase;
        letter-spacing: .14em;
        font-weight: 700;
        opacity: .92;
        margin-bottom: 8px;
    }

    .rekap-card-number {
        font-size: 2.35rem;
        line-height: 1;
        font-weight: 800;
        margin-bottom: 8px;
        position: relative;
        z-index: 2;
    }

    .rekap-card-desc {
        font-size: .84rem;
        opacity: .95;
        position: relative;
        z-index: 2;
    }

    .rekap-card-mini {
        margin-top: 12px;
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        background: rgba(15,23,42,.14);
        padding: 5px 10px;
        border-radius: 999px;
        font-size: .75rem;
        position: relative;
        z-index: 2;
        box-shadow: inset 0 0 0 1px rgba(255,255,255,.14);
    }

    /* BOX UMUM */
    .rekap-box {
        background: #ffffff;
        border-radius: 18px;
        padding: 16px 18px 18px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 10px 26px rgba(15,23,42,0.10);
        margin-bottom: 18px;
    }

    .rekap-section-title {
        font-weight: 700;
        font-size: 1.05rem;
        display: flex;
        gap: .5rem;
        align-items: center;
        margin-bottom: 4px;
    }

    .rekap-section-title span.emoji {
        font-size: 1.4rem;
    }

    .rekap-section-sub {
        font-size: .82rem;
        color: #6b7280;
        margin-bottom: 10px;
    }

    .small-muted {
        font-size: .8rem;
        color: #6b7280;
    }

    /* TOMBOL ATAS TABEL */
    .rekap-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: flex-end;
        align-items: center;
        margin-bottom: 10px;
    }

    .btn-pill {
        border-radius: 999px !important;
        font-size: .85rem;
        padding: 6px 14px;
        display: inline-flex;
        align-items: center;
        gap: .35rem;
    }

    .btn-print-modern {
        background: linear-gradient(135deg, #111827, #374151);
        color: #ffffff !important;
        border: none !important;
        box-shadow: 0 10px 24px rgba(17,24,39,.18);
    }

    .btn-print-modern:hover {
        filter: brightness(1.06);
    }

    /* TABEL */
    .rekap-table thead th {
        background: #eff6ff;
        font-size: .84rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #4b5563;
        border-bottom: 1px solid #e5e7eb;
    }

    .rekap-table tbody tr:nth-child(even) {
        background: #f9fafb;
    }

    .rekap-table tbody tr:hover {
        background: #eef2ff;
    }

    /* FILTER & GRAFIK */
    .rekap-filter-row {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .rekap-filter-inputs {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
    }

    .rekap-filter-inputs input[type="date"] {
        padding: 6px 10px;
        border-radius: 999px;
        border: 1px solid #d1d5db;
        font-size: .85rem;
    }

    .rekap-filter-inputs button {
        border-radius: 999px;
        font-size: .82rem;
        padding: 6px 14px;
    }

    .chart-box {
        background: #ffffff;
        border-radius: 18px;
        padding: 16px 18px 18px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 10px 24px rgba(15,23,42,0.10);
    }

    .chart-inner {
        margin-top: 10px;
        background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
        border: 1px solid #eef2f7;
        border-radius: 16px;
        padding: 12px 14px 6px;
    }

    .print-only {
        display: none;
    }

    @media (max-width: 900px) {
        .rekap-stat-row {
            grid-template-columns: 1fr;
        }

        .rekap-hero {
            flex-direction: column;
            align-items: flex-start;
        }

        .rekap-hero-right {
            text-align: left;
        }
    }

    @media print {
        body {
            background: #ffffff !important;
        }

        .rekap-wrapper {
            max-width: 100%;
            margin: 0;
        }

        .rekap-hero,
        .rekap-stat-row,
        .chart-box,
        .rekap-toolbar,
        .no-print {
            display: none !important;
        }

        .rekap-box {
            box-shadow: none !important;
            border: none !important;
            padding: 0 !important;
            margin: 0 !important;
            border-radius: 0 !important;
        }

        .print-only {
            display: block !important;
            margin-bottom: 18px;
        }

        .print-title {
            text-align: center;
            font-weight: 800;
            font-size: 20px;
            text-transform: uppercase;
            margin-bottom: 4px;
            color: #111827;
        }

        .print-subtitle {
            text-align: center;
            font-size: 13px;
            color: #4b5563;
            margin-bottom: 6px;
        }

        .print-date {
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 14px;
        }

        .print-stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 14px;
        }

        .print-stat-card {
            border: 1px solid #d1d5db;
            border-radius: 10px;
            padding: 10px;
            text-align: center;
        }

        .print-stat-label {
            font-size: 11px;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 4px;
            font-weight: 700;
            letter-spacing: .06em;
        }

        .print-stat-number {
            font-size: 22px;
            font-weight: 800;
            color: #111827;
        }

        .rekap-table thead th {
            background: #f3f4f6 !important;
            color: #111827 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .rekap-table tbody tr:nth-child(even),
        .rekap-table tbody tr:hover {
            background: transparent !important;
        }

        .rekap-table .btn,
        .rekap-table form {
            display: none !important;
        }

        .rekap-table th:last-child,
        .rekap-table td:last-child {
            display: none !important;
        }
    }
</style>

<div class="rekap-wrapper">

    {{-- HEADER --}}
    <div class="rekap-hero">
        <div class="rekap-hero-left">
            <div class="rekap-hero-icon">📊</div>
            <div>
                <div class="rekap-hero-title">Rekap Kunjungan Perpustakaan</div>
                <div class="rekap-hero-sub">
                    Pantau statistik pengunjung dan riwayat tamu perpustakaan SMPN 1 Bandung.
                    Data diperbarui otomatis dari buku tamu kunjungan.
                </div>
            </div>
        </div>
        <div class="rekap-hero-right">
            <div class="rekap-hero-badge">
                <span>📅</span>
                <span>Tanggal: <strong>{{ now()->translatedFormat('d F Y') }}</strong></span>
            </div>
            <small class="mt-1 d-block">Gunakan filter tanggal di bawah untuk melihat rentang tertentu.</small>
        </div>
    </div>

    {{-- KARTU STATISTIK --}}
    <div class="rekap-stat-row">
        <div class="rekap-card rekap-card-1">
            <div class="rekap-card-top">
                <div>
                    <div class="rekap-card-label">Hari Ini</div>
                    <div class="rekap-card-number">{{ $todayCount }}</div>
                </div>
                <div class="rekap-card-badge">🟢</div>
            </div>
            <div class="rekap-card-desc">
                Jumlah pengunjung pada tanggal {{ now()->translatedFormat('d F Y') }}.
            </div>
            <div class="rekap-card-mini">Realtime</div>
        </div>

        <div class="rekap-card rekap-card-2">
            <div class="rekap-card-top">
                <div>
                    <div class="rekap-card-label">Bulan Ini</div>
                    <div class="rekap-card-number">{{ $thisMonthCount }}</div>
                </div>
                <div class="rekap-card-badge">📅</div>
            </div>
            <div class="rekap-card-desc">
                Total kunjungan selama {{ now()->translatedFormat('F Y') }}.
            </div>
            <div class="rekap-card-mini">Statistik bulanan</div>
        </div>

        <div class="rekap-card rekap-card-3">
            <div class="rekap-card-top">
                <div>
                    <div class="rekap-card-label">Seluruh Data</div>
                    <div class="rekap-card-number">{{ $totalVisitors }}</div>
                </div>
                <div class="rekap-card-badge">📚</div>
            </div>
            <div class="rekap-card-desc">
                Akumulasi semua kunjungan perpustakaan yang pernah tercatat di sistem.
            </div>
            <div class="rekap-card-mini">Total histori kunjungan</div>
        </div>
    </div>

    {{-- DATA KUNJUNGAN (TABEL) --}}
    <div class="rekap-box">

        {{-- KHUSUS CETAK --}}
        <div class="print-only">
            <div class="print-title">Laporan Rekap Kunjungan Perpustakaan</div>
            <div class="print-subtitle">Perpustakaan SMPN 1 Bandung</div>
            <div class="print-date">Dicetak pada {{ now()->translatedFormat('d F Y, H:i') }} WIB</div>

            <div class="print-stats">
                <div class="print-stat-card">
                    <div class="print-stat-label">Hari Ini</div>
                    <div class="print-stat-number">{{ $todayCount }}</div>
                </div>
                <div class="print-stat-card">
                    <div class="print-stat-label">Bulan Ini</div>
                    <div class="print-stat-number">{{ $thisMonthCount }}</div>
                </div>
                <div class="print-stat-card">
                    <div class="print-stat-label">Total Data</div>
                    <div class="print-stat-number">{{ $totalVisitors }}</div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <div class="rekap-section-title">
                    <span class="emoji">📑</span>
                    <span>Data Kunjungan Perpustakaan</span>
                </div>
                <div class="rekap-section-sub">
                    Daftar tamu perpustakaan yang terurut dari kunjungan terbaru.
                </div>
            </div>

            <div class="rekap-toolbar no-print">
                <button type="button" onclick="window.print()" class="btn btn-pill btn-print-modern">
                    🖨 Cetak Laporan
                </button>
                <button type="button" onclick="window.history.back()" class="btn btn-outline-secondary btn-pill">
                    ← Kembali
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-sm rekap-table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:40px;">No</th>
                        <th style="width:150px;">Tanggal</th>
                        <th>Nama</th>
                        <th style="width:90px;">NIS</th>
                        <th style="width:90px;">Kelas</th>
                        <th style="width:180px;">Keperluan</th>
                        <th style="width:120px;">Waktu Datang</th>
                        <th style="width:110px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($visitors as $visitor)
                        @php
                            $rawTime = $visitor->visit_time
                                ?? $visitor->time_in
                                ?? $visitor->time
                                ?? $visitor->created_at;
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ Carbon::parse($visitor->visit_date)->translatedFormat('d F Y') }}</td>
                            <td>{{ $visitor->name }}</td>
                            <td>{{ $visitor->nis ?: 'Belum diisi' }}</td>
                            <td>{{ $visitor->class }}</td>
                            <td>{{ $visitor->purpose ?: 'Belum diisi' }}</td>
                            <td>{{ Carbon::parse($rawTime)->timezone('Asia/Jakarta')->format('H:i') }} WIB</td>

                            <td>
                                <form action="{{ route('visitors.destroy', $visitor->id) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Yakin ingin menghapus data kunjungan ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger btn-pill">
                                        🗑 Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center small-muted py-3">
                                Belum ada data kunjungan yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- FILTER + GRAFIK --}}
    <div class="chart-box no-print">
        <div class="rekap-filter-row">
            <div>
                <div class="rekap-section-title mb-1">
                    <span class="emoji">📅</span>
                    <span>Filter Tanggal & Tren Kunjungan</span>
                </div>
                <div class="rekap-section-sub mb-0">
                    Pilih rentang tanggal untuk melihat rekap di tabel. Grafik di bawah
                    menampilkan tren kunjungan dengan tampilan yang lebih modern dan rapi.
                </div>
            </div>

            <form method="GET" action="{{ route('visitors.index') }}" class="rekap-filter-inputs">
                <div class="small-muted">Rentang:</div>
                <input type="date" name="from" value="{{ request('from') }}">
                <span class="small-muted">s.d</span>
                <input type="date" name="to" value="{{ request('to') }}">

                <button type="submit" class="btn btn-primary btn-sm">
                    Terapkan
                </button>

                <a href="{{ route('visitors.index') }}" class="btn btn-outline-secondary btn-sm">
                    Reset
                </a>
            </form>
        </div>

        <div class="chart-inner">
            <div style="height: 240px;">
                <canvas id="visitChart"></canvas>
            </div>
        </div>
    </div>

</div>

{{-- CHART.JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    (function () {
        const canvas = document.getElementById('visitChart');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');

        const labels = {!! json_encode($chartLabels ?? []) !!};
        const data   = {!! json_encode($chartData   ?? []) !!};

        if (!labels.length) return;

        const maxValue = Math.max(...data);
        const suggestedMax = maxValue <= 5 ? 6 : maxValue <= 10 ? 12 : maxValue + 2;

        const gradient = ctx.createLinearGradient(0, 0, 0, 240);
        gradient.addColorStop(0, 'rgba(37, 99, 235, 0.95)');
        gradient.addColorStop(0.55, 'rgba(59, 130, 246, 0.78)');
        gradient.addColorStop(1, 'rgba(147, 197, 253, 0.55)');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Kunjungan',
                    data: data,
                    backgroundColor: gradient,
                    borderColor: 'rgba(37, 99, 235, 1)',
                    borderWidth: 1.2,
                    borderRadius: 14,
                    borderSkipped: false,
                    maxBarThickness: 56,
                    barPercentage: 0.58,
                    categoryPercentage: 0.64,
                    hoverBackgroundColor: 'rgba(37, 99, 235, 0.88)',
                    hoverBorderColor: 'rgba(29, 78, 216, 1)',
                    hoverBorderWidth: 1.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                layout: {
                    padding: {
                        top: 4,
                        left: 4,
                        right: 6,
                        bottom: 0
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.96)',
                        titleColor: '#ffffff',
                        bodyColor: '#e5e7eb',
                        displayColors: false,
                        padding: 12,
                        cornerRadius: 12,
                        callbacks: {
                            label: function (context) {
                                return context.parsed.y + ' kunjungan';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        border: {
                            display: false
                        },
                        ticks: {
                            color: '#64748b',
                            font: {
                                size: 12,
                                weight: '600'
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        suggestedMax: suggestedMax,
                        border: {
                            display: false
                        },
                        grid: {
                            color: 'rgba(148, 163, 184, 0.16)',
                            drawBorder: false
                        },
                        ticks: {
                            stepSize: 2,
                            precision: 0,
                            color: '#64748b',
                            font: {
                                size: 11
                            }
                        }
                    }
                },
                animation: {
                    duration: 700,
                    easing: 'easeOutQuart'
                }
            }
        });
    })();
</script>
@endsection

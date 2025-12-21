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
    }

    /* KARTU STATISTIK */
    .rekap-stat-row {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 20px;
    }

    .rekap-card {
        border-radius: 18px;
        padding: 16px 18px;
        color: #ffffff;
        box-shadow: 0 12px 30px rgba(15,23,42,0.15);
    }

    .rekap-card-1 { background: linear-gradient(135deg, #22c55e, #16a34a); }
    .rekap-card-2 { background: linear-gradient(135deg, #a855f7, #6366f1); }
    .rekap-card-3 { background: linear-gradient(135deg, #06b6d4, #0ea5e9); }

    .rekap-card-label {
        font-size: .78rem;
        text-transform: uppercase;
        letter-spacing: .12em;
        font-weight: 700;
        opacity: .9;
        margin-bottom: 6px;
    }

    .rekap-card-number {
        font-size: 2.1rem;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .rekap-card-desc {
        font-size: .85rem;
        opacity: .9;
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
        padding: 14px 18px 18px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 10px 24px rgba(15,23,42,0.10);
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
            <div class="rekap-card-label">HARI INI</div>
            <div class="rekap-card-number">{{ $todayCount }}</div>
            <div class="rekap-card-desc">
                Jumlah pengunjung pada tanggal {{ now()->translatedFormat('d F Y') }}.
            </div>
        </div>
        <div class="rekap-card rekap-card-2">
            <div class="rekap-card-label">BULAN INI</div>
            <div class="rekap-card-number">{{ $thisMonthCount }}</div>
            <div class="rekap-card-desc">
                Total kunjungan selama {{ now()->translatedFormat('F Y') }}.
            </div>
        </div>
        <div class="rekap-card rekap-card-3">
            <div class="rekap-card-label">SELURUH DATA</div>
            <div class="rekap-card-number">{{ $totalVisitors }}</div>
            <div class="rekap-card-desc">
                Akumulasi semua kunjungan perpustakaan yang pernah tercatat di sistem.
            </div>
        </div>
    </div>

    {{-- DATA KUNJUNGAN (TABEL) --}}
    <div class="rekap-box">
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

            <div class="rekap-toolbar">
                <button type="button" onclick="window.history.back()" class="btn btn-outline-secondary btn-pill">
                    ← Kembali
                </button>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-pill">
                    🏠 Dashboard
                </a>
                <a href="#" class="btn btn-warning btn-pill text-dark">
                    🖨 Cetak PDF
                </a>
                <a href="{{ route('visitors.export', ['from' => request('from'), 'to' => request('to')]) }}"
                   class="btn btn-success btn-pill text-white">
                    ⬇ Export Excel
                </a>
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
                            // pilih field waktu yang ada: visit_time > time_in > time > created_at
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
    <div class="chart-box">
        <div class="rekap-filter-row">
            <div>
                <div class="rekap-section-title mb-1">
                    <span class="emoji">📅</span>
                    <span>Filter Tanggal & Grafik Kunjungan</span>
                </div>
                <div class="rekap-section-sub mb-0">
                    Pilih rentang tanggal untuk melihat rekap di tabel. Grafik di bawah
                    menunjukkan tren kunjungan per bulan.
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

        <div style="height: 260px;">
            <canvas id="visitChart"></canvas>
        </div>

        {{-- tombol kembali tambahan di bawah grafik --}}
        <div class="mt-3 d-flex justify-content-end">
            <button type="button" onclick="window.history.back()" class="btn btn-outline-secondary btn-pill">
                ← Kembali
            </button>
        </div>
    </div>

</div>

{{-- CHART.JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    (function () {
        const canvas = document.getElementById('visitChart');
        const ctx = canvas.getContext('2d');

        const labels = {!! json_encode($chartLabels ?? []) !!};
        const data   = {!! json_encode($chartData   ?? []) !!};

        if (!labels.length) return;

        // gradient modern
        const gradient = ctx.createLinearGradient(0, 0, 0, canvas.height);
        gradient.addColorStop(0, 'rgba(37, 99, 235, 0.95)');
        gradient.addColorStop(0.5, 'rgba(59, 130, 246, 0.75)');
        gradient.addColorStop(1, 'rgba(129, 140, 248, 0.25)');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Kunjungan',
                    data: data,
                    backgroundColor: gradient,
                    borderColor: 'rgba(37, 99, 235, 1)',
                    borderWidth: 1,
                    borderRadius: 10,
                    hoverBorderWidth: 2,
                    maxBarThickness: 40,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        top: 10,
                        left: 5,
                        right: 5,
                        bottom: 5
                    }
                },
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.95)',
                        padding: 10,
                        cornerRadius: 10,
                        titleFont: { size: 12, weight: '600' },
                        bodyFont: { size: 11 },
                        callbacks: {
                            label: function (context) {
                                return ' ' + context.parsed.y + ' kunjungan';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                        },
                        ticks: {
                            font: { size: 11 }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(209, 213, 219, 0.5)',
                            drawBorder: false,
                        },
                        ticks: {
                            precision: 0,
                            font: { size: 11 }
                        }
                    }
                },
                animation: {
                    duration: 700,
                    easing: 'easeOutCubic'
                }
            }
        });
    })();
</script>
@endsection

<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VisitorController extends Controller
{
    /**
     * HALAMAN REKAP KUNJUNGAN (ADMIN)
     * route: GET /visitors  (name: visitors.index)
     */
    public function index(Request $request)
    {
        $from = $request->input('from');
        $to   = $request->input('to');

        // Query dasar
        $query = Visitor::query();

        // Filter rentang tanggal kalau ada
        if ($from) {
            $query->whereDate('visit_date', '>=', $from);
        }

        if ($to) {
            $query->whereDate('visit_date', '<=', $to);
        }

        // Urutkan dari kunjungan terbaru
        $visitors = $query
            ->orderByDesc('visit_date')
            ->orderByDesc('created_at')
            ->get();

        // Statistik (fallback untuk Blade kamu)
        $today = Visitor::whereDate('visit_date', Carbon::today())->count();

        $thisMonth = Visitor::whereYear('visit_date', Carbon::today()->year)
            ->whereMonth('visit_date', Carbon::today()->month)
            ->count();

        $totalVisitors = Visitor::count();

        // Data grafik: jumlah kunjungan per bulan
        $chartRows = Visitor::selectRaw('DATE_FORMAT(visit_date, "%Y-%m") as ym, COUNT(*) as total')
            ->groupBy('ym')
            ->orderBy('ym')
            ->get();

        $chartLabels = $chartRows->map(function ($row) {
            // ubah "2025-11" jadi "Nov 2025" (pakai locale Indonesia kalau sudah di-set)
            return Carbon::createFromFormat('Y-m', $row->ym)->translatedFormat('M Y');
        })->values();

        $chartData = $chartRows->pluck('total')->values();

        return view('visitors.index', [
            'visitors'       => $visitors,
            'today'          => $today,
            'thisMonth'      => $thisMonth,
            'totalVisitors'  => $totalVisitors,
            'chartLabels'    => $chartLabels,
            'chartData'      => $chartData,
        ]);
    }

    /**
     * FORM TAMBAH (RESOURCE) – kalau suatu saat mau dipakai dari admin.
     * Saat ini kamu sudah pakai /kunjungan (publik), jadi ini opsional.
     */
    public function create()
    {
        // Bisa diarahkan ke form yang sama dengan /kunjungan
        return view('student.visit.register');
    }

    /**
     * SIMPAN DATA KUNJUNGAN
     * - dipakai route POST /kunjungan   (name: visit.store)
     * - juga dipakai POST /visitors     (resource) kalau kamu mau pakai dari admin
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'nis'     => 'required|string|max:50',
            'class'   => 'required|string|max:50',
            'purpose' => 'required|string|max:255',
            'note'    => 'nullable|string|max:255',
        ]);

        Visitor::create([
            'name'       => $validated['name'],
            'nis'        => $validated['nis'],
            'class'      => $validated['class'],
            'purpose'    => $validated['purpose'],
            'note'       => $validated['note'] ?? null,
            'visit_date' => now()->toDateString(),
        ]);

        // Kalau datang dari form publik /kunjungan
        if ($request->routeIs('visit.store') || $request->is('kunjungan')) {
            return back()->with('success', 'Terima kasih, kunjungan Anda sudah dicatat.');
        }

        // Kalau dipanggil dari /visitors (resource)
        return redirect()
            ->route('visitors.index')
            ->with('success', 'Data kunjungan berhasil ditambahkan.');
    }

    /**
     * TAMPILKAN DETAIL 1 KUNJUNGAN (opsional)
     */
    public function show(Visitor $visitor)
    {
        // Kalau mau nanti bikin view khusus detail.
        return view('visitors.show', compact('visitor'));
    }

    /**
     * FORM EDIT (opsional)
     */
    public function edit(Visitor $visitor)
    {
        return view('visitors.edit', compact('visitor'));
    }

    /**
     * UPDATE DATA KUNJUNGAN (opsional)
     */
    public function update(Request $request, Visitor $visitor)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'nis'     => 'required|string|max:50',
            'class'   => 'required|string|max:50',
            'purpose' => 'required|string|max:255',
            'note'    => 'nullable|string|max:255',
            'visit_date' => 'required|date',
        ]);

        $visitor->update($validated);

        return redirect()
            ->route('visitors.index')
            ->with('success', 'Data kunjungan berhasil diperbarui.');
    }

    /**
     * HAPUS DATA KUNJUNGAN
     * dipakai dari tombol 🗑 di tabel rekap
     */
    public function destroy(Visitor $visitor)
    {
        $visitor->delete();

        return back()->with('success', 'Data kunjungan berhasil dihapus.');
    }

    /**
     * EXPORT EXCEL (sederhana pakai CSV)
     * route: visitors.export
     */
    public function exportExcel(Request $request): StreamedResponse
    {
        $from = $request->input('from');
        $to   = $request->input('to');

        $query = Visitor::query();

        if ($from) {
            $query->whereDate('visit_date', '>=', $from);
        }

        if ($to) {
            $query->whereDate('visit_date', '<=', $to);
        }

        $visitors = $query
            ->orderBy('visit_date')
            ->orderBy('name')
            ->get();

        $fileName = 'rekap-kunjungan-' . now()->format('Ymd-His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        $callback = function () use ($visitors) {
            $handle = fopen('php://output', 'w');

            // Header kolom
            fputcsv($handle, [
                'Tanggal',
                'Nama',
                'NIS',
                'Kelas',
                'Keperluan',
                'Catatan',
                'Waktu Input',
            ]);

            foreach ($visitors as $v) {
                fputcsv($handle, [
                    optional($v->visit_date instanceof Carbon ? $v->visit_date : Carbon::parse($v->visit_date))
                        ->format('Y-m-d'),
                    $v->name,
                    $v->nis,
                    $v->class,
                    $v->purpose,
                    $v->note,
                    optional($v->created_at)->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * HALAMAN KHUSUS CETAK (opsional)
     * route: visitors.print
     * (sekarang tombol PDF-mu masih "#", jadi ini belum kepakai)
     */
    public function print(Request $request)
    {
        $from = $request->input('from');
        $to   = $request->input('to');

        $query = Visitor::query();

        if ($from) {
            $query->whereDate('visit_date', '>=', $from);
        }

        if ($to) {
            $query->whereDate('visit_date', '<=', $to);
        }

        $visitors = $query
            ->orderBy('visit_date')
            ->orderBy('name')
            ->get();

        // kamu bisa bikin view resources/views/visitors/print.blade.php
        // dengan layout yang bersih untuk Ctrl+P → Save as PDF
        return view('visitors.print', compact('visitors', 'from', 'to'));
    }
}

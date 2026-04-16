{{-- resources/views/student/borrow/status.blade.php --}}
@extends('layouts.app')

@section('title', 'Cek Status Peminjaman')

@section('content')
@php
    use Carbon\Carbon;

    $maxActiveBooks = 3;
    $activeStatuses = ['Diajukan', 'Dipinjam', 'Terlambat'];
    $activeBorrowCount = isset($borrowings)
        ? $borrowings->filter(function ($item) use ($activeStatuses) {
            return in_array($item->status ?? 'Diajukan', $activeStatuses, true);
        })->count()
        : 0;

    $finePerDay = 2000;
    $lateModals = [];

    $studentName = null;
    if (isset($borrowings) && $borrowings->count()) {
        $firstBorrow = $borrowings->first();
        $studentName = $firstBorrow->student_name
            ?? optional($firstBorrow->member)->name
            ?? null;
    }
@endphp

<style>
    body {
        background: radial-gradient(circle at top left, #dbeafe 0, #eff6ff 42%, #f8fafc 100%);
    }

    .status-wrapper {
        max-width: 1160px;
        margin: 24px auto 36px;
        padding: 0 16px;
        font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    }

    .status-hero {
        border-radius: 24px;
        padding: 20px 24px;
        background: linear-gradient(115deg, #1d4ed8, #4f46e5 55%, #06b6d4);
        color: #fff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        box-shadow: 0 22px 46px rgba(15,23,42,0.22);
        margin-bottom: 22px;
        position: relative;
        overflow: hidden;
    }

    .status-hero::after {
        content: "";
        position: absolute;
        right: -60px;
        top: -60px;
        width: 220px;
        height: 220px;
        background: rgba(255,255,255,.08);
        border-radius: 999px;
        pointer-events: none;
    }

    .status-hero-left {
        display: flex;
        align-items: center;
        gap: 18px;
        position: relative;
        z-index: 1;
    }

    .status-hero-icon {
        width: 68px;
        height: 68px;
        border-radius: 22px;
        background: radial-gradient(circle at 30% 0, #e0f2fe 0, #38bdf8 42%, #0f172a 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.1rem;
        box-shadow: 0 18px 34px rgba(15,23,42,.35);
    }

    .status-hero-title {
        font-size: 1.8rem;
        font-weight: 800;
        letter-spacing: .01em;
        margin-bottom: 4px;
        line-height: 1.15;
    }

    .status-hero-sub {
        font-size: .95rem;
        opacity: .96;
        max-width: 640px;
    }

    .btn-hero-back {
        border-radius: 999px;
        padding: .62rem 1.4rem;
        border: 1px solid rgba(255,255,255,.10);
        background: rgba(15,23,42,.14);
        color: #f8fafc;
        font-size: .88rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        text-decoration: none;
        backdrop-filter: blur(8px);
        position: relative;
        z-index: 1;
    }

    .btn-hero-back:hover {
        background: rgba(15,23,42,.24);
        color: #fff;
    }

    .status-card {
        border-radius: 22px;
        background: rgba(255,255,255,.94);
        border: 1px solid rgba(226,232,240,.95);
        box-shadow: 0 18px 42px rgba(15,23,42,.07);
        padding: 20px 20px 16px;
        backdrop-filter: blur(10px);
    }

    .status-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 14px;
        margin-bottom: 12px;
    }

    .status-card-title {
        font-size: 1.15rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: .7rem;
        color: #0f172a;
    }

    .status-dot {
        width: 12px;
        height: 12px;
        border-radius: 999px;
        background: #22c55e;
        box-shadow: 0 0 0 7px rgba(34,197,94,.20);
    }

    .student-identity {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 5px;
        text-align: right;
    }

    .status-nis-text {
        font-size: .84rem;
        color: #64748b;
    }

    .status-name-text {
        font-size: .86rem;
        color: #0f172a;
        font-weight: 700;
        background: #eff6ff;
        border: 1px solid #dbeafe;
        padding: .34rem .78rem;
        border-radius: 999px;
    }

    .status-form-wrap {
        border: 1px solid #e2e8f0;
        background: linear-gradient(180deg, #ffffff, #f8fafc);
        border-radius: 20px;
        padding: 14px;
        margin-bottom: 14px;
    }

    .status-form-row {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
    }

    .status-input-wrap {
        flex: 1 1 320px;
        position: relative;
    }

    .status-input {
        width: 100%;
        border-radius: 999px;
        padding: .76rem 1.15rem;
        font-size: .94rem;
        border: 1px solid #cbd5e1;
        outline: none;
        background: #fff;
        transition: .2s ease;
    }

    .status-input:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 4px rgba(79,70,229,.12);
    }

    .status-btn-submit {
        border-radius: 999px;
        padding: .76rem 1.6rem;
        border: none;
        background: linear-gradient(135deg, #2563eb, #4f46e5);
        color: #fff;
        font-size: .93rem;
        font-weight: 700;
        box-shadow: 0 14px 28px rgba(37,99,235,.24);
        min-width: 160px;
    }

    .status-btn-submit:hover {
        filter: brightness(1.05);
    }

    .status-hint {
        font-size: .8rem;
        color: #64748b;
        margin-bottom: 14px;
    }

    .status-table-wrapper {
        border-radius: 18px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        background: #fff;
    }

    .status-table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: collapse;
        font-size: .88rem;
    }

    .status-table thead {
        background: linear-gradient(90deg, #eff6ff, #e0f2fe);
    }

    .status-table thead th {
        padding: .8rem .92rem;
        font-weight: 700;
        color: #475569;
        border-bottom: 1px solid #e5e7eb;
        white-space: nowrap;
    }

    .status-table tbody td {
        padding: .8rem .92rem;
        border-bottom: 1px solid #f1f5f9;
        background: #ffffff;
        vertical-align: top;
    }

    .status-table tbody tr:nth-child(even) td {
        background: #fbfdff;
    }

    .status-table tbody tr:hover td {
        background: #eef2ff;
    }

    .status-book-title {
        font-weight: 700;
        color: #0f172a;
        line-height: 1.45;
        max-width: 240px;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .34rem .9rem;
        border-radius: 999px;
        font-size: .8rem;
        font-weight: 700;
    }

    .pill-active {
        background: #dcfce7;
        color: #166534;
    }

    .pill-pending {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .pill-returned {
        background: #e5e7eb;
        color: #374151;
    }

    .pill-late {
        background: #fee2e2;
        color: #991b1b;
    }

    .pill-dot {
        width: 8px;
        height: 8px;
        border-radius: 999px;
        background: currentColor;
    }

    .status-empty-text {
        font-size: .86rem;
        color: #6b7280;
    }

    .action-stack {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 6px;
    }

    .btn-action {
        width: 210px;
        max-width: 100%;
        justify-content: center;
        border: none;
        border-radius: 999px;
        padding: .68rem 1rem;
        font-weight: 800;
        font-size: .83rem;
        cursor: pointer;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: .48rem;
        line-height: 1;
        transition: .18s ease;
    }

    .btn-action:hover {
        transform: translateY(-1px);
        filter: brightness(1.03);
    }

    .btn-extend {
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        color: #111827;
        box-shadow: 0 12px 22px rgba(245,158,11,.22);
    }

    .btn-extend:disabled {
        opacity: .55;
        cursor: not-allowed;
        filter: none;
        box-shadow: none;
        transform: none;
    }

    .btn-fine {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: #fff;
        box-shadow: 0 12px 22px rgba(220,38,38,.24);
    }

    .extend-muted {
        font-size: .77rem;
        color: #6b7280;
        line-height: 1.3;
    }

    .fine-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, .10);
        z-index: 3000;
        display: none;
    }

    .fine-backdrop.show {
        display: block;
    }

    .fine-modal {
        position: fixed;
        inset: 0;
        z-index: 3010;
        display: none;
        padding: 16px;
        overflow: hidden;
    }

    .fine-modal.show {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .fine-modal-dialog {
        width: min(920px, 100%);
        max-height: calc(100vh - 32px);
        margin: 0 auto;
    }

    .fine-modal-content {
        border: none;
        border-radius: 30px;
        overflow: hidden;
        box-shadow: 0 30px 90px rgba(15,23,42,.20);
        background: #fff;
        position: relative;
        display: flex;
        flex-direction: column;
        max-height: calc(100vh - 32px);
    }

    .fine-modal-content::before {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        background:
            radial-gradient(circle at top right, rgba(249,115,22,.10), transparent 22%),
            radial-gradient(circle at bottom left, rgba(59,130,246,.06), transparent 20%);
    }

    .fine-modal-header,
    .fine-modal-body,
    .fine-modal-footer,
    .fine-book-card,
    .fine-total-card,
    .fine-detail-card,
    .fine-status-board,
    .fine-timeline,
    .fine-note-modern {
        position: relative;
        z-index: 1;
    }

    .fine-modal-header {
        background: linear-gradient(135deg, #7f1d1d 0%, #dc2626 48%, #f97316 100%);
        color: #fff;
        padding: 22px 24px 18px;
        border: none;
        overflow: hidden;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        flex-shrink: 0;
    }

    .fine-modal-header::before {
        content: "";
        position: absolute;
        right: -28px;
        top: -28px;
        width: 160px;
        height: 160px;
        border-radius: 999px;
        background: rgba(255,255,255,.10);
        pointer-events: none;
    }

    .fine-modal-header::after {
        content: "";
        position: absolute;
        left: 40%;
        bottom: -40px;
        width: 160px;
        height: 160px;
        border-radius: 999px;
        background: rgba(255,255,255,.05);
        pointer-events: none;
    }

    .fine-modal-title-wrap {
        display: flex;
        align-items: center;
        gap: 15px;
        flex: 1 1 auto;
        min-width: 0;
    }

    .fine-modal-icon {
        width: 60px;
        height: 60px;
        border-radius: 20px;
        background: rgba(255,255,255,.14);
        border: 1px solid rgba(255,255,255,.18);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.18);
        flex-shrink: 0;
    }

    .fine-modal-title {
        font-size: 1.5rem;
        font-weight: 900;
        margin: 0;
        line-height: 1.1;
        letter-spacing: .01em;
    }

    .fine-modal-subtitle {
        font-size: .9rem;
        opacity: .94;
        margin-top: 6px;
    }

    .fine-modal-close {
        border: 0;
        width: 48px;
        height: 48px;
        min-width: 48px;
        border-radius: 16px;
        background: rgba(255,255,255,.14);
        color: #fff;
        font-size: 1.8rem;
        line-height: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        flex-shrink: 0;
    }

    .fine-modal-close:hover {
        background: rgba(255,255,255,.22);
        transform: rotate(90deg);
    }

    .fine-modal-body {
        padding: 22px;
        overflow-y: auto;
        overflow-x: hidden;
        flex: 1 1 auto;
        min-height: 0;
        max-height: calc(100vh - 220px);
        -webkit-overflow-scrolling: touch;
    }

    .fine-hero-summary {
        display: grid;
        grid-template-columns: 1.2fr .8fr;
        gap: 16px;
        margin-bottom: 16px;
    }

    .fine-book-card,
    .fine-total-card,
    .fine-detail-card,
    .fine-note-modern {
        border-radius: 22px;
        border: 1px solid rgba(226,232,240,.95);
        background: rgba(255,255,255,.96);
        box-shadow: 0 12px 30px rgba(15,23,42,.06);
    }

    .fine-book-card,
    .fine-total-card {
        padding: 20px;
    }

    .fine-book-label,
    .fine-total-label,
    .fine-detail-head,
    .fine-section-label {
        font-size: .72rem;
        text-transform: uppercase;
        letter-spacing: .1em;
        color: #64748b;
        font-weight: 900;
        margin-bottom: 8px;
    }

    .fine-book-title {
        font-size: 1.34rem;
        font-weight: 900;
        color: #0f172a;
        line-height: 1.35;
        margin-bottom: 12px;
    }

    .fine-book-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .fine-mini-chip {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        padding: .45rem .85rem;
        border-radius: 999px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #334155;
        font-size: .8rem;
        font-weight: 700;
    }

    .fine-mini-chip .dot {
        width: 8px;
        height: 8px;
        border-radius: 999px;
        background: currentColor;
    }

    .fine-total-card {
        background: linear-gradient(135deg, #fff7ed, #ffedd5);
        border: 1px solid #fdba74;
        overflow: hidden;
    }

    .fine-total-card::after {
        content: "";
        position: absolute;
        right: -25px;
        bottom: -25px;
        width: 120px;
        height: 120px;
        border-radius: 999px;
        background: rgba(249,115,22,.10);
        pointer-events: none;
    }

    .fine-total-amount {
        font-size: 2.2rem;
        font-weight: 900;
        color: #c2410c;
        line-height: 1.05;
        margin-bottom: 8px;
    }

    .fine-total-helper {
        font-size: .9rem;
        color: #9a3412;
        margin-bottom: 14px;
    }

    .fine-progress {
        height: 10px;
        border-radius: 999px;
        background: rgba(251,146,60,.18);
        overflow: hidden;
    }

    .fine-progress-bar {
        width: 100%;
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(90deg, #f97316, #dc2626);
    }

    .fine-detail-layout {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 16px;
    }

    .fine-detail-stack {
        display: grid;
        gap: 12px;
    }

    .fine-detail-card {
        padding: 16px;
    }

    .fine-detail-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 8px;
    }

    .fine-detail-icon {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        flex-shrink: 0;
    }

    .fine-detail-value {
        font-size: 1rem;
        color: #0f172a;
        font-weight: 900;
        line-height: 1.45;
    }

    .fine-detail-subtext {
        font-size: .82rem;
        color: #64748b;
        margin-top: 4px;
        line-height: 1.45;
    }

    .fine-status-board {
        border-radius: 22px;
        padding: 18px;
        border: 1px solid rgba(226,232,240,.95);
        background: linear-gradient(180deg, rgba(255,255,255,.96), rgba(248,250,252,.96));
        box-shadow: 0 12px 30px rgba(15,23,42,.06);
    }

    .fine-status-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .fine-status-item {
        border-radius: 18px;
        padding: 14px;
        background: #fff;
        border: 1px solid #e5e7eb;
    }

    .fine-status-title {
        font-size: .75rem;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #64748b;
        font-weight: 800;
        margin-bottom: 9px;
    }

    .status-pill-big {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        padding: .5rem 1rem;
        border-radius: 999px;
        font-size: .83rem;
        font-weight: 800;
    }

    .status-pill-big.late {
        background: #fee2e2;
        color: #991b1b;
    }

    .status-pill-big.unpaid {
        background: #fff7ed;
        color: #c2410c;
    }

    .status-pill-big.paid {
        background: #dcfce7;
        color: #166534;
    }

    .status-pill-big .dot {
        width: 8px;
        height: 8px;
        border-radius: 999px;
        background: currentColor;
    }

    .fine-timeline {
        margin-top: 16px;
        border-radius: 22px;
        padding: 18px;
        border: 1px solid rgba(226,232,240,.95);
        background: rgba(255,255,255,.96);
        box-shadow: 0 12px 30px rgba(15,23,42,.06);
    }

    .fine-timeline-list {
        display: grid;
        gap: 12px;
    }

    .fine-timeline-item {
        display: grid;
        grid-template-columns: 42px 1fr;
        gap: 12px;
        align-items: start;
    }

    .fine-timeline-bullet {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        font-weight: 900;
        color: #fff;
        background: linear-gradient(135deg, #2563eb, #4f46e5);
        box-shadow: 0 10px 20px rgba(79,70,229,.18);
    }

    .fine-timeline-card {
        border-radius: 18px;
        background: #fff;
        border: 1px solid #e5e7eb;
        padding: 14px 15px;
    }

    .fine-timeline-title {
        font-size: .9rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 4px;
    }

    .fine-timeline-date {
        font-size: .95rem;
        font-weight: 900;
        color: #111827;
    }

    .fine-timeline-desc {
        font-size: .82rem;
        color: #64748b;
        margin-top: 3px;
        line-height: 1.45;
    }

    .fine-note-modern {
        margin-top: 16px;
        padding: 16px;
        border: 1px solid #fdba74;
        background: linear-gradient(135deg, #fff7ed, #fffbeb);
        color: #9a3412;
        display: grid;
        grid-template-columns: 40px 1fr;
        gap: 12px;
    }

    .fine-note-icon {
        width: 40px;
        height: 40px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(249,115,22,.12);
        font-size: 1.05rem;
    }

    .fine-note-title {
        font-size: .88rem;
        font-weight: 900;
        margin-bottom: 4px;
        color: #9a3412;
    }

    .fine-note-text {
        font-size: .84rem;
        line-height: 1.55;
    }

    .fine-modal-footer {
        border: none;
        padding: 0 22px 22px;
        background: #fff;
        display: flex;
        justify-content: space-between;
        gap: 10px;
        flex-shrink: 0;
    }

    .fine-footer-helper {
        font-size: .8rem;
        color: #64748b;
        display: inline-flex;
        align-items: center;
        gap: .45rem;
    }

    .btn-fine-close {
        border: none;
        border-radius: 999px;
        padding: .82rem 1.55rem;
        font-weight: 800;
        background: linear-gradient(135deg, #0f172a, #1e293b);
        color: #fff;
        box-shadow: 0 14px 28px rgba(15,23,42,.18);
        transition: .2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .45rem;
        cursor: pointer;
    }

    .btn-fine-close:hover {
        transform: translateY(-1px);
        filter: brightness(1.04);
        color: #fff;
    }

    @media (max-width: 992px) {
        .status-card-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .student-identity {
            align-items: flex-start;
            text-align: left;
        }

        .fine-hero-summary,
        .fine-detail-layout {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .status-wrapper {
            padding: 0 12px;
        }

        .status-hero {
            flex-direction: column;
            align-items: flex-start;
            padding: 18px 18px;
        }

        .status-hero-title {
            font-size: 1.5rem;
        }

        .status-card {
            padding: 16px;
        }

        .fine-modal {
            padding: 10px;
            align-items: flex-start;
        }

        .fine-modal-dialog {
            width: 100%;
            max-height: calc(100vh - 20px);
        }

        .fine-modal-content {
            max-height: calc(100vh - 20px);
        }

        .fine-modal-header {
            padding: 18px 18px 16px;
        }

        .fine-modal-body {
            padding: 16px;
            max-height: calc(100vh - 210px);
        }

        .fine-modal-title {
            font-size: 1.28rem;
        }

        .fine-book-title {
            font-size: 1.12rem;
        }

        .fine-total-amount {
            font-size: 1.8rem;
        }

        .fine-status-grid {
            grid-template-columns: 1fr;
        }

        .fine-modal-footer {
            padding: 0 16px 16px;
            flex-direction: column;
            align-items: stretch;
        }

        .btn-fine-close {
            width: 100%;
        }

        .btn-action {
            width: 100%;
            min-width: 0;
        }

        .status-btn-submit {
            width: 100%;
        }
    }
</style>

<div class="status-wrapper">
    <div class="status-hero">
        <div class="status-hero-left">
            <div class="status-hero-icon">📚</div>
            <div>
                <div class="status-hero-title">Cek Status Peminjaman</div>
                <div class="status-hero-sub">
                    Masukkan NIS untuk melihat buku yang sedang atau pernah Anda pinjam.
                </div>
            </div>
        </div>

        <div>
            <a href="{{ route('catalog') }}" class="btn-hero-back">
                ← Kembali ke Katalog
            </a>
        </div>
    </div>

    <div class="status-card">
        <div class="status-card-header">
            <div class="status-card-title">
                <span class="status-dot"></span>
                <span>Riwayat Peminjaman</span>
            </div>

            @if(!empty($nis))
                <div class="student-identity">
                    <span class="status-nis-text">NIS: <strong>{{ $nis }}</strong></span>
                    @if($studentName)
                        <span class="status-name-text">👤 {{ $studentName }}</span>
                    @endif
                </div>
            @endif
        </div>

        @if (session('success'))
            <div id="flash-message" class="alert alert-success py-2 mb-2">
                {{ session('success') }}
            </div>
        @elseif (session('error'))
            <div id="flash-message" class="alert alert-danger py-2 mb-2">
                {{ session('error') }}
            </div>
        @endif

        <div class="status-form-wrap">
            <form method="GET" action="{{ route('student.borrow.status') }}">
                <div class="status-form-row">
                    <div class="status-input-wrap">
                        <input
                            type="text"
                            name="nis"
                            class="status-input"
                            placeholder="Masukkan NIS siswa"
                            value="{{ old('nis', $nis ?? '') }}"
                        >
                    </div>
                    <button type="submit" class="status-btn-submit">Cek Status</button>
                </div>
            </form>

            <div class="status-hint mb-0">
                Tekan Enter setelah mengetik NIS, atau klik tombol <strong>Cek Status</strong>.
            </div>
        </div>

        @if(isset($borrowings) && $borrowings->count())
            <div class="status-table-wrapper mt-2">
                <table class="status-table">
                    <thead>
                        <tr>
                            <th style="width:60px;">No</th>
                            <th>Judul Buku</th>
                            <th style="width:170px;">Tanggal Pinjam</th>
                            <th style="width:170px;">Jatuh Tempo</th>
                            <th style="width:170px;">Tanggal Kembali</th>
                            <th style="width:130px;">Status</th>
                            <th style="width:170px;">Kadaluarsa</th>
                            <th style="width:220px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($borrowings as $index => $borrow)
                            @php
                                $status = $borrow->status ?? 'Diajukan';
                                $isDiajukan = $status === 'Diajukan';
                                $isDipinjam = $status === 'Dipinjam';
                                $isKembali  = $status === 'Kembali';
                                $isLate     = $status === 'Terlambat';

                                $rawExpire = $borrow->expired_at
                                    ?? $borrow->expires_at
                                    ?? $borrow->expire_at
                                    ?? null;

                                if (!$rawExpire && $borrow->created_at) {
                                    $rawExpire = Carbon::parse($borrow->created_at)->addDays(2);
                                }

                                $expiresAt = $rawExpire ? Carbon::parse($rawExpire) : null;
                                $isExpired = $expiresAt ? now()->gt($expiresAt->copy()->endOfDay()) : false;

                                $extendCount = (int) ($borrow->extend_count ?? 0);
                                $maxExtend   = 2;

                                $dueDate = $borrow->due_date ? Carbon::parse($borrow->due_date) : null;
                                $borrowDate = $borrow->borrow_date ? Carbon::parse($borrow->borrow_date) : null;
                                $returnDate = $borrow->return_date ? Carbon::parse($borrow->return_date) : null;

                                $isDueDatePassed = $dueDate ? now()->gt($dueDate->copy()->endOfDay()) : false;

                                $canExtend = false;
                                $disableReason = '';

                                if ($extendCount >= $maxExtend) {
                                    $disableReason = 'Batas perpanjang sudah maksimal';
                                } elseif ($isDiajukan) {
                                    if ($isExpired) {
                                        $disableReason = 'Pengajuan sudah kadaluarsa';
                                    } else {
                                        $canExtend = true;
                                    }
                                } elseif ($isDipinjam) {
                                    if (!$dueDate) {
                                        $disableReason = 'Jatuh tempo tidak tersedia';
                                    } elseif ($isDueDatePassed) {
                                        $disableReason = 'Peminjaman sudah melewati jatuh tempo';
                                    } else {
                                        $canExtend = true;
                                    }
                                }

                                $showExtendButton = $isDiajukan || $isDipinjam;

                                $extendLabel = '⏳ Perpanjang';
                                $extendTitle = $isDiajukan
                                    ? 'Perpanjang pengajuan +2 hari (maks 2x)'
                                    : 'Perpanjang jatuh tempo +2 hari (maks 2x)';
                                $extendConfirm = $isDiajukan
                                    ? "return confirm('Perpanjang pengajuan +2 hari? (maks 2x)')"
                                    : "return confirm('Perpanjang peminjaman +2 hari? Jatuh tempo akan ikut berubah. (maks 2x)')";

                                $lateDays = (int) ($borrow->late_days ?? 0);

                                if ($isLate && $dueDate) {
                                    $comparisonDate = $returnDate ? $returnDate->copy()->startOfDay() : now()->startOfDay();
                                    $calculatedLateDays = $dueDate->copy()->startOfDay()->diffInDays($comparisonDate, false);
                                    $calculatedLateDays = max(0, (int) $calculatedLateDays);

                                    if ($lateDays <= 0) {
                                        $lateDays = $calculatedLateDays;
                                    }
                                }

                                if ($lateDays < 0) {
                                    $lateDays = 0;
                                }

                                $fineAmount = (int) ($borrow->fine_amount ?? 0);
                                if ($fineAmount <= 0) {
                                    $fineAmount = $lateDays * $finePerDay;
                                }

                                $finePaid = filter_var($borrow->fine_paid ?? false, FILTER_VALIDATE_BOOLEAN);

                                if ($isLate) {
                                    $lateModals[] = [
                                        'id' => $borrow->id,
                                        'title' => optional($borrow->book)->title ?? '-',
                                        'borrow_date' => $borrowDate ? $borrowDate->translatedFormat('d F Y') : '-',
                                        'due_date' => $dueDate ? $dueDate->translatedFormat('d F Y') : '-',
                                        'return_date' => $returnDate ? $returnDate->translatedFormat('d F Y') : 'Belum dikembalikan',
                                        'late_days' => $lateDays,
                                        'fine_per_day' => $finePerDay,
                                        'fine_amount' => $fineAmount,
                                        'fine_paid' => $finePaid,
                                    ];
                                }
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="status-book-title">{{ optional($borrow->book)->title ?? '-' }}</div>
                                </td>

                                <td>
                                    @if(!$isDiajukan && $borrowDate)
                                        {{ $borrowDate->translatedFormat('d F Y') }}
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>
                                    @if(!$isDiajukan && $dueDate)
                                        {{ $dueDate->translatedFormat('d F Y') }}
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>
                                    @if($returnDate)
                                        {{ $returnDate->translatedFormat('d F Y') }}
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>
                                    @if($isDiajukan)
                                        <span class="status-pill pill-pending">
                                            <span class="pill-dot"></span>
                                            Diajukan
                                        </span>
                                    @elseif($isDipinjam)
                                        <span class="status-pill pill-active">
                                            <span class="pill-dot"></span>
                                            Dipinjam
                                        </span>
                                    @elseif($isLate)
                                        <span class="status-pill pill-late">
                                            <span class="pill-dot"></span>
                                            Terlambat
                                        </span>
                                    @else
                                        <span class="status-pill pill-returned">
                                            <span class="pill-dot"></span>
                                            Dikembalikan
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    @if($isDiajukan)
                                        @if($expiresAt)
                                            @if($isExpired)
                                                <span class="extend-muted">Kadaluarsa</span>
                                            @else
                                                {{ $expiresAt->translatedFormat('d F Y') }}
                                            @endif
                                        @else
                                            -
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>
                                    @if($showExtendButton)
                                        <div class="action-stack">
                                            <form method="POST" action="{{ route('student.borrow.extend', $borrow->id) }}">
                                                @csrf
                                                <input type="hidden" name="nis" value="{{ $nis }}">
                                                <input type="hidden" name="active_borrow_count" value="{{ $activeBorrowCount }}">
                                                <input type="hidden" name="max_active_books" value="{{ $maxActiveBooks }}">

                                                <button
                                                    type="submit"
                                                    class="btn-action btn-extend"
                                                    {{ $canExtend ? '' : 'disabled' }}
                                                    title="{{ $canExtend ? $extendTitle : $disableReason }}"
                                                    onclick="{{ $canExtend ? $extendConfirm : 'return false;' }}"
                                                >
                                                    {{ $extendLabel }} ({{ $extendCount }}/{{ $maxExtend }})
                                                </button>
                                            </form>

                                            @if(!$canExtend)
                                                <div class="extend-muted">
                                                    {{ $disableReason ?: '-' }}
                                                </div>
                                            @endif
                                        </div>
                                    @elseif($isLate)
                                        <button
                                            type="button"
                                            class="btn-action btn-fine"
                                            onclick="return openFineModal('fineModal{{ $borrow->id }}')"
                                        >
                                            💸 Lihat Denda
                                        </button>
                                    @else
                                        <span class="extend-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div id="fineBackdrop" class="fine-backdrop" onclick="closeActiveFineModal()"></div>

            @foreach($lateModals as $modal)
                <div
                    class="fine-modal"
                    id="fineModal{{ $modal['id'] }}"
                    tabindex="-1"
                    aria-labelledby="fineModalLabel{{ $modal['id'] }}"
                    aria-hidden="true"
                >
                    <div class="fine-modal-dialog">
                        <div class="fine-modal-content">
                            <div class="fine-modal-header">
                                <div class="fine-modal-title-wrap">
                                    <div class="fine-modal-icon">💸</div>
                                    <div>
                                        <h5 class="fine-modal-title" id="fineModalLabel{{ $modal['id'] }}">
                                            Rincian Denda
                                        </h5>
                                        <div class="fine-modal-subtitle">
                                            Informasi lengkap keterlambatan, timeline pengembalian, dan status pembayaran.
                                        </div>
                                    </div>
                                </div>

                                <button
                                    type="button"
                                    class="fine-modal-close"
                                    onclick="return closeFineModal('fineModal{{ $modal['id'] }}')"
                                    aria-label="Tutup"
                                >
                                    ×
                                </button>
                            </div>

                            <div class="fine-modal-body">
                                <div class="fine-hero-summary">
                                    <div class="fine-book-card">
                                        <div class="fine-book-label">Buku yang terkena denda</div>
                                        <div class="fine-book-title">{{ $modal['title'] }}</div>

                                        <div class="fine-book-meta">
                                            <span class="fine-mini-chip">
                                                <span class="dot" style="color:#1d4ed8;"></span>
                                                Pinjam: {{ $modal['borrow_date'] }}
                                            </span>
                                            <span class="fine-mini-chip">
                                                <span class="dot" style="color:#ea580c;"></span>
                                                Jatuh tempo: {{ $modal['due_date'] }}
                                            </span>
                                            <span class="fine-mini-chip">
                                                <span class="dot" style="color:#475569;"></span>
                                                Kembali: {{ $modal['return_date'] }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="fine-total-card">
                                        <div class="fine-total-label">Total denda saat ini</div>
                                        <div class="fine-total-amount">
                                            Rp {{ number_format($modal['fine_amount'], 0, ',', '.') }}
                                        </div>
                                        <div class="fine-total-helper">
                                            Akumulasi {{ number_format($modal['late_days'], 0, ',', '.') }} hari keterlambatan × Rp {{ number_format($modal['fine_per_day'], 0, ',', '.') }} / hari
                                        </div>

                                        <div class="fine-progress">
                                            <div class="fine-progress-bar"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="fine-detail-layout">
                                    <div class="fine-detail-stack">
                                        <div class="fine-detail-card">
                                            <div class="fine-detail-top">
                                                <div>
                                                    <div class="fine-detail-head">Hari keterlambatan</div>
                                                    <div class="fine-detail-value">{{ number_format($modal['late_days'], 0, ',', '.') }} hari</div>
                                                </div>
                                                <div class="fine-detail-icon">⏱️</div>
                                            </div>
                                            <div class="fine-detail-subtext">
                                                Semakin lama buku belum diselesaikan, total denda akan terus mengikuti jumlah hari keterlambatan yang tercatat.
                                            </div>
                                        </div>

                                        <div class="fine-detail-card">
                                            <div class="fine-detail-top">
                                                <div>
                                                    <div class="fine-detail-head">Tarif per hari</div>
                                                    <div class="fine-detail-value">
                                                        Rp {{ number_format($modal['fine_per_day'], 0, ',', '.') }}
                                                    </div>
                                                </div>
                                                <div class="fine-detail-icon">💰</div>
                                            </div>
                                            <div class="fine-detail-subtext">
                                                Tarif denda dihitung per hari keterlambatan sesuai aturan perpustakaan yang berlaku.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="fine-status-board">
                                        <div class="fine-section-label">Status saat ini</div>

                                        <div class="fine-status-grid">
                                            <div class="fine-status-item">
                                                <div class="fine-status-title">Status peminjaman</div>
                                                <span class="status-pill-big late">
                                                    <span class="dot"></span>
                                                    Terlambat
                                                </span>
                                            </div>

                                            <div class="fine-status-item">
                                                <div class="fine-status-title">Status pembayaran</div>
                                                @if($modal['fine_paid'])
                                                    <span class="status-pill-big paid">
                                                        <span class="dot"></span>
                                                        Lunas
                                                    </span>
                                                @else
                                                    <span class="status-pill-big unpaid">
                                                        <span class="dot"></span>
                                                        Belum Bayar
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="fine-status-item">
                                                <div class="fine-status-title">Tanggal jatuh tempo</div>
                                                <div class="fine-detail-value">{{ $modal['due_date'] }}</div>
                                            </div>

                                            <div class="fine-status-item">
                                                <div class="fine-status-title">Tanggal pengembalian</div>
                                                <div class="fine-detail-value">{{ $modal['return_date'] }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="fine-timeline">
                                    <div class="fine-section-label">Timeline peminjaman</div>

                                    <div class="fine-timeline-list">
                                        <div class="fine-timeline-item">
                                            <div class="fine-timeline-bullet">1</div>
                                            <div class="fine-timeline-card">
                                                <div class="fine-timeline-title">Tanggal peminjaman</div>
                                                <div class="fine-timeline-date">{{ $modal['borrow_date'] }}</div>
                                                <div class="fine-timeline-desc">
                                                    Buku mulai tercatat sebagai pinjaman aktif pada tanggal ini.
                                                </div>
                                            </div>
                                        </div>

                                        <div class="fine-timeline-item">
                                            <div class="fine-timeline-bullet">2</div>
                                            <div class="fine-timeline-card">
                                                <div class="fine-timeline-title">Batas pengembalian</div>
                                                <div class="fine-timeline-date">{{ $modal['due_date'] }}</div>
                                                <div class="fine-timeline-desc">
                                                    Setelah melewati tanggal ini, keterlambatan mulai dihitung.
                                                </div>
                                            </div>
                                        </div>

                                        <div class="fine-timeline-item">
                                            <div class="fine-timeline-bullet">3</div>
                                            <div class="fine-timeline-card">
                                                <div class="fine-timeline-title">Status akhir</div>
                                                <div class="fine-timeline-date">{{ $modal['return_date'] }}</div>
                                                <div class="fine-timeline-desc">
                                                    Tercatat {{ number_format($modal['late_days'], 0, ',', '.') }} hari terlambat dengan total denda Rp {{ number_format($modal['fine_amount'], 0, ',', '.') }}.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="fine-note-modern">
                                    <div class="fine-note-icon">📌</div>
                                    <div>
                                        <div class="fine-note-title">Informasi penyelesaian denda</div>
                                        <div class="fine-note-text">
                                            Silakan hubungi petugas perpustakaan untuk konfirmasi nominal akhir, proses pembayaran, dan validasi status denda setelah pelunasan.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="fine-modal-footer">
                                <div class="fine-footer-helper">
                                    <span>ℹ️</span>
                                    <span>Pastikan status pembayaran diperbarui oleh petugas setelah pelunasan.</span>
                                </div>

                                <button
                                    type="button"
                                    class="btn-fine-close"
                                    onclick="return closeFineModal('fineModal{{ $modal['id'] }}')"
                                >
                                    Tutup Modal
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @elseif(!empty($nis))
            <p class="mt-2 status-empty-text">
                Tidak ditemukan peminjaman untuk NIS <strong>{{ $nis }}</strong>.
            </p>
        @endif
    </div>
</div>

<script>
    (function () {
        const el = document.getElementById('flash-message');
        if (!el) return;

        setTimeout(() => {
            el.style.transition = 'opacity 400ms ease';
            el.style.opacity = '0';

            setTimeout(() => {
                if (el && el.parentNode) {
                    el.parentNode.removeChild(el);
                }
            }, 450);
        }, 4000);
    })();

    function getFineBackdrop() {
        return document.getElementById('fineBackdrop');
    }

    function cleanupModalArtifacts() {
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');

        const backdrop = getFineBackdrop();
        if (backdrop) {
            backdrop.classList.remove('show');
        }
    }

    function openFineModal(modalId) {
        const modalEl = document.getElementById(modalId);
        const backdrop = getFineBackdrop();
        if (!modalEl || !backdrop) return false;

        document.querySelectorAll('.fine-modal.show').forEach(function (item) {
            item.classList.remove('show');
            item.setAttribute('aria-hidden', 'true');
        });

        backdrop.classList.add('show');
        modalEl.classList.add('show');
        modalEl.setAttribute('aria-hidden', 'false');

        document.body.classList.add('modal-open');
        document.body.style.overflow = 'hidden';

        return false;
    }

    function closeFineModal(modalId) {
        const modalEl = document.getElementById(modalId);
        if (!modalEl) return false;

        modalEl.classList.remove('show');
        modalEl.setAttribute('aria-hidden', 'true');
        cleanupModalArtifacts();

        return false;
    }

    function closeActiveFineModal() {
        const activeModal = document.querySelector('.fine-modal.show');
        if (activeModal) {
            closeFineModal(activeModal.id);
        }
        return false;
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeActiveFineModal();
            }
        });
    });
</script>
@endsection

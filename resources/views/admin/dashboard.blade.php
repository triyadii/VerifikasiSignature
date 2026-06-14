@extends('layouts.app')
@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Total Verifikasi</div>
        <div class="stat-value stat-blue">{{ number_format($totalLogs) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Dokumen Valid</div>
        <div class="stat-value stat-green">{{ number_format($validLogs) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Dokumen Tidak Valid</div>
        <div class="stat-value stat-red">{{ number_format($invalidLogs) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total User</div>
        <div class="stat-value stat-purple">{{ number_format($totalUsers) }}</div>
    </div>
</div>

<div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
        <p class="card-title" style="margin:0">Log Verifikasi Terbaru</p>
        <a href="{{ route('admin.logs') }}" class="btn btn-secondary btn-sm">Lihat Semua →</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nama File</th>
                    <th>Status</th>
                    <th>Penandatangan</th>
                    <th>Diperiksa Oleh</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentLogs as $log)
                <tr>
                    <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="{{ $log->filename }}">
                        {{ $log->filename }}
                    </td>
                    <td>
                        @if($log->status === 'VALID')
                            <span class="badge badge-green">✅ Valid</span>
                        @elseif($log->status === 'TIDAK VALID')
                            <span class="badge badge-red">❌ Tidak Valid</span>
                        @else
                            <span class="badge badge-yellow">⚠️ Error</span>
                        @endif
                    </td>
                    <td>{{ $log->nama_penandatangan ?? '-' }}</td>
                    <td>{{ $log->user->name ?? 'Tidak diketahui' }}</td>
                    <td style="white-space:nowrap;font-size:.8rem;color:#8892a4">
                        {{ $log->created_at->format('d/m/Y H:i') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;color:#8892a4;padding:32px">
                        Belum ada data verifikasi.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

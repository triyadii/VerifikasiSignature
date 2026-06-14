@extends('layouts.app')
@section('title', 'Log Verifikasi')
@section('page-title', 'Log Verifikasi')

@section('content')
<div class="card">
    <form method="GET" action="{{ route('admin.logs') }}"
          style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px;align-items:flex-end">
        <div>
            <label class="form-label" style="margin-bottom:4px">Cari</label>
            <input type="text" name="search" class="form-control" style="width:220px"
                   placeholder="File / penandatangan / user"
                   value="{{ request('search') }}">
        </div>
        <div>
            <label class="form-label" style="margin-bottom:4px">Status</label>
            <select name="status" class="form-control" style="width:160px">
                <option value="">Semua Status</option>
                <option value="VALID"       {{ request('status')==='VALID'       ?'selected':'' }}>Valid</option>
                <option value="TIDAK VALID" {{ request('status')==='TIDAK VALID' ?'selected':'' }}>Tidak Valid</option>
                <option value="ERROR"       {{ request('status')==='ERROR'       ?'selected':'' }}>Error</option>
            </select>
        </div>
        <div style="display:flex;gap:8px">
            <button type="submit" class="btn btn-primary">🔍 Filter</button>
            <a href="{{ route('admin.logs') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama File</th>
                    <th>Status</th>
                    <th>Penandatangan</th>
                    <th>Instansi</th>
                    <th>Diperiksa Oleh</th>
                    <th>IP Address</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $i => $log)
                <tr>
                    <td style="color:#8892a4;font-size:.8rem">{{ $logs->firstItem() + $i }}</td>
                    <td style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="{{ $log->filename }}">
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
                    <td>{{ $log->instansi ?? '-' }}</td>
                    <td>
                        <div style="font-size:.85rem">{{ $log->user->name ?? 'Dihapus' }}</div>
                        <div style="font-size:.72rem;color:#8892a4">{{ $log->user->email ?? '' }}</div>
                    </td>
                    <td style="font-size:.8rem;color:#8892a4">{{ $log->ip_address ?? '-' }}</td>
                    <td style="white-space:nowrap;font-size:.8rem;color:#8892a4">
                        {{ $log->created_at->format('d/m/Y H:i') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:40px;color:#8892a4">
                        Tidak ada log yang ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:8px;font-size:.78rem;color:#8892a4">
        Total: {{ $logs->total() }} data &nbsp;·&nbsp; Halaman {{ $logs->currentPage() }} dari {{ $logs->lastPage() }}
    </div>

    <div style="margin-top:12px">
        {{ $logs->withQueryString()->links() }}
    </div>
</div>
@endsection

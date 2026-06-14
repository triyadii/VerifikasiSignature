@extends('layouts.app')
@section('title', 'Data Sertifikat')
@section('page-title', 'Manajemen Data Sertifikat')

@section('content')
<style>
    /* ── HEADER ACTIONS ── */
    .header-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .search-box {
        position: relative;
        flex: 1;
        max-width: 380px;
        min-width: 260px;
    }

    .search-box input {
        width: 100%;
        padding: 10px 16px 10px 40px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 8px;
        color: var(--text);
        font-size: .875rem;
        transition: border-color .2s, box-shadow .2s;
        outline: none;
    }

    .search-box input:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(79, 124, 255, 0.15);
    }

    .search-box .icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--muted);
        font-size: 0.9rem;
        pointer-events: none;
    }

    /* ── MODAL STYLING ── */
    .modal-overlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(15, 17, 23, 0.7);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
    }

    .modal-overlay.active {
        opacity: 1;
        pointer-events: auto;
    }

    .modal-container {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        width: 100%;
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5), 0 10px 10px -5px rgba(0, 0, 0, 0.4);
        transform: scale(0.95);
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        display: flex;
        flex-direction: column;
    }

    .modal-overlay.active .modal-container {
        transform: scale(1);
    }

    .modal-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h3 {
        font-size: 1.1rem;
        font-weight: 600;
        background: linear-gradient(135deg, var(--accent), var(--accent2));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .modal-close {
        background: none;
        border: none;
        color: var(--muted);
        font-size: 1.5rem;
        cursor: pointer;
        line-height: 1;
        transition: color 0.2s;
    }

    .modal-close:hover {
        color: var(--text);
    }

    .modal-body {
        padding: 24px;
    }

    /* ── EMPTY STATE ── */
    .empty-state {
        text-align: center;
        padding: 48px 24px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 14px;
        margin-top: 16px;
    }

    .empty-state-icon {
        font-size: 3rem;
        margin-bottom: 16px;
        display: inline-block;
        filter: drop-shadow(0 0 10px rgba(79, 124, 255, 0.2));
    }

    .empty-state h4 {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 8px;
        color: var(--text);
    }

    .empty-state p {
        font-size: 0.85rem;
        color: var(--muted);
        max-width: 320px;
        margin: 0 auto;
    }
</style>

<div class="header-actions">
    <!-- Form Search -->
    <form method="GET" action="{{ route('sertifikat.index') }}" class="search-box">
        <span class="icon">🔍</span>
        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nomor ID, nama PU, nama usaha..." onchange="this.form.submit()">
    </form>

    <!-- Tombol Modal -->
    <button type="button" id="btnTambahSertifikat" class="btn btn-primary">
        <span>➕</span> Tambah Sertifikat Baru
    </button>
</div>

<!-- Tampilan Utama: Tabel Data -->
<div class="card">
    <div class="card-title">📜 Daftar Sertifikat Terdaftar</div>
    
    @if($sertifikats->isEmpty())
        <div class="empty-state">
            <span class="empty-state-icon">📄</span>
            <h4>Belum ada data sertifikat</h4>
            <p>
                @if($search)
                    Tidak ditemukan data sertifikat yang cocok dengan pencarian "{{ $search }}".
                @else
                    Data sertifikat kosong. Silakan tambahkan data baru dengan tombol di atas.
                @endif
            </p>
        </div>
    @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px">No</th>
                        <th>Nomor ID Sertifikat</th>
                        <th>Nama PU</th>
                        <th>Nama Usaha</th>
                        <th>Alamat Lokasi Usaha</th>
                        <th>Tanggal Dikeluarkan</th>
                        <th>Jenis Legalitas</th>
                        <th style="text-align: center; width: 240px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sertifikats as $index => $item)
                        <tr>
                            <td>{{ ($sertifikats->currentPage() - 1) * $sertifikats->perPage() + $loop->iteration }}</td>
                            <td style="font-weight: 600; color: var(--accent);">
                                @if($item->signature)
                                    <span style="color: var(--green); margin-right: 4px;" title="Tanda Tangan Digital Aktif (RSA)">🔒</span>
                                @endif
                                {{ $item->nomor_id_sertifikat }}
                            </td>
                            <td>{{ $item->nama_pu }}</td>
                            <td>{{ $item->nama_usaha }}</td>
                            <td style="max-width: 250px; white-space: normal; line-height: 1.4;">{{ $item->alamat_lokasi_usaha }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_dikeluarkan_surat)->translatedFormat('d F Y') }}</td>
                            <td>
                                <span class="badge badge-blue">{{ $item->jenis_legalitas_usaha }}</span>
                            </td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 6px; justify-content: center; align-items: center; flex-wrap: wrap;">
                                    @if($item->signature)
                                        @if($item->pdf_path)
                                            {{-- PDF sudah dibuat: tampilkan tombol Download + Buat Ulang --}}
                                            <a href="{{ route('sertifikat.download', $item->id) }}" class="btn btn-secondary btn-sm" target="_blank" title="Buka/Unduh PDF" style="background: rgba(34,197,94,.12); color: var(--green); border: 1px solid rgba(34,197,94,.25);">
                                                📥 Download PDF
                                            </a>
                                            <a href="{{ route('sertifikat.generate-pdf', $item->id) }}" class="btn btn-secondary btn-sm" title="Generate ulang PDF" style="background: rgba(245,158,11,.12); color: var(--yellow); border: 1px solid rgba(245,158,11,.25);" onclick="return confirm('Buat ulang PDF? File lama akan diganti.')">
                                                🔄 Buat Ulang
                                            </a>
                                        @else
                                            {{-- Sudah TTE tapi belum ada PDF --}}
                                            <a href="{{ route('sertifikat.generate-pdf', $item->id) }}" class="btn btn-secondary btn-sm" title="Generate PDF Sertifikat" style="background: rgba(34,197,94,.12); color: var(--green); border: 1px solid rgba(34,197,94,.25);">
                                                📄 Buat PDF
                                            </a>
                                        @endif
                                        <a href="{{ route('sertifikat.generate', $item->id) }}" class="btn btn-secondary btn-sm" title="Lihat Detail RSA Keys" style="background: rgba(245,158,11,.12); color: var(--yellow); border: 1px solid rgba(245,158,11,.25);">
                                            🔑 RSA
                                        </a>
                                    @else
                                        <a href="{{ route('sertifikat.generate', $item->id) }}" class="btn btn-primary btn-sm" title="Tanda Tangan Digital RSA" style="background: var(--accent2);">
                                            ✍️ TTE RSA
                                        </a>
                                    @endif
                                    
                                    <a href="{{ route('sertifikat.edit', $item->id) }}" class="btn btn-secondary btn-sm" title="Edit Data" style="background: rgba(79,124,255,.1); border-color: rgba(79,124,255,.2); color: var(--accent);">
                                        ✏️ Edit
                                    </a>
                                    
                                    <form action="{{ route('sertifikat.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data sertifikat ini?')" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus Data">
                                            🗑️ Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Links -->
        <div style="margin-top: 20px">
            {{ $sertifikats->links() }}
        </div>
    @endif
</div>

<!-- Modal Input Form -->
<div id="sertifikatModal" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h3>📝 Form Input Data Sertifikat</h3>
            <button type="button" class="modal-close" id="btnCloseModal">&times;</button>
        </div>
        <form method="POST" action="{{ route('sertifikat.store') }}">
            @csrf
            <div class="modal-body">
                
                <div class="form-group">
                    <label class="form-label" for="nomor_id_sertifikat">Nomor ID Sertifikat</label>
                    <input type="text" id="nomor_id_sertifikat" name="nomor_id_sertifikat" 
                           class="form-control @error('nomor_id_sertifikat') is-invalid @enderror"
                           value="{{ old('nomor_id_sertifikat') }}" placeholder="Masukkan Nomor ID Sertifikat..." required>
                    @error('nomor_id_sertifikat')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="nama_pu">Nama PU (Pelaku Usaha)</label>
                    <input type="text" id="nama_pu" name="nama_pu" 
                           class="form-control @error('nama_pu') is-invalid @enderror"
                           value="{{ old('nama_pu') }}" placeholder="Masukkan nama pemilik / pelaku usaha..." required>
                    @error('nama_pu')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="nama_usaha">Nama Usaha</label>
                    <input type="text" id="nama_usaha" name="nama_usaha" 
                           class="form-control @error('nama_usaha') is-invalid @enderror"
                           value="{{ old('nama_usaha') }}" placeholder="Masukkan Nama Usaha..." required>
                    @error('nama_usaha')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="alamat_lokasi_usaha">Alamat Lokasi Usaha</label>
                    <textarea id="alamat_lokasi_usaha" name="alamat_lokasi_usaha" rows="3" 
                              class="form-control @error('alamat_lokasi_usaha') is-invalid @enderror"
                              placeholder="Tuliskan alamat lengkap lokasi usaha..." required>{{ old('alamat_lokasi_usaha') }}</textarea>
                    @error('alamat_lokasi_usaha')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="tanggal_dikeluarkan_surat">Tanggal Dikeluarkan Surat</label>
                    <input type="date" id="tanggal_dikeluarkan_surat" name="tanggal_dikeluarkan_surat" 
                           class="form-control @error('tanggal_dikeluarkan_surat') is-invalid @enderror"
                           value="{{ old('tanggal_dikeluarkan_surat') }}" required>
                    @error('tanggal_dikeluarkan_surat')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="jenis_legalitas_usaha">Jenis Legalitas Usaha</label>
                    <input type="text" id="jenis_legalitas_usaha" name="jenis_legalitas_usaha" 
                           class="form-control @error('jenis_legalitas_usaha') is-invalid @enderror"
                           value="{{ old('jenis_legalitas_usaha') }}" placeholder="Contoh: NIB, SIUP, PIRT..." required>
                    @error('jenis_legalitas_usaha')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px; border-top:1px solid var(--border); padding-top:16px;">
                    <button type="button" id="btnBatalSertifikat" class="btn btn-secondary">Batal</button>
                    <button type="submit" class="btn btn-primary">💾 Simpan Data</button>
                </div>

            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalOverlay = document.getElementById('sertifikatModal');
        const btnTambah = document.getElementById('btnTambahSertifikat');
        const btnBatal = document.getElementById('btnBatalSertifikat');
        const btnClose = document.getElementById('btnCloseModal');

        function openModal() {
            modalOverlay.classList.add('active');
            document.body.style.overflow = 'hidden'; // Prevent main page scrolling
        }

        function closeModal() {
            modalOverlay.classList.remove('active');
            document.body.style.overflow = ''; // Re-enable scrolling
        }

        if (btnTambah) btnTambah.addEventListener('click', openModal);
        if (btnBatal) btnBatal.addEventListener('click', closeModal);
        if (btnClose) btnClose.addEventListener('click', closeModal);

        // Close when clicking overlay backdrop (outside the modal container)
        modalOverlay.addEventListener('click', function(e) {
            if (e.target === modalOverlay) {
                closeModal();
            }
        });

        // ESC key to close modal
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modalOverlay.classList.contains('active')) {
                closeModal();
            }
        });

        // Automatically open the modal if validation failed to show validation feedback
        @if ($errors->any())
            openModal();
        @endif
    });
</script>
@endsection

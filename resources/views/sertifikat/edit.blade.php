@extends('layouts.app')
@section('title', 'Edit Sertifikat')
@section('page-title', 'Edit Data Sertifikat')

@section('content')
<div style="max-width:600px">
    <div class="card">
        <p class="card-title" style="display: flex; align-items: center; gap: 8px;">
            <span>✏️</span> Edit Data Sertifikat
        </p>

        @if($sertifikat->signature)
            <div class="alert alert-danger" style="margin-bottom: 20px; font-size: 0.85rem;">
                ⚠️ <strong>Perhatian:</strong> Sertifikat ini sudah memiliki Tanda Tangan Digital (RSA). Jika Anda mengubah data ini, tanda tangan digital lama akan <strong>dihapus secara otomatis</strong> untuk menjaga integritas keabsahan dokumen, dan Anda harus melakukan proses <strong>TTE RSA ulang</strong> setelah menyimpan.
            </div>
        @endif

        <form method="POST" action="{{ route('sertifikat.update', $sertifikat->id) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label" for="nomor_id_sertifikat">Nomor ID Sertifikat</label>
                <input type="text" id="nomor_id_sertifikat" name="nomor_id_sertifikat"
                       class="form-control {{ $errors->has('nomor_id_sertifikat') ? 'is-invalid' : '' }}"
                       value="{{ old('nomor_id_sertifikat', $sertifikat->nomor_id_sertifikat) }}" required>
                @error('nomor_id_sertifikat')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="nama_pu">Nama PU (Pelaku Usaha)</label>
                <input type="text" id="nama_pu" name="nama_pu"
                       class="form-control {{ $errors->has('nama_pu') ? 'is-invalid' : '' }}"
                       value="{{ old('nama_pu', $sertifikat->nama_pu) }}" required>
                @error('nama_pu')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="nama_usaha">Nama Usaha</label>
                <input type="text" id="nama_usaha" name="nama_usaha"
                       class="form-control {{ $errors->has('nama_usaha') ? 'is-invalid' : '' }}"
                       value="{{ old('nama_usaha', $sertifikat->nama_usaha) }}" required>
                @error('nama_usaha')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="alamat_lokasi_usaha">Alamat Lokasi Usaha</label>
                <textarea id="alamat_lokasi_usaha" name="alamat_lokasi_usaha" rows="3"
                          class="form-control {{ $errors->has('alamat_lokasi_usaha') ? 'is-invalid' : '' }}" required>{{ old('alamat_lokasi_usaha', $sertifikat->alamat_lokasi_usaha) }}</textarea>
                @error('alamat_lokasi_usaha')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="tanggal_dikeluarkan_surat">Tanggal Dikeluarkan Surat</label>
                <input type="date" id="tanggal_dikeluarkan_surat" name="tanggal_dikeluarkan_surat"
                       class="form-control {{ $errors->has('tanggal_dikeluarkan_surat') ? 'is-invalid' : '' }}"
                       value="{{ old('tanggal_dikeluarkan_surat', $sertifikat->tanggal_dikeluarkan_surat) }}" required>
                @error('tanggal_dikeluarkan_surat')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="jenis_legalitas_usaha">Jenis Legalitas Usaha</label>
                <input type="text" id="jenis_legalitas_usaha" name="jenis_legalitas_usaha"
                       class="form-control {{ $errors->has('jenis_legalitas_usaha') ? 'is-invalid' : '' }}"
                       value="{{ old('jenis_legalitas_usaha', $sertifikat->jenis_legalitas_usaha) }}" required>
                @error('jenis_legalitas_usaha')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div style="display:flex; gap:10px; margin-top:24px; border-top:1px solid var(--border); padding-top:16px;">
                <button type="submit" class="btn btn-primary">💾 Perbarui Data</button>
                <a href="{{ route('sertifikat.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

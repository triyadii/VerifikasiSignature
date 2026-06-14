@extends('layouts.app')
@section('title', 'Verifikasi TTE')
@section('page-title', 'Verifikasi Tanda Tangan Elektronik')

@section('content')
<style>
    .upload-wrap { max-width: 640px; margin: 0 auto; }
    .drop-area {
        border: 2px dashed #2e3348;
        border-radius: 14px;
        padding: 48px 24px;
        text-align: center;
        cursor: pointer;
        transition: all .25s;
        background: rgba(79,124,255,.03);
        position: relative;
    }
    .drop-area:hover, .drop-area.dragover {
        border-color: #4f7cff;
        background: rgba(79,124,255,.08);
    }
    .drop-icon { font-size: 2.8rem; margin-bottom: 12px; }
    .drop-title { font-size: 1rem; font-weight: 600; margin-bottom: 4px; }
    .drop-hint  { font-size: .78rem; color: #8892a4; }
    #fileInput  { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
    .file-preview {
        margin-top: 12px;
        padding: 10px 14px;
        background: rgba(79,124,255,.1);
        border: 1px solid rgba(79,124,255,.3);
        border-radius: 8px;
        font-size: .85rem;
        color: #4f7cff;
        display: none;
    }
    .btn-upload {
        margin-top: 20px;
        width: 100%;
        padding: 13px;
        background: linear-gradient(135deg, #4f7cff, #7c5cfc);
        border: none;
        border-radius: 10px;
        color: #fff;
        font-size: .95rem;
        font-weight: 600;
        font-family: 'Inter', sans-serif;
        cursor: pointer;
        transition: opacity .2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 4px 16px rgba(79,124,255,.3);
    }
    .btn-upload:hover { opacity: .9; }
    .spinner {
        width: 16px; height: 16px;
        border: 2px solid rgba(255,255,255,.4);
        border-top-color: #fff;
        border-radius: 50%;
        animation: spin .7s linear infinite;
        display: none;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* Result */
    .result-box { margin-top: 24px; border-radius: 14px; padding: 24px; border: 1px solid; }
    .result-valid   { background: rgba(34,197,94,.06);  border-color: rgba(34,197,94,.25); }
    .result-invalid { background: rgba(239,68,68,.06);  border-color: rgba(239,68,68,.25); }
    .result-title { font-size: 1.1rem; font-weight: 700; margin-bottom: 12px; }
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 14px; }
    .info-item label { font-size: .72rem; color: #8892a4; display: block; margin-bottom: 2px; }
    .info-item span  { font-size: .88rem; font-weight: 500; }
    .integrity-box {
        margin-top: 14px;
        padding: 12px 16px;
        border-radius: 10px;
        font-size: .82rem;
    }
    .integrity-ok    { background: rgba(79,124,255,.1); border: 1px solid rgba(79,124,255,.25); color: #4f7cff; }
    .integrity-warn  { background: rgba(245,158,11,.1); border: 1px solid rgba(245,158,11,.25); color: #f59e0b; }
    details summary  { cursor: pointer; font-size: .78rem; color: #8892a4; margin-top: 14px; }
    details pre      { margin-top: 8px; padding: 12px; background: #0f1117; border-radius: 8px; font-size: .72rem; overflow-x: auto; color: #8892a4; line-height: 1.6; }
</style>

<div class="upload-wrap">
    <div class="card">
        <p class="card-title">Upload Dokumen PDF untuk Diperiksa</p>

        <form action="{{ route('verifikasi.upload') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
            @csrf
            <div class="drop-area" id="dropArea">
                <input type="file" id="fileInput" name="file" accept=".pdf" required>
                <div class="drop-icon">📄</div>
                <div class="drop-title">Klik atau seret file ke sini</div>
                <div class="drop-hint">Format: PDF &nbsp;·&nbsp; Maksimal 5 MB</div>
            </div>
            <div class="file-preview" id="filePreview"></div>
            <button type="submit" class="btn-upload" id="submitBtn">
                <span id="btnText">🔍 Cek Tanda Tangan</span>
                <span class="spinner" id="spinner"></span>
            </button>
        </form>
    </div>

    @if(session('hasil'))
        @php $h = session('hasil'); @endphp
        <div class="result-box {{ $h['status'] === 'VALID' ? 'result-valid' : 'result-invalid' }}">
            <div class="result-title">
                @if($h['status'] === 'VALID')
                    ✅ Tanda Tangan <span style="color:#22c55e">VALID</span>
                @else
                    ❌ Tanda Tangan <span style="color:#ef4444">TIDAK VALID</span>
                @endif
            </div>
            <p style="font-size:.85rem;color:#8892a4">{{ $h['keterangan'] ?? '' }}</p>

            @if(isset($h['is_custom_rsa']) && $h['is_custom_rsa'])
                <!-- ── Jasa Izin Indonesia Custom RSA Verification Details ── -->
                <div class="info-grid" style="margin-top: 18px;">
                    <div class="info-item" style="grid-column: span 2;">
                        <label>Nomor ID Sertifikat</label>
                        <span style="font-weight: 700; color: #4f7cff;">{{ $h['nomor_id_sertifikat'] }}</span>
                    </div>
                    <div class="info-item">
                        <label>Nama PU (Pelaku Usaha)</label>
                        <span>{{ $h['nama_pu'] }}</span>
                    </div>
                    <div class="info-item">
                        <label>Nama Usaha</label>
                        <span>{{ $h['nama_usaha'] }}</span>
                    </div>
                    <div class="info-item" style="grid-column: span 2;">
                        <label>Alamat Lokasi Usaha</label>
                        <span style="line-height: 1.4; display: block; margin-top: 2px;">{{ $h['alamat_lokasi_usaha'] }}</span>
                    </div>
                    <div class="info-item">
                        <label>Tanggal Dikeluarkan Surat</label>
                        <span>{{ \Carbon\Carbon::parse($h['tanggal_dikeluarkan_surat'])->translatedFormat('d F Y') }}</span>
                    </div>
                    <div class="info-item">
                        <label>Jenis Legalitas Usaha</label>
                        <span>{{ $h['jenis_legalitas_usaha'] }}</span>
                    </div>
                    <div class="info-item">
                        <label>Penandatangan Resmi</label>
                        <span>{{ $h['penandatangan'] }}</span>
                    </div>
                    <div class="info-item">
                        <label>Jabatan</label>
                        <span>{{ $h['jabatan'] }}</span>
                    </div>
                </div>

                <div class="integrity-box {{ ($h['is_unmodified'] ?? false) ? 'integrity-ok' : 'integrity-warn' }}" style="margin-top: 18px;">
                    🛡️ <strong>Integritas Dokumen:</strong>
                    {{ $h['penjelasan_edit'] ?? '-' }}
                </div>

                <details style="margin-top: 18px;">
                    <summary>🔑 Lihat Kunci Publik RSA & Digital Signature Hash</summary>
                    <div style="margin-top: 12px; border-top: 1px solid var(--border); padding-top: 12px;">
                        <label style="font-size: .72rem; color: #8892a4; font-weight: 600; display: block; margin-bottom: 6px;">RSA PUBLIC KEY (PEM)</label>
                        <pre style="margin-top: 0; padding: 10px; background: #0f1117; border-radius: 6px; font-size: .68rem; overflow-x: auto; color: #8892a4; line-height: 1.4; font-family: monospace;">{{ $h['public_key'] }}</pre>

                        <label style="font-size: .72rem; color: #8892a4; font-weight: 600; display: block; margin-bottom: 6px; margin-top: 12px;">DIGITAL SIGNATURE HASH (Base64)</label>
                        <pre style="margin-top: 0; padding: 10px; background: #0f1117; border-radius: 6px; font-size: .68rem; overflow-x: auto; color: #8892a4; line-height: 1.4; font-family: monospace; white-space: pre-wrap; word-break: break-all;">{{ $h['signature'] }}</pre>
                    </div>
                </details>

            @else
                <!-- ── Standard Adobe PKCS7 Verification Details ── -->
                @if($h['status'] === 'VALID')
                    <div class="info-grid">
                        <div class="info-item">
                            <label>Penandatangan</label>
                            <span>{{ $h['nama'] ?? '-' }}</span>
                        </div>
                        <div class="info-item">
                            <label>Instansi</label>
                            <span>{{ $h['instansi'] ?? '-' }}</span>
                        </div>
                        <div class="info-item">
                            <label>Waktu Tanda Tangan</label>
                            <span>{{ $h['waktu'] ?? '-' }}</span>
                        </div>
                        <div class="info-item">
                            <label>Status</label>
                            <span class="badge badge-green">Terverifikasi</span>
                        </div>
                    </div>

                    <div class="integrity-box {{ ($h['is_unmodified'] ?? false) ? 'integrity-ok' : 'integrity-warn' }}">
                        🛡️ <strong>Integritas Dokumen:</strong>
                        {{ $h['penjelasan_edit'] ?? '-' }}
                    </div>

                    @if(!empty($h['detail']))
                        <details>
                            <summary>Lihat Detail Sertifikat</summary>
                            <pre>{{ $h['detail'] }}</pre>
                        </details>
                    @endif
                @else
                    @if(!empty($h['detail']))
                        <pre style="margin-top:14px;padding:12px;background:#0f1117;border-radius:8px;font-size:.72rem;color:#ef4444;overflow-x:auto">{{ $h['detail'] }}</pre>
                    @endif
                @endif
            @endif
        </div>
    @endif
</div>

<script>
const input    = document.getElementById('fileInput');
const preview  = document.getElementById('filePreview');
const dropArea = document.getElementById('dropArea');
const form     = document.getElementById('uploadForm');
const spinner  = document.getElementById('spinner');
const btnText  = document.getElementById('btnText');

input.addEventListener('change', function () {
    if (this.files.length > 0) {
        preview.style.display = 'block';
        preview.textContent = '📎 ' + this.files[0].name;
    }
});

['dragover','dragleave','drop'].forEach(evt => {
    dropArea.addEventListener(evt, e => {
        e.preventDefault();
        if (evt === 'dragover')  dropArea.classList.add('dragover');
        if (evt === 'dragleave') dropArea.classList.remove('dragover');
        if (evt === 'drop') {
            dropArea.classList.remove('dragover');
            input.files = e.dataTransfer.files;
            if (input.files.length > 0) {
                preview.style.display = 'block';
                preview.textContent = '📎 ' + input.files[0].name;
            }
        }
    });
});

form.addEventListener('submit', function () {
    spinner.style.display = 'block';
    btnText.textContent = 'Memproses...';
});
</script>
@endsection

@extends('layouts.app')
@section('title', 'TTE Berhasil')
@section('page-title', 'Tanda Tangan Digital Berhasil')

@section('content')
<style>
    .success-card {
        max-width: 800px;
        margin: 0 auto;
    }

    .success-header {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        margin-bottom: 28px;
        padding-bottom: 24px;
        border-bottom: 1px solid var(--border);
    }

    .success-icon {
        font-size: 3.5rem;
        margin-bottom: 12px;
        display: inline-block;
        animation: pulse 2s infinite;
        filter: drop-shadow(0 0 15px rgba(34, 197, 94, 0.4));
    }

    .success-title {
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--green);
        margin-bottom: 6px;
    }

    .success-subtitle {
        font-size: 0.875rem;
        color: var(--muted);
        max-width: 480px;
    }

    .cert-preview-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin-bottom: 28px;
        background: var(--surface2);
        padding: 20px;
        border-radius: 12px;
        border: 1px solid var(--border);
    }

    .preview-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .preview-label {
        font-size: 0.72rem;
        text-transform: uppercase;
        color: var(--muted);
        letter-spacing: 0.05em;
    }

    .preview-value {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text);
    }

    .crypto-block {
        margin-bottom: 20px;
    }

    .crypto-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--muted);
        margin-bottom: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .crypto-textarea {
        width: 100%;
        font-family: 'Courier New', Courier, monospace;
        font-size: 0.75rem;
        background: #090a0f;
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 12px;
        color: var(--muted);
        resize: vertical;
        line-height: 1.4;
        transition: color 0.2s, border-color 0.2s;
    }

    .crypto-textarea:focus {
        border-color: var(--accent);
        color: var(--text);
        outline: none;
    }

    .copy-btn {
        background: rgba(79, 124, 255, 0.12);
        color: var(--accent);
        border: 1px solid rgba(79, 124, 255, 0.25);
        padding: 2px 10px;
        border-radius: 4px;
        font-size: 0.7rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .copy-btn:hover {
        background: var(--accent);
        color: #fff;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.06); }
    }
</style>

<div class="success-card">
    <div class="card">
        <div class="success-header">
            <span class="success-icon">🔒</span>
            <h2 class="success-title">Dokumen Berhasil Ditandatangani Digital!</h2>
            <p class="success-subtitle">
                Sertifikat telah dienkripsi secara digital menggunakan algoritma <strong>RSA-SHA256</strong>. Kunci kriptografi dan TTE aktif telah disimpan dalam database.
            </p>
        </div>

        <div class="cert-preview-grid">
            <div class="preview-item">
                <span class="preview-label">Nomor Sertifikat</span>
                <span class="preview-value" style="color: var(--accent)">{{ $sertifikat->nomor_id_sertifikat }}</span>
            </div>
            <div class="preview-item">
                <span class="preview-label">Pelaku Usaha (PU)</span>
                <span class="preview-value">{{ $sertifikat->nama_pu }}</span>
            </div>
            <div class="preview-item">
                <span class="preview-label">Nama Usaha</span>
                <span class="preview-value">{{ $sertifikat->nama_usaha }}</span>
            </div>
            <div class="preview-item">
                <span class="preview-label">Jenis Legalitas</span>
                <span class="preview-value">{{ $sertifikat->jenis_legalitas_usaha }}</span>
            </div>
        </div>

        <!-- RSA Signature Block -->
        <div class="crypto-block">
            <div class="crypto-label">
                <span>🔏 HASIL RSA DIGITAL SIGNATURE (Base64)</span>
                <button type="button" class="copy-btn" onclick="copyText('signatureText')">Salin</button>
            </div>
            <textarea id="signatureText" class="crypto-textarea" rows="4" readonly>{{ $sertifikat->signature }}</textarea>
        </div>

        <!-- RSA Public Key Block -->
        <div class="crypto-block">
            <div class="crypto-label">
                <span>🔑 PUBLIC KEY (Untuk Verifikasi Keabsahan)</span>
                <button type="button" class="copy-btn" onclick="copyText('publicKeyText')">Salin</button>
            </div>
            <textarea id="publicKeyText" class="crypto-textarea" rows="5" readonly>{{ $sertifikat->public_key }}</textarea>
        </div>

        <!-- RSA Private Key Block -->
        <div class="crypto-block">
            <div class="crypto-label">
                <span>🔒 PRIVATE KEY (Rahasia - Digunakan Untuk Enkripsi)</span>
                <button type="button" class="copy-btn" onclick="copyText('privateKeyText')">Salin</button>
            </div>
            <textarea id="privateKeyText" class="crypto-textarea" rows="5" readonly>{{ $sertifikat->private_key }}</textarea>
        </div>

        <!-- Button Actions -->
        <div style="display: flex; gap: 12px; margin-top: 32px; border-top: 1px solid var(--border); padding-top: 24px; justify-content: center; flex-wrap: wrap;">
            <a href="{{ route('sertifikat.download', $sertifikat->id) }}" class="btn btn-primary" target="_blank" style="padding: 12px 28px; font-weight: 600; font-size: 0.95rem; box-shadow: 0 4px 14px rgba(79, 124, 255, 0.4);">
                📥 Unduh / Tampilkan PDF TTE
            </a>
            <a href="{{ route('sertifikat.index') }}" class="btn btn-secondary" style="padding: 12px 24px;">
                Kembali ke Daftar
            </a>
        </div>
    </div>
</div>

<script>
    function copyText(elementId) {
        const copyText = document.getElementById(elementId);
        copyText.select();
        copyText.setSelectionRange(0, 99999); /* For mobile devices */
        navigator.clipboard.writeText(copyText.value)
            .then(() => {
                alert("Berhasil disalin ke clipboard!");
            })
            .catch(() => {
                alert("Gagal menyalin teks.");
            });
    }
</script>
@endsection

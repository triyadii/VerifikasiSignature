<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Tanda Tangan Digital (RSA) — Jasa Izin Verified</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:        #0f1117;
            --surface:   #1a1d27;
            --surface2:  #22263a;
            --border:    #2e3348;
            --accent:    #4f7cff;
            --green:     #22c55e;
            --red:       #ef4444;
            --text:      #e2e8f0;
            --muted:     #8892a4;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .verify-container {
            width: 100%;
            max-width: 680px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 20px 30px rgba(0, 0, 0, 0.4);
        }

        .header {
            text-align: center;
            margin-bottom: 28px;
        }

        .header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .header p {
            font-size: 0.85rem;
            color: var(--muted);
        }

        /* ── RESULT BANNER ── */
        .result-banner {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 28px;
            border: 1px solid transparent;
        }

        .result-banner.valid {
            background: rgba(34, 197, 94, 0.12);
            border-color: rgba(34, 197, 94, 0.3);
            color: var(--green);
        }

        .result-banner.invalid {
            background: rgba(239, 68, 68, 0.12);
            border-color: rgba(239, 68, 68, 0.3);
            color: var(--red);
        }

        .result-icon {
            font-size: 2.5rem;
            flex-shrink: 0;
            filter: drop-shadow(0 0 10px currentColor);
        }

        .result-content h2 {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .result-content p {
            font-size: 0.85rem;
            opacity: 0.9;
        }

        /* ── DATA SECTION ── */
        .section-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--muted);
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--border);
            padding-bottom: 6px;
        }

        .data-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }

        .data-item {
            background: var(--surface2);
            padding: 14px 16px;
            border-radius: 8px;
            border: 1px solid rgba(46, 51, 72, 0.5);
        }

        .data-label {
            font-size: 0.72rem;
            color: var(--muted);
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .data-value {
            font-size: 0.9rem;
            font-weight: 600;
        }

        /* ── CRYPTO TECH DETAILS ── */
        .crypto-details {
            margin-top: 24px;
        }

        .crypto-textarea {
            width: 100%;
            font-family: 'Courier New', Courier, monospace;
            font-size: 0.7rem;
            background: #090a0f;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 10px;
            color: var(--muted);
            resize: vertical;
            line-height: 1.3;
            margin-bottom: 16px;
        }

        .footer {
            text-align: center;
            margin-top: 32px;
            font-size: 0.8rem;
            color: var(--muted);
            border-top: 1px solid var(--border);
            padding-top: 20px;
        }
    </style>
</head>
<body>

<div class="verify-container">
    <div class="header">
        <img src="{{ asset('logo.png') }}" style="width: 80px; height: auto; margin-bottom: 12px; filter: drop-shadow(0 0 10px rgba(30,58,138,0.2));" alt="Logo PT Jasa Izin Indonesia">
        <h1 style="color: #4f7cff; font-weight: 800; text-transform: uppercase; font-size: 1.4rem;">PT. JASA IZIN INDONESIA</h1>
        <p style="font-size: 0.85rem; color: var(--muted); margin-top: 4px;">Portal Verifikasi Tanda Tangan Elektronik Resmi</p>
    </div>

    @if($isValid)
        <!-- Valid Banner -->
        <div class="result-banner valid">
            <span class="result-icon">🛡️</span>
            <div class="result-content">
                <h2>Tanda Tangan Digital VALID</h2>
                <p>Dokumen ini ditandatangani secara resmi oleh PT Jasa Izin Indonesia menggunakan Kriptografi Kunci Asimetris RSA-256 dengan penandatangan utama <strong>GOM GOM SIDABUTAR</strong>. Keaslian dokumen terjamin 100%.</p>
            </div>
        </div>
    @else
        <!-- Invalid Banner -->
        <div class="result-banner invalid">
            <span class="result-icon">⚠️</span>
            <div class="result-content">
                <h2>Tanda Tangan Digital TIDAK VALID</h2>
                <p>Dokumen ini tidak terverifikasi secara digital. Terjadi kegagalan validasi sidik kriptografi asimetris. Dokumen ini mungkin telah diubah secara ilegal atau tidak dikeluarkan oleh PT Jasa Izin Indonesia.</p>
            </div>
        </div>
    @endif

    <!-- Informasi Sertifikat -->
    <div class="section-title">📄 Data Resmi Sertifikat</div>
    <div class="data-grid">
        <div class="data-item">
            <div class="data-label">Nomor Sertifikat</div>
            <div class="data-value" style="color: var(--accent);">{{ $sertifikat->nomor_id_sertifikat }}</div>
        </div>
        <div class="data-item">
            <div class="data-label">Nama Pelaku Usaha (PU)</div>
            <div class="data-value">{{ $sertifikat->nama_pu }}</div>
        </div>
        <div class="data-item">
            <div class="data-label">Nama Usaha</div>
            <div class="data-value">{{ $sertifikat->nama_usaha }}</div>
        </div>
        <div class="data-item">
            <div class="data-label">Jenis Legalitas</div>
            <div class="data-value">{{ $sertifikat->jenis_legalitas_usaha }}</div>
        </div>
        <div class="data-item" style="grid-column: span 2;">
            <div class="data-label">Alamat Usaha</div>
            <div class="data-value" style="font-weight: 400; line-height: 1.4;">{{ $sertifikat->alamat_lokasi_usaha }}</div>
        </div>
        <div class="data-item">
            <div class="data-label">Tanggal Dikeluarkan</div>
            <div class="data-value">{{ \Carbon\Carbon::parse($sertifikat->tanggal_dikeluarkan_surat)->translatedFormat('d F Y') }}</div>
        </div>
        <div class="data-item">
            <div class="data-label">Penandatangan Resmi</div>
            <div class="data-value" style="color: var(--accent);">GOM GOM SIDABUTAR</div>
        </div>
        <div class="data-item" style="grid-column: span 2;">
            <div class="data-label">Status Integritas Dokumen</div>
            <div class="data-value" style="color: {{ $isValid ? 'var(--green)' : 'var(--red)' }}">
                {{ $isValid ? '✅ Cocok, Sah & Belum Pernah Dimodifikasi' : '❌ Rusak, Palsu atau Tidak Cocok' }}
            </div>
        </div>
    </div>

    <!-- Cryptographic Proof Panel -->
    <div class="crypto-details">
        <div class="section-title">🔑 Bukti Kriptografi (Asymmetric RSA-256 Proof)</div>
        
        <label style="font-size: 0.75rem; color: var(--muted); display: block; margin-bottom: 6px; font-weight: 600;">RSA PUBLIC KEY</label>
        <textarea class="crypto-textarea" rows="4" readonly>{{ $sertifikat->public_key }}</textarea>

        <label style="font-size: 0.75rem; color: var(--muted); display: block; margin-bottom: 6px; font-weight: 600;">DIGITAL SIGNATURE HASH (Base64)</label>
        <textarea class="crypto-textarea" rows="3" readonly>{{ $sertifikat->signature }}</textarea>
    </div>

    <div class="footer">
        <p>&copy; 2026 Jasa Izin Verified. Semua hak dilindungi undang-undang.</p>
        <p style="font-size: 0.7rem; margin-top: 4px; color: var(--muted);">Portal verifikasi ini dijamin keamanannya oleh enkripsi asimetris RSA-SHA256.</p>
    </div>
</div>

</body>
</html>

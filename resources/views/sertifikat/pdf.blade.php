<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Pernyataan Keaslian Sertifikat — {{ $sertifikat->nomor_id_sertifikat }}</title>
    <style>
        @page {
            margin: 1cm 1.5cm 1cm 1.5cm;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000000;
            margin: 0;
            padding: 0;
            font-size: 11pt;
            line-height: 1.35;
        }

        /* ── HEADER ── */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 3px double #1e3a8a;
            padding-bottom: 5px;
            margin-bottom: 12px;
        }

        .header-logo {
            width: 65px;
            vertical-align: middle;
        }

        .header-logo img {
            width: 65px;
            height: auto;
            display: block;
        }

        .header-text {
            vertical-align: middle;
            padding-left: 12px;
        }

        .header-text h1 {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0;
            font-size: 17pt;
            font-weight: bold;
            color: #1e3a8a;
            letter-spacing: 0.5px;
        }

        .header-text p {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 1px 0 0 0;
            font-size: 9pt;
            color: #000000;
            font-style: italic;
            letter-spacing: 0.5px;
        }

        /* ── TITLE ── */
        .title-section {
            text-align: center;
            margin-bottom: 12px;
        }

        .title-section h2 {
            font-size: 12.5pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 0;
            text-transform: uppercase;
        }

        .title-section .doc-num {
            font-size: 11pt;
            margin-top: 1px;
        }

        /* ── CONTENT ── */
        .content-paragraph {
            text-align: justify;
            text-indent: 0;
            margin-bottom: 8px;
        }

        /* ── SMALL INFO TABLE ── */
        .info-table {
            width: 100%;
            margin-left: 0px;
            margin-bottom: 8px;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 1px 0;
            vertical-align: top;
        }

        .info-table td.label {
            width: 140px;
        }

        .info-table td.colon {
            width: 15px;
            text-align: center;
        }

        /* ── DETAIL DATA TABLE ── */
        .detail-table {
            width: 100%;
            margin: 10px auto;
            border-collapse: collapse;
        }

        .detail-table th, .detail-table td {
            border: 1px solid #000000;
            padding: 5px 8px;
            text-align: left;
            font-size: 10pt;
        }

        .detail-table td.label {
            width: 190px;
            font-weight: bold;
            background-color: #f7f9fa;
        }

        .detail-table td.colon {
            width: 15px;
            text-align: center;
            background-color: #f7f9fa;
        }

        /* ── SIGNATURE / FOOTER ── */
        .footer-section {
            width: 100%;
            margin-top: 15px;
            border-collapse: collapse;
        }

        .footer-left {
            width: 55%;
            vertical-align: top;
        }

        .footer-right {
            width: 45%;
            text-align: center;
            vertical-align: top;
        }

        .sig-place-date {
            margin-bottom: 2px;
        }

        .sig-relation {
            margin-bottom: 4px;
        }

        .qr-wrapper {
            margin: 6px auto;
            width: 80px;
            height: 80px;
            border: 1px solid #000000;
            padding: 3px;
            background: #ffffff;
            display: inline-block;
        }

        .qr-wrapper img {
            width: 100%;
            height: 100%;
            display: block;
        }

        .sig-name {
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            margin-top: 3px;
            font-size: 11pt;
        }

        .sig-role {
            font-size: 11pt;
            margin-top: 1px;
        }
    </style>
</head>
<body>

    <!-- Header / Kop Surat -->
    <table class="header-table">
        <tr>
            <td class="header-logo">
                @if($logoData)
                    <img src="{{ $logoData }}" alt="Logo PT Jasa Izin Indonesia">
                @endif
            </td>
            <td class="header-text">
                <h1>PT. JASA IZIN INDONESIA</h1>
                <p>your best solution permit</p>
            </td>
        </tr>
    </table>

    <!-- Judul Surat -->
    <div class="title-section">
        <h2>SURAT PERNYATAAN</h2>
        <div class="doc-num">Nomor: {{ $sertifikat->nomor_id_sertifikat }}</div>
    </div>

    <!-- Identitas Pihak Pertama -->
    <p class="content-paragraph">Yang bertanda tangan di bawah ini:</p>
    <table class="info-table">
        <tr>
            <td class="label">Nama Perusahaan</td>
            <td class="colon">:</td>
            <td><strong>PT Jasa Izin Indonesia</strong></td>
        </tr>
        <tr>
            <td class="label">Alamat</td>
            <td class="colon">:</td>
            <td>Jln. Lintas Medan Lubuk Pakam Deli Serdang</td>
        </tr>
    </table>

    <!-- Isi Pernyataan 1 -->
    <p class="content-paragraph" style="text-align: justify;">
        Dengan ini menyatakan bahwa dokumen surat izin usaha berupa sertifikat yang telah diterbitkan oleh badan hukum, lembaga, maupun instansi terkait melalui proses pengurusan oleh PT Jasa Izin Indonesia adalah dokumen yang sah, resmi, dan sesuai dengan ketentuan peraturan perundang-undangan yang berlaku di Negara Republik Indonesia.
    </p>

    <!-- Rincian Dokumen -->
    <p class="content-paragraph">Adapun detail dokumen yang dimaksud adalah sebagai berikut:</p>
    <table class="detail-table">
        <tr>
            <td class="label">Nomor ID Sertifikat</td>
            <td class="colon">:</td>
            <td>{{ $sertifikat->nomor_id_sertifikat }}</td>
        </tr>
        <tr>
            <td class="label">Nama PU</td>
            <td class="colon">:</td>
            <td>{{ $sertifikat->nama_pu }}</td>
        </tr>
        <tr>
            <td class="label">Nama Usaha</td>
            <td class="colon">:</td>
            <td>{{ $sertifikat->nama_usaha }}</td>
        </tr>
        <tr>
            <td class="label">Alamat Lokasi Usaha</td>
            <td class="colon">:</td>
            <td>{{ $sertifikat->alamat_lokasi_usaha }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Dikeluarkan Surat</td>
            <td class="colon">:</td>
            <td>{{ \Carbon\Carbon::parse($sertifikat->tanggal_dikeluarkan_surat)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td class="label">Jenis Legalitas Usaha</td>
            <td class="colon">:</td>
            <td>{{ $sertifikat->jenis_legalitas_usaha }}</td>
        </tr>
    </table>

    <!-- Pernyataan Tambahan -->
    <p class="content-paragraph" style="text-align: justify;">
        PT Jasa Izin Indonesia memastikan bahwa dokumen tersebut telah melalui proses verifikasi, validasi, dan prosedur administrasi sesuai ketentuan instansi penerbit terkait.
    </p>
    <p class="content-paragraph" style="text-align: justify;">
        Apabila di kemudian hari ditemukan adanya penyalahgunaan, pemalsuan, perubahan data, maupun penggunaan dokumen di luar tanggung jawab dan kewenangan PT Jasa Izin Indonesia setelah dokumen diserahkan kepada pihak penerima, maka hal tersebut sepenuhnya menjadi tanggung jawab pihak yang melakukan penyalahgunaan tersebut.
    </p>
    <p class="content-paragraph" style="text-align: justify;">
        Demikian surat pernyataan ini dibuat dengan sebenar-benarnya agar dapat dipergunakan sebagaimana mestinya.
    </p>

    <!-- Tanda Tangan Seksi -->
    <table class="footer-section">
        <tr>
            <td class="footer-left">
                <!-- Spasi kosong kiri -->
            </td>
            <td class="footer-right">
                <div class="sig-place-date">
                    Lubuk Pakam, {{ \Carbon\Carbon::parse($sertifikat->tanggal_dikeluarkan_surat)->translatedFormat('d F Y') }}
                </div>
                <div class="sig-relation">
                    Hormat kami,<br>
                    <strong>PT Jasa Izin Indonesia</strong>
                </div>
                <div class="qr-wrapper" title="Tanda Tangan Elektronik Terverifikasi">
                    <img src="{{ $qrCodeData }}" alt="TTE QR Code">
                </div>
                <div class="sig-name">
                    GOM GOM SIDABUTAR
                </div>
                <div class="sig-role">
                    Direktur
                </div>
            </td>
        </tr>
    </table>

</body>
</html>

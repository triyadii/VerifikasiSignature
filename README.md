# Verifikasi Signature — Sistem Verifikasi Tanda Tangan Elektronik (TTE)

Aplikasi berbasis web (Laravel) untuk **menerbitkan dan memverifikasi sertifikat digital** beserta tanda tangan elektroniknya. Aplikasi menerbitkan sertifikat dalam bentuk PDF yang ditandatangani secara digital, lalu menyediakan halaman verifikasi publik untuk memastikan keaslian dan keutuhan dokumen tersebut.

Aplikasi mendukung dua mekanisme verifikasi:

- **RSA-SHA256 (custom)** — tanda tangan dibuat dari data sertifikat menggunakan kunci RSA dan diverifikasi dengan public key yang tersimpan di sistem.
- **PKCS7 (standar PDF)** — verifikasi tanda tangan digital PDF standar (Adobe/OpenSSL) sebagai jalur cadangan.

---

## Fitur

### Manajemen Sertifikat
- Tambah, ubah, dan hapus data sertifikat (nomor ID sertifikat, nama PU, nama usaha, alamat lokasi usaha, tanggal dikeluarkan, dan jenis legalitas usaha).
- Daftar sertifikat dengan **pencarian** (berdasarkan nomor ID, nama PU, nama usaha, atau jenis legalitas) dan **pagination**.
- Saat data sertifikat diubah, tanda tangan lama otomatis diinvalidasi untuk menjaga integritas data.

### Tanda Tangan Digital
- Pembuatan pasangan kunci **RSA 2048-bit** secara otomatis.
- Penandatanganan data sertifikat menggunakan algoritma **SHA-256**.
- Penyimpanan public key dan private key yang terkait dengan setiap sertifikat.

### Generate PDF Sertifikat
- Render sertifikat ke **PDF** (format A4 portrait) menggunakan DomPDF.
- Penyisipan **QR Code** yang mengarah ke halaman verifikasi sertifikat.
- Penyisipan **logo** instansi pada dokumen.
- Penyimpanan dan **unduh** file PDF sertifikat.

### Verifikasi Publik (tanpa login)
- **Unggah PDF** untuk diverifikasi langsung tanpa perlu masuk ke sistem.
- Dua jalur verifikasi: **RSA custom** (mencocokkan nomor sertifikat dan memverifikasi signature dengan public key) dan **PKCS7** (via OpenSSL).
- **Ekstraksi teks PDF** dari stream terkompresi maupun tidak terkompresi.
- **Deteksi modifikasi** dokumen setelah ditandatangani melalui analisis ByteRange.
- Hasil verifikasi yang detail: status, identitas penandatangan, waktu tanda tangan, serta peringatan bila dokumen telah diubah setelah ditandatangani.

### Dashboard Admin
- Statistik ringkas: total verifikasi, jumlah dokumen valid, jumlah dokumen tidak valid, dan total pengguna.
- Daftar log verifikasi terbaru.

### Log Audit Verifikasi
- Pencatatan setiap aktivitas verifikasi: nama file, status (VALID / TIDAK VALID / ERROR), nama penandatangan, instansi, waktu, alamat IP, dan keterangan.
- Log dapat **difilter dan dicari**.

### Manajemen Pengguna & Hak Akses
- Manajemen pengguna (tambah, ubah, hapus) dengan **role admin dan user**.
- Login dengan opsi **remember-me**.
- Proteksi akses halaman melalui middleware autentikasi (`auth`) dan otorisasi admin (`admin`).

---

## Lisensi

Aplikasi ini dirilis di bawah **MIT License**, sesuai deklarasi lisensi pada `composer.json`. Anda bebas menggunakan, menyalin, memodifikasi, dan mendistribusikan perangkat lunak ini sesuai ketentuan MIT License.

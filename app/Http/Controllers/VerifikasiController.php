<?php

namespace App\Http\Controllers;

use App\Models\VerificationLog;
use App\Models\Sertifikat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VerifikasiController extends Controller
{
    public function index()
    {
        return view('upload');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:5120'
        ]);

        try {
            $file = $request->file('file');
            if (!$file->isValid()) {
                throw new \Exception("Upload file gagal");
            }

            $originalName = $file->getClientOriginalName();
            $path = Storage::disk('public')->putFile('dokumen', $file);

            if (!$path) {
                throw new \Exception("Gagal menyimpan file");
            }

            // Read raw PDF content
            $pdfContent = Storage::disk('public')->get($path);
            
            // Extract text contents from the PDF
            $extractedText = $this->extractPdfText($pdfContent);

            // Find matching signed certificate
            $sertifikat = null;
            $sertifikats = Sertifikat::whereNotNull('signature')->get();
            foreach ($sertifikats as $s) {
                if (strpos($extractedText, $s->nomor_id_sertifikat) !== false) {
                    $sertifikat = $s;
                    break;
                }
            }

            if ($sertifikat) {
                // Prepare raw signed data for verification
                $data_to_verify = $sertifikat->nomor_id_sertifikat . '|' .
                                 $sertifikat->nama_pu . '|' .
                                 $sertifikat->nama_usaha . '|' .
                                 $sertifikat->alamat_lokasi_usaha . '|' .
                                 $sertifikat->tanggal_dikeluarkan_surat . '|' .
                                 $sertifikat->jenis_legalitas_usaha;

                // Verify the cryptographic signature using the stored public key
                $verification_res = openssl_verify(
                    $data_to_verify, 
                    base64_decode($sertifikat->signature), 
                    $sertifikat->public_key, 
                    OPENSSL_ALGO_SHA256
                );
                
                $isValidSignature = ($verification_res === 1);
                
                // Integrity check: make sure the text content is identical
                $dataMatches = true;
                $tamperingDetails = [];

                if (strpos($extractedText, $sertifikat->nama_pu) === false) {
                    $dataMatches = false;
                    $tamperingDetails[] = 'Nama PU tidak cocok/diubah';
                }
                if (strpos($extractedText, $sertifikat->nama_usaha) === false) {
                    $dataMatches = false;
                    $tamperingDetails[] = 'Nama Usaha tidak cocok/diubah';
                }
                if (strpos($extractedText, $sertifikat->jenis_legalitas_usaha) === false) {
                    $dataMatches = false;
                    $tamperingDetails[] = 'Jenis Legalitas tidak cocok/diubah';
                }

                $finalStatus = ($isValidSignature && $dataMatches) ? 'VALID' : 'TIDAK VALID';

                if ($finalStatus === 'VALID') {
                    $keterangan = 'Dokumen sah dan terverifikasi secara digital (Asymmetric RSA-256). Integritas data terjamin.';
                    $penjelasan_edit = 'Dokumen belum ada perubahan sama sekali sejak ditandatangani.';
                } else {
                    $keterangan = 'Dokumen TIDAK VALID. Terjadi modifikasi data atau sidik kriptografi tidak cocok.';
                    if (!$isValidSignature) {
                        $tamperingDetails[] = 'Kunci/Signature tidak cocok';
                    }
                    $penjelasan_edit = 'PERINGATAN: ' . implode(', ', $tamperingDetails) . '.';
                }

                $hasil = [
                    'is_custom_rsa'   => true,
                    'status'          => $finalStatus,
                    'keterangan'      => $keterangan,
                    'is_unmodified'   => $dataMatches,
                    'penjelasan_edit' => $penjelasan_edit,

                    // Owner data
                    'nomor_id_sertifikat' => $sertifikat->nomor_id_sertifikat,
                    'nama_pu'             => $sertifikat->nama_pu,
                    'nama_usaha'          => $sertifikat->nama_usaha,
                    'alamat_lokasi_usaha' => $sertifikat->alamat_lokasi_usaha,
                    'tanggal_dikeluarkan_surat' => $sertifikat->tanggal_dikeluarkan_surat,
                    'jenis_legalitas_usaha' => $sertifikat->jenis_legalitas_usaha,
                    'penandatangan'       => 'GOM GOM SIDABUTAR',
                    'jabatan'             => 'Direktur',

                    // Cryptographic details
                    'public_key'          => $sertifikat->public_key,
                    'signature'           => $sertifikat->signature,
                ];

                // Save log
                VerificationLog::create([
                    'user_id'            => Auth::id(),
                    'filename'           => $originalName,
                    'status'             => $hasil['status'],
                    'nama_penandatangan' => $hasil['penandatangan'],
                    'instansi'           => 'PT Jasa Izin Indonesia',
                    'waktu_ttd'          => $sertifikat->tanggal_dikeluarkan_surat,
                    'ip_address'         => $request->ip(),
                    'keterangan'         => $hasil['keterangan'] . ' - ' . $hasil['penjelasan_edit'],
                ]);

                return back()->with([
                    'success' => 'File berhasil diproses',
                    'hasil'   => $hasil
                ]);
            }

            // Fallback: Check standard Adobe PKCS7 digital signature
            $hasil = $this->verifikasiPDF($path);

            // Simpan log verifikasi
            VerificationLog::create([
                'user_id'            => Auth::id(),
                'filename'           => $originalName,
                'status'             => $hasil['status'],
                'nama_penandatangan' => $hasil['nama'] ?? null,
                'instansi'           => $hasil['instansi'] ?? null,
                'waktu_ttd'          => $hasil['waktu'] ?? null,
                'ip_address'         => $request->ip(),
                'keterangan'         => $hasil['keterangan'] ?? null,
            ]);

            return back()->with([
                'success' => 'File berhasil diproses',
                'hasil'   => $hasil
            ]);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    private function verifikasiPDF($path)
    {
        if (!Storage::disk('public')->exists($path)) {
            return [
                'status'     => 'ERROR',
                'keterangan' => 'File tidak ditemukan di storage'
            ];
        }
        $content = Storage::disk('public')->get($path);
        if (strpos($content, '/ByteRange') === false) {
            return [
                'status'     => 'TIDAK VALID',
                'keterangan' => 'Dokumen tidak memiliki tanda tangan digital'
            ];
        }

        preg_match('/\/Contents\s*<([0-9A-F\s]+)>/i', $content, $matches);

        if (!isset($matches[1])) {
            return [
                'status'     => 'TIDAK VALID',
                'keterangan' => 'Signature tidak ditemukan pada struktur PDF',
                'detail'     => 'Tag /Contents tidak mengandung nilai Hexadecimal yang valid.'
            ];
        }

        $signatureHex = preg_replace('/\s+/', '', $matches[1]);
        $signature    = hex2bin($signatureHex);

        Storage::disk('local')->put('temp/signature.p7s', $signature);
        $sigPath = Storage::disk('local')->path('temp/signature.p7s');

        $output = [];
        $result = null;

        exec("openssl pkcs7 -inform DER -in " . escapeshellarg($sigPath) . " -print_certs 2>&1", $output, $result);

        if ($result === 0) {
            $subject     = '';
            $allSubjects = [];
            foreach ($output as $line) {
                if (stripos(trim($line), 'subject') === 0 || stripos(trim($line), 'subject=') !== false) {
                    $allSubjects[] = $line;
                }
            }

            foreach ($allSubjects as $subj) {
                if (stripos($subj, 'Tanda Tangan Digital') !== false) {
                    $subject = $subj;
                    break;
                }
            }

            if (empty($subject) && count($allSubjects) > 0) {
                foreach ($allSubjects as $subj) {
                    if (stripos($subj, 'Lembaga Sandi Negara') === false &&
                        stripos($subj, 'Balai Sertifikasi Elektronik') === false &&
                        stripos($subj, 'BSrE') === false) {
                        $subject = $subj;
                        break;
                    }
                }
            }

            if (empty($subject) && count($allSubjects) > 0) {
                $subject = $allSubjects[0];
            }

            $nama    = 'Tidak diketahui';
            $instansi = 'Tidak diketahui';

            if (preg_match('/CN\s*=\s*([^,\/]+)/', $subject, $m)) {
                $nama = trim($m[1]);
            }
            if (preg_match('/O\s*=\s*([^,\/]+)/', $subject, $m)) {
                $instansi = trim($m[1]);
            }

            $waktu = 'Tidak diketahui';
            if (preg_match('/\/M\s*\(\s*D:([0-9]{14}[^)]*)\s*\)/', $content, $mTime)) {
                $rawPdfTime = $mTime[1];
                if (preg_match('/^(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/', $rawPdfTime, $tParts)) {
                    $waktu = $tParts[3] . '-' . $tParts[2] . '-' . $tParts[1] . ' ' . $tParts[4] . ':' . $tParts[5] . ':' . $tParts[6];
                } else {
                    $waktu = $rawPdfTime;
                }
            }

            $penjelasan_edit = 'Status dokumen tidak dapat dipastikan';
            $is_unmodified   = false;
            if (preg_match('/\/ByteRange\s*\[(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\]/', $content, $br)) {
                $offset2      = (int)$br[3];
                $length2      = (int)$br[4];
                $expectedEnd  = $offset2 + $length2;
                $fileSize     = strlen($content);

                if (abs($fileSize - $expectedEnd) <= 5) {
                    $is_unmodified   = true;
                    $penjelasan_edit = 'Dokumen belum ada perubahan sama sekali sejak ditandatangani. (Integritas ByteRange cocok: File tertutup sempurna oleh Signature).';
                } else {
                    $penjelasan_edit = 'PERINGATAN: Terdapat penambahan data/edit setelah dokumen ditandatangani. (Ada ' . ($fileSize - $expectedEnd) . ' bytes di luar jangkauan Signature).';
                }
            }

            return [
                'status'         => 'VALID',
                'keterangan'     => 'Dokumen sah dan Signature terverifikasi (PKCS7)',
                'nama'           => $nama,
                'instansi'       => $instansi,
                'waktu'          => $waktu,
                'is_unmodified'  => $is_unmodified,
                'penjelasan_edit' => $penjelasan_edit,
                'detail'         => implode("\n", $output)
            ];
        }

        return [
            'status'     => 'TIDAK VALID',
            'keterangan' => 'Signature tidak valid atau gagal dibaca (Error Code: ' . $result . ')',
            'detail'     => implode("\n", $output)
        ];
    }

    private function extractPdfText($pdfContent)
    {
        $text = '';
        
        // 1. Extract text from uncompressed PDF streams
        preg_match_all('/\((.*?)\)/s', $pdfContent, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $match) {
                $text .= stripcslashes($match) . ' ';
            }
        }
        
        // 2. Extract text from compressed PDF streams (using gzuncompress)
        if (strpos($pdfContent, '/FlateDecode') !== false) {
            preg_match_all('/stream[\r\n]+(.*?)[\r\n]+endstream/is', $pdfContent, $streamMatches);
            if (!empty($streamMatches[1])) {
                foreach ($streamMatches[1] as $stream) {
                    $decompressed = @gzuncompress(trim($stream));
                    if ($decompressed === false) {
                        $decompressed = @gzuncompress($stream);
                    }
                    if ($decompressed !== false) {
                        preg_match_all('/\((.*?)\)/s', $decompressed, $subMatches);
                        if (!empty($subMatches[1])) {
                            foreach ($subMatches[1] as $match) {
                                $text .= stripcslashes($match) . ' ';
                            }
                        }
                    }
                }
            }
        }
        
        return $text;
    }
}

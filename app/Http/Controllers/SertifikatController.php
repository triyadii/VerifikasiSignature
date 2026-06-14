<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Models\Sertifikat;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Output\QROutputInterface;
use Dompdf\Dompdf;
use Dompdf\Options;

class SertifikatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $query = Sertifikat::latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_id_sertifikat', 'like', "%{$search}%")
                  ->orWhere('nama_pu', 'like', "%{$search}%")
                  ->orWhere('nama_usaha', 'like', "%{$search}%")
                  ->orWhere('jenis_legalitas_usaha', 'like', "%{$search}%");
            });
        }

        $sertifikats = $query->paginate(10)->withQueryString();

        return view('sertifikat.index', compact('sertifikats', 'search'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_id_sertifikat' => 'required|string|max:200',
            'nama_pu'             => 'required|string|max:200',
            'nama_usaha'          => 'required|string|max:200',
            'alamat_lokasi_usaha' => 'required|string',
            'tanggal_dikeluarkan_surat' => 'required|date',
            'jenis_legalitas_usaha' => 'required|string|max:200',
        ]);

        Sertifikat::create($validated);

        return redirect()->route('sertifikat.index')
            ->with('success', 'Sertifikat berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sertifikat $sertifikat)
    {
        return view('sertifikat.edit', compact('sertifikat'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sertifikat $sertifikat)
    {
        $validated = $request->validate([
            'nomor_id_sertifikat' => 'required|string|max:200',
            'nama_pu'             => 'required|string|max:200',
            'nama_usaha'          => 'required|string|max:200',
            'alamat_lokasi_usaha' => 'required|string',
            'tanggal_dikeluarkan_surat' => 'required|date',
            'jenis_legalitas_usaha' => 'required|string|max:200',
        ]);

        // If data is updated, clear old signature + generated PDF to ensure integrity.
        if ($sertifikat->signature) {
            // Delete old PDF file from storage if exists
            if ($sertifikat->pdf_path && Storage::disk('public')->exists($sertifikat->pdf_path)) {
                Storage::disk('public')->delete($sertifikat->pdf_path);
            }
            $validated['signature']  = null;
            $validated['public_key'] = null;
            $validated['private_key'] = null;
            $validated['pdf_path']   = null;
        }

        $sertifikat->update($validated);

        return redirect()->route('sertifikat.index')
            ->with('success', 'Sertifikat berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sertifikat $sertifikat)
    {
        $sertifikat->delete();

        return redirect()->route('sertifikat.index')
            ->with('success', 'Sertifikat berhasil dihapus.');
    }

    /**
     * Generate RSA keys and digitally sign the certificate data.
     */
    public function generate(Sertifikat $sertifikat)
    {
        try {
            // Check if already signed
            if (!$sertifikat->signature || !$sertifikat->private_key || !$sertifikat->public_key) {
                // Generate RSA Keypair
                $config = array(
                    "private_key_bits" => 2048,
                    "private_key_type" => OPENSSL_KEYTYPE_RSA,
                );

                $res = openssl_pkey_new($config);
                if ($res === false) {
                    throw new \Exception("Gagal melakukan enkripsi RSA. Pastikan openssl php module aktif.");
                }

                // Export Private Key
                openssl_pkey_export($res, $private_key);

                // Export Public Key
                $public_key_details = openssl_pkey_get_details($res);
                $public_key = $public_key_details["key"];

                // Plain text data to sign (Certificate Data representation)
                $data_to_sign = $sertifikat->nomor_id_sertifikat . '|' .
                                 $sertifikat->nama_pu . '|' .
                                 $sertifikat->nama_usaha . '|' .
                                 $sertifikat->alamat_lokasi_usaha . '|' .
                                 $sertifikat->tanggal_dikeluarkan_surat . '|' .
                                 $sertifikat->jenis_legalitas_usaha;

                // Sign using SHA-256 algorithm
                openssl_sign($data_to_sign, $binary_signature, $private_key, OPENSSL_ALGO_SHA256);
                $signature = base64_encode($binary_signature);

                // Save keys and signature in database
                $sertifikat->update([
                    'private_key' => $private_key,
                    'public_key'  => $public_key,
                    'signature'   => $signature
                ]);
            }

            return view('sertifikat.generate_success', compact('sertifikat'));

        } catch (\Exception $e) {
            return redirect()->route('sertifikat.index')
                ->with('error', 'Gagal membuat Tanda Tangan Digital RSA: ' . $e->getMessage());
        }
    }

    /**
     * Generate PDF, save to local storage, and record the path in DB.
     * If PDF already exists, skip regeneration.
     */
    public function generatePdf(Sertifikat $sertifikat)
    {
        if (!$sertifikat->signature) {
            return redirect()->route('sertifikat.index')
                ->with('error', 'Dokumen belum ditandatangani secara digital. Silakan klik tombol TTE terlebih dahulu.');
        }

        try {
            $verifyUrl  = route('sertifikat.verify', $sertifikat->id);
            $qrOptions  = new QROptions([
                'outputInterface' => \chillerlan\QRCode\Output\QRMarkupSVG::class,
                'outputBase64'    => true,
            ]);
            $qrCodeData = (new QRCode($qrOptions))->render($verifyUrl);

            $pdfOptions = new Options();
            $pdfOptions->set('isHtml5ParserEnabled', true);
            $pdfOptions->set('isRemoteEnabled', true);

            $dompdf = new Dompdf($pdfOptions);
            $logoPath = public_path('logo.png');
            $logoData = '';
            if (file_exists($logoPath)) {
                $logoData = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
            }

            $html = view('sertifikat.pdf', compact('sertifikat', 'qrCodeData', 'logoData'))->render();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Save PDF to storage/app/public/sertifikat/
            $fileName    = 'Sertifikat_' . str_replace(['/', '\\', ' '], '_', $sertifikat->nomor_id_sertifikat) . '_' . $sertifikat->id . '.pdf';
            $storagePath = 'sertifikat/' . $fileName;

            Storage::disk('public')->put($storagePath, $dompdf->output());

            // Persist the path to DB
            $sertifikat->update(['pdf_path' => $storagePath]);

            return redirect()->route('sertifikat.index')
                ->with('success', 'PDF berhasil dibuat dan disimpan. Silakan klik tombol Download PDF.');

        } catch (\Exception $e) {
            return redirect()->route('sertifikat.index')
                ->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
        }
    }

    /**
     * Serve the saved PDF file from local storage as a download/inline view.
     */
    public function downloadPdf(Sertifikat $sertifikat)
    {
        if (!$sertifikat->pdf_path || !Storage::disk('public')->exists($sertifikat->pdf_path)) {
            return redirect()->route('sertifikat.index')
                ->with('error', 'File PDF belum dibuat. Silakan klik tombol "Buat PDF" terlebih dahulu.');
        }

        $absolutePath = Storage::disk('public')->path($sertifikat->pdf_path);
        $fileName     = 'Sertifikat_' . str_replace(['/', '\\', ' '], '_', $sertifikat->nomor_id_sertifikat) . '.pdf';

        return response()->file($absolutePath, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
            'Cache-Control'       => 'no-store, no-cache, must-revalidate',
        ]);
    }

    /**
     * Public page to verify the digital signature validity.
     */
    public function verify(Sertifikat $sertifikat)
    {
        $isValid = false;
        
        if ($sertifikat->signature && $sertifikat->public_key) {
            // Recalculate original plain text
            $data_to_verify = $sertifikat->nomor_id_sertifikat . '|' .
                             $sertifikat->nama_pu . '|' .
                             $sertifikat->nama_usaha . '|' .
                             $sertifikat->alamat_lokasi_usaha . '|' .
                             $sertifikat->tanggal_dikeluarkan_surat . '|' .
                             $sertifikat->jenis_legalitas_usaha;

            // Verify using openssl_verify
            $res = openssl_verify(
                $data_to_verify, 
                base64_decode($sertifikat->signature), 
                $sertifikat->public_key, 
                OPENSSL_ALGO_SHA256
            );
            
            $isValid = ($res === 1);
        }

        return view('sertifikat.verify', compact('sertifikat', 'isValid'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\DokumenInduk;
use App\Models\LembarKendali;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    /**
     * Dashboard Admin TU - Daftar semua surat masuk
     */
    public function index()
    {
        $dokumens = DokumenInduk::orderByDesc('id')->get();
        return view('admin.index', compact('dokumens'));
    }

    /**
     * Tampilkan form input surat baru
     */
    public function createSurat()
    {
        return view('admin.input_surat');
    }

    /**
     * Simpan surat baru ke database
     */
    public function storeSurat(Request $request)
    {
        $request->validate([
            'judul_draf'       => 'required|string|max:255',
            'instansi_pemohon' => 'required|string|max:255',
            'file_surat'       => 'required|file|mimes:pdf,doc,docx|max:10240',
        ]);

        // Upload file ke storage/app/public/uploads
        $file = $request->file('file_surat');
        $namaFile = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
        $file->storeAs('uploads', $namaFile, 'public');

        // Simpan ke database
        $dokumen = DokumenInduk::create([
            'judul_draf'       => $request->judul_draf,
            'instansi_pemohon' => $request->instansi_pemohon,
            'file_surat_awal'  => $namaFile,
            'status_global'    => 'Menunggu Disposisi',
        ]);

        // Catat di Lembar Kendali
        LembarKendali::create([
            'dokumen_id' => $dokumen->id,
            'aktor_id'   => Auth::id(),
            'tindakan'   => 'Surat Permohonan Diinput ke Sistem',
        ]);

        return redirect()->route('admin.index')
            ->with('success', 'Surat berhasil diinput dan masuk antrean Kabag!');
    }
}
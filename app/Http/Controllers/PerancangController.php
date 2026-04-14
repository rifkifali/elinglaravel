<?php

namespace App\Http\Controllers;

use App\Models\DokumenInduk;
use App\Models\DrafVersi;
use App\Models\LembarKendali;
use App\Models\Penugasan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerancangController extends Controller
{
    /**
     * Dashboard Perancang - daftar tugas yang ditugaskan
     */
    public function index()
    {
        $tugasList = Penugasan::with(['dokumen'])
            ->where('perancang_id', Auth::id())
            ->orderBy('deadline')
            ->get();

        // Tambahkan info catatan revisi untuk setiap dokumen
        foreach ($tugasList as $tugas) {
            $tugas->catatan_revisi = $this->getCatatanRevisi($tugas->dokumen_id, $tugas->dokumen->status_global);
        }

        return view('perancang.index', compact('tugasList'));
    }

    /**
     * Workspace drafting untuk dokumen tertentu
     */
    public function workspace(int $id)
    {
        $dokumen    = DokumenInduk::findOrFail($id);
        $versi_baru = $dokumen->nextVersi();

        // Cek catatan revisi dari pimpinan
        $revisi = LembarKendali::where('dokumen_id', $id)
            ->where(function ($q) {
                $q->where('tindakan', 'LIKE', 'Revisi Kasubbag:%')
                  ->orWhere('tindakan', 'LIKE', 'Ditolak/Revisi oleh Kabag:%');
            })
            ->orderByDesc('id')
            ->first();

        $catatanRevisi = null;
        $waktuRevisi   = null;

        if ($revisi && $dokumen->status_global === 'Proses Drafting') {
            $catatanRevisi = str_replace(
                ['Revisi Kasubbag: ', 'Ditolak/Revisi oleh Kabag: '],
                '',
                $revisi->tindakan
            );
            $waktuRevisi = $revisi->waktu_tindakan;
        }

        return view('perancang.workspace', compact('dokumen', 'versi_baru', 'catatanRevisi', 'waktuRevisi'));
    }

    /**
     * Kirim draf baru ke Kasubbag
     */
    public function kirimDraf(Request $request, int $id)
    {
        $request->validate([
            'catatan_revisi' => 'required|string',
            'file_draf'      => 'required|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $dokumen    = DokumenInduk::findOrFail($id);
        $versiBaru  = $dokumen->nextVersi();

        // Upload file
        $file     = $request->file('file_draf');
        $namaFile = time() . '_V' . $versiBaru . '_' . str_replace(' ', '_', $file->getClientOriginalName());
        $file->storeAs('uploads', $namaFile, 'public');

        // Simpan draf baru
        DrafVersi::create([
            'dokumen_id'     => $id,
            'versi_ke'       => $versiBaru,
            'file_draf'      => $namaFile,
            'catatan_revisi' => $request->catatan_revisi,
        ]);

        // Update status dokumen
        $dokumen->update(['status_global' => 'Review Kasubbag']);

        // Catat di Lembar Kendali
        LembarKendali::create([
            'dokumen_id' => $id,
            'aktor_id'   => Auth::id(),
            'tindakan'   => "Draf V{$versiBaru} dikirim ke Kasubbag",
        ]);

        return redirect()->route('perancang.index')
            ->with('success', "Draf Versi {$versiBaru} berhasil dikirim ke Kasubbag!");
    }

    /**
     * Helper: ambil catatan revisi terbaru dari pimpinan
     */
    private function getCatatanRevisi(int $dokumenId, string $status): ?string
    {
        if ($status !== 'Proses Drafting') return null;

        $log = LembarKendali::where('dokumen_id', $dokumenId)
            ->where(function ($q) {
                $q->where('tindakan', 'LIKE', 'Revisi Kasubbag:%')
                  ->orWhere('tindakan', 'LIKE', 'Ditolak/Revisi oleh Kabag:%');
            })
            ->orderByDesc('id')
            ->first();

        if (!$log) return null;

        return str_replace(
            ['Revisi Kasubbag: ', 'Ditolak/Revisi oleh Kabag: '],
            '',
            $log->tindakan
        );
    }
}
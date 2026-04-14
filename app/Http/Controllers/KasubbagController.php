<?php

namespace App\Http\Controllers;

use App\Models\DokumenInduk;
use App\Models\LembarKendali;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KasubbagController extends Controller
{
    /**
     * Dashboard Kasubbag
     */
    public function index()
    {
        $dokumens = DokumenInduk::whereIn('status_global', [
            'Proses Drafting',
            'Review Kasubbag',
            'Review Kabag',
            'Final',
        ])->orderByDesc('id')->get();

        // Cek apakah ada catatan revisi dari Kabag
        foreach ($dokumens as $doc) {
            $doc->catatan_kabag = $this->getCatatanKabag($doc->id, $doc->status_global);
        }

        return view('kasubbag.index', compact('dokumens'));
    }

    /**
     * Form review draf
     */
    public function reviewDraf(int $id)
    {
        $dokumen     = DokumenInduk::findOrFail($id);
        $drafTerbaru = $dokumen->drafTerbaru;

        // Cek apakah ada catatan revisi dari Kabag
        $revisiKabag = LembarKendali::where('dokumen_id', $id)
            ->where('tindakan', 'LIKE', 'Ditolak/Revisi oleh Kabag:%')
            ->orderByDesc('id')
            ->first();

        $catatanRevisiKabag = null;
        $waktuRevisiKabag   = null;

        if ($revisiKabag && $dokumen->status_global === 'Review Kasubbag') {
            $catatanRevisiKabag = str_replace('Ditolak/Revisi oleh Kabag: ', '', $revisiKabag->tindakan);
            $waktuRevisiKabag   = $revisiKabag->waktu_tindakan;
        }

        return view('kasubbag.review_draf', compact(
            'dokumen',
            'drafTerbaru',
            'catatanRevisiKabag',
            'waktuRevisiKabag'
        ));
    }

    /**
     * Tolak - kembalikan ke Perancang
     */
    public function revisi(Request $request, int $id)
    {
        $request->validate([
            'catatan_review' => 'required|string',
        ]);

        $dokumen = DokumenInduk::findOrFail($id);
        $dokumen->update(['status_global' => 'Proses Drafting']);

        LembarKendali::create([
            'dokumen_id' => $id,
            'aktor_id'   => Auth::id(),
            'tindakan'   => 'Revisi Kasubbag: ' . $request->catatan_review,
        ]);

        return redirect()->route('kasubbag.index')
            ->with('success', 'Draf dikembalikan ke Perancang untuk direvisi.');
    }

    /**
     * Setujui - teruskan ke Kabag
     */
    public function setuju(Request $request, int $id)
    {
        $catatan = $request->catatan_review ?: 'Disetujui tanpa catatan';

        $dokumen = DokumenInduk::findOrFail($id);
        $dokumen->update(['status_global' => 'Review Kabag']);

        LembarKendali::create([
            'dokumen_id' => $id,
            'aktor_id'   => Auth::id(),
            'tindakan'   => 'Disetujui Kasubbag. Diteruskan ke Kabag. (Catatan: ' . $catatan . ')',
        ]);

        return redirect()->route('kasubbag.index')
            ->with('success', 'Draf disetujui dan berhasil diteruskan ke Kabag.');
    }

    /**
     * Detail lacak dokumen (view only)
     */
    public function detailLacak(int $id)
    {
        $dokumen     = DokumenInduk::findOrFail($id);
        $drafTerbaru = $dokumen->drafTerbaru;
        $timeline    = $dokumen->lembarKendali()->with('aktor')->orderBy('waktu_tindakan')->get();

        return view('kasubbag.detail_lacak', compact('dokumen', 'drafTerbaru', 'timeline'));
    }

    /**
     * Helper: ambil catatan revisi dari Kabag
     */
    private function getCatatanKabag(int $dokumenId, string $status): ?string
    {
        if ($status !== 'Review Kasubbag') {
            return null;
        }

        $log = LembarKendali::where('dokumen_id', $dokumenId)
            ->where('tindakan', 'LIKE', 'Ditolak/Revisi oleh Kabag:%')
            ->orderByDesc('id')
            ->first();

        if (!$log) return null;

        $catatan = str_replace('Ditolak/Revisi oleh Kabag: ', '', $log->tindakan);
        return strlen($catatan) > 40 ? substr($catatan, 0, 40) . '...' : $catatan;
    }
}
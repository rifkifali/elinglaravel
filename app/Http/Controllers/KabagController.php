<?php

namespace App\Http\Controllers;

use App\Models\DokumenInduk;
use App\Models\LembarKendali;
use App\Models\Penugasan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KabagController extends Controller
{
    /**
     * Dashboard Kabag - semua dokumen
     */
    public function index()
    {
        $dokumens = DokumenInduk::orderByDesc('id')->get();
        return view('kabag.index', compact('dokumens'));
    }

    /**
     * Form disposisi - tunjuk perancang
     */
    public function disposisi(int $id)
    {
        $dokumen = DokumenInduk::findOrFail($id);
        $perancangList = User::where('role', 'Perancang')->get();
        return view('kabag.disposisi', compact('dokumen', 'perancangList'));
    }

    /**
     * Simpan disposisi
     */
    public function storeDisposisi(Request $request, int $id)
    {
        $request->validate([
            'perancang_id' => 'required|exists:users,id',
            'deadline'     => 'required|date|after_or_equal:today',
            'catatan'      => 'required|string',
        ]);

        $dokumen = DokumenInduk::findOrFail($id);

        // Simpan penugasan
        Penugasan::create([
            'dokumen_id'        => $id,
            'perancang_id'      => $request->perancang_id,
            'catatan_disposisi' => $request->catatan,
            'deadline'          => $request->deadline,
        ]);

        // Update status
        $dokumen->update(['status_global' => 'Proses Drafting']);

        // Catat di Lembar Kendali
        LembarKendali::create([
            'dokumen_id' => $id,
            'aktor_id'   => Auth::id(),
            'tindakan'   => 'Disposisi diberikan kepada Perancang',
        ]);

        return redirect()->route('kabag.index')
            ->with('success', 'Disposisi berhasil dikirim!');
    }

    /**
     * Detail lacak + finalisasi / revisi
     */
    public function detailLacak(int $id)
    {
        $dokumen     = DokumenInduk::with(['drafTerbaru', 'lembarKendali.aktor'])->findOrFail($id);
        $drafTerbaru = $dokumen->drafTerbaru;
        $timeline    = $dokumen->lembarKendali()->with('aktor')->orderBy('waktu_tindakan')->get();

        return view('kabag.detail_lacak', compact('dokumen', 'drafTerbaru', 'timeline'));
    }

    /**
     * Finalisasi dokumen (setujui)
     */
    public function finalisasi(int $id)
    {
        $dokumen = DokumenInduk::findOrFail($id);
        $dokumen->update(['status_global' => 'Final']);

        LembarKendali::create([
            'dokumen_id' => $id,
            'aktor_id'   => Auth::id(),
            'tindakan'   => 'Dokumen DISETUJUI FINAL oleh Kabag Hukum. Siap cetak/arsip.',
        ]);

        return redirect()->route('kabag.index')
            ->with('success', 'Dokumen telah disetujui dan berstatus FINAL.');
    }

    /**
     * Tolak dan minta revisi
     */
    public function revisi(Request $request, int $id)
    {
        $request->validate([
            'catatan_revisi' => 'required|string',
        ]);

        $dokumen = DokumenInduk::findOrFail($id);
        $dokumen->update(['status_global' => 'Review Kasubbag']);

        LembarKendali::create([
            'dokumen_id' => $id,
            'aktor_id'   => Auth::id(),
            'tindakan'   => 'Ditolak/Revisi oleh Kabag: ' . $request->catatan_revisi,
        ]);

        return redirect()->route('kabag.index')
            ->with('success', 'Dokumen dikembalikan ke Kasubbag untuk direvisi.');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\DokumenInduk;
use App\Models\DrafVersi;
use App\Models\LembarKendali;
use App\Models\Penugasan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SuperAdminController extends Controller
{
    // ================================================================
    // MANAJEMEN USER
    // ================================================================

    /**
     * Dashboard - Daftar semua pengguna
     */
    public function index()
    {
        $users = User::orderBy('role')->orderBy('nama_lengkap')->get();
        return view('superadmin.index', compact('users'));
    }

    /**
     * Form tambah/edit pengguna
     */
    public function formUser(?int $id = null)
    {
        $user = $id ? User::findOrFail($id) : null;
        $mode = $user ? 'Edit' : 'Tambah';
        $roles = ['Admin', 'Perancang', 'Kasubbag', 'Kabag', 'Super Admin'];
        return view('superadmin.form_user', compact('user', 'mode', 'roles'));
    }

    /**
     * Simpan / Update pengguna
     */
    public function saveUser(Request $request, ?int $id = null)
    {
        $rules = [
            'nama_lengkap' => 'required|string|max:255',
            'nip'          => 'required|string|max:18|unique:users,nip' . ($id ? ",{$id}" : ''),
            'password'     => 'required|string|min:4',
            'role_user'    => 'required|in:Admin,Perancang,Kasubbag,Kabag,Super Admin',
        ];

        $request->validate($rules);

        $data = [
            'nama_lengkap' => $request->nama_lengkap,
            'name'         => $request->nama_lengkap,
            'nip'          => $request->nip,
            'password'     => $request->password, // plain text sesuai sistem lama
            'role'         => $request->role_user,
        ];

        if ($id) {
            User::findOrFail($id)->update($data);
            $pesan = 'Data pengguna berhasil diperbarui!';
        } else {
            User::create($data);
            $pesan = 'Pengguna baru berhasil ditambahkan!';
        }

        return redirect()->route('superadmin.index')->with('success', $pesan);
    }

    /**
     * Hapus pengguna
     */
    public function deleteUser(int $id)
    {
        if ($id === Auth::id()) {
            return redirect()->route('superadmin.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        User::findOrFail($id)->delete();
        return redirect()->route('superadmin.index')
            ->with('success', 'Data pengguna berhasil dihapus!');
    }

    // ================================================================
    // MANAJEMEN SURAT
    // ================================================================

    /**
     * Daftar semua dokumen
     */
    public function manageSurat()
    {
        $dokumens = DokumenInduk::orderByDesc('id')->get();
        return view('superadmin.manage_surat', compact('dokumens'));
    }

    /**
     * Form edit surat
     */
    public function editSurat(int $id)
    {
        $dokumen  = DokumenInduk::findOrFail($id);
        $statuses = ['Menunggu Disposisi', 'Proses Drafting', 'Review Kasubbag', 'Review Kabag', 'Final'];
        return view('superadmin.edit_surat', compact('dokumen', 'statuses'));
    }

    /**
     * Simpan perubahan surat
     */
    public function updateSurat(Request $request, int $id)
    {
        $request->validate([
            'judul'    => 'required|string|max:255',
            'instansi' => 'required|string|max:255',
            'status'   => 'required|in:Menunggu Disposisi,Proses Drafting,Review Kasubbag,Review Kabag,Final',
        ]);

        DokumenInduk::findOrFail($id)->update([
            'judul_draf'       => $request->judul,
            'instansi_pemohon' => $request->instansi,
            'status_global'    => $request->status,
        ]);

        return redirect()->route('superadmin.manage-surat')
            ->with('success', 'Data dokumen berhasil diupdate!');
    }

    /**
     * Hapus surat beserta seluruh relasinya
     */
    public function deleteSurat(int $id)
    {
        $dokumen = DokumenInduk::findOrFail($id);

        // Hapus file fisik dari storage
        if ($dokumen->file_surat_awal && Storage::disk('public')->exists('uploads/' . $dokumen->file_surat_awal)) {
            Storage::disk('public')->delete('uploads/' . $dokumen->file_surat_awal);
        }

        // Hapus semua file draf versi
        foreach ($dokumen->drafVersi as $draf) {
            if (Storage::disk('public')->exists('uploads/' . $draf->file_draf)) {
                Storage::disk('public')->delete('uploads/' . $draf->file_draf);
            }
        }

        // Laravel akan cascade delete karena foreign key (onDelete cascade)
        $dokumen->delete();

        return redirect()->route('superadmin.manage-surat')
            ->with('success', 'Surat dan semua riwayatnya berhasil dihapus!');
    }

    // ================================================================
    // MANAJEMEN TIMELINE
    // ================================================================

    /**
     * Daftar semua log aktivitas
     */
    public function manageTimeline()
    {
        $timeline = LembarKendali::with(['dokumen', 'aktor'])
            ->orderByDesc('waktu_tindakan')
            ->get();

        return view('superadmin.manage_timeline', compact('timeline'));
    }

    /**
     * Form edit log timeline
     */
    public function editTimeline(int $id)
    {
        $log = LembarKendali::with(['dokumen'])->findOrFail($id);
        return view('superadmin.edit_timeline', compact('log'));
    }

    /**
     * Simpan perubahan log timeline
     */
    public function updateTimeline(Request $request, int $id)
    {
        $request->validate([
            'tindakan' => 'required|string',
            'waktu'    => 'required|date',
        ]);

        LembarKendali::findOrFail($id)->update([
            'tindakan'       => $request->tindakan,
            'waktu_tindakan' => $request->waktu,
        ]);

        return redirect()->route('superadmin.manage-timeline')
            ->with('success', 'Catatan timeline berhasil diubah!');
    }

    /**
     * Hapus satu log timeline
     */
    public function deleteTimeline(int $id)
    {
        LembarKendali::findOrFail($id)->delete();
        return redirect()->route('superadmin.manage-timeline')
            ->with('success', 'Catatan riwayat dihapus!');
    }
}
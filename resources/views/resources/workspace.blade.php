<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workspace Perancang - E-LETING</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f7f6; color: #334155; }
        .navbar-custom { background: linear-gradient(135deg, #059669 0%, #047857 100%); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .card-custom { border: none; border-radius: 16px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); background: #ffffff; overflow: hidden; }
        .doc-info-box { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; }
        .form-label { font-weight: 600; color: #475569; font-size: 0.9rem; }
        .form-control { border-radius: 8px; padding: 0.75rem 1rem; border: 1px solid #cbd5e1; transition: all 0.2s; }
        .form-control:focus { box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15); border-color: #10b981; }
        .upload-box { border: 2px dashed #94a3b8; border-radius: 12px; padding: 2.5rem 1.5rem; background-color: #f8fafc; text-align: center; transition: all 0.3s ease; cursor: pointer; }
        .upload-box:hover, .upload-box:focus-within { border-color: #10b981; background-color: #ecfdf5; }
        .btn-custom { border-radius: 8px; font-weight: 600; transition: all 0.2s; }
        .btn-custom:hover { transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom mb-5 py-3">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ route('perancang.index') }}">
            <i class="bi bi-pen fs-4 me-2"></i> E-LETING <span class="fw-light ms-2 fs-6 opacity-75">| Ruang Perancang</span>
        </a>
        <div class="d-flex align-items-center">
            <a href="{{ route('perancang.index') }}" class="btn btn-outline-light btn-sm btn-custom px-3">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>
</nav>

<div class="container mb-5">
    
    <div class="mb-4">
        <h3 class="fw-bold mb-1 text-dark">Workspace Drafting</h3>
        <p class="text-muted mb-0">Referensi dokumen awal dan ruang unggah hasil draf Anda.</p>
    </div>

    {{-- Alert Error Validasi --}}
    @if ($errors->any())
        <div class="alert alert-danger border-0 border-start border-danger border-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-5">
            
            {{-- Peringatan Catatan Revisi (Jika Ada) --}}
            @if ($catatanRevisi && $dokumen->status_global === 'Proses Drafting')
            <div class="alert alert-danger border-0 border-start border-danger border-4 p-4 mb-4 shadow-sm bg-danger bg-opacity-10">
                <h6 class="fw-bold text-danger mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i>Catatan Revisi dari Pimpinan</h6>
                <p class="mb-2 text-dark fw-medium fst-italic lh-base">"{{ $catatanRevisi }}"</p>
                <hr class="border-danger opacity-25 my-2">
                <small class="text-danger fw-bold"><i class="bi bi-clock me-1"></i> Dikembalikan pada: {{ \Carbon\Carbon::parse($waktuRevisi)->format('d M Y - H:i') }}</small>
            </div>
            @endif

            <div class="card card-custom h-100">
                <div class="card-header bg-white border-bottom p-4">
                    <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                        <i class="bi bi-journal-text me-2 text-primary fs-5"></i> Referensi Dokumen Induk
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="doc-info-box mb-4">
                        <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-1 mb-3">ID Dokumen: #{{ $dokumen->id }}</span>
                        
                        <p class="text-muted small mb-1"><i class="bi bi-bookmark text-primary me-1"></i> Judul Produk Hukum</p>
                        <h6 class="fw-bold text-dark mb-3 lh-base">{{ $dokumen->judul_draf }}</h6>
                        
                        <hr class="opacity-25">
                        
                        <p class="text-muted small mb-1"><i class="bi bi-building text-primary me-1"></i> Instansi Pemohon</p>
                        <p class="mb-0 fw-medium text-dark">{{ $dokumen->instansi_pemohon }}</p>
                    </div>
                    
                    {{-- Menggunakan asset path untuk file storage di Laravel (Pastikan 'php artisan storage:link' sudah dijalankan) --}}
                    <a href="{{ asset('storage/uploads/' . $dokumen->file_surat_awal) }}" target="_blank" class="btn btn-outline-primary btn-custom w-100 py-3 d-flex justify-content-center align-items-center">
                        <i class="bi bi-file-earmark-pdf fs-5 me-2"></i> Buka Surat Permohonan Awal
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card card-custom h-100 border-top border-success border-4">
                <div class="card-body p-4 p-md-5">
                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold text-dark mb-0">Upload Hasil Kerja</h5>
                        <span class="badge bg-success fs-6 px-3 py-2 rounded-pill shadow-sm">Versi {{ $versi_baru }}</span>
                    </div>
                    
                    {{-- Form Upload (Perhatikan penggunaan enctype) --}}
                    <form method="POST" action="{{ route('perancang.kirim-draf', $dokumen->id) }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label"><i class="bi bi-chat-left-dots me-1 text-success"></i> Catatan Untuk Kasubbag</label>
                            <textarea name="catatan_revisi" class="form-control" rows="4" required placeholder="Jelaskan progres draf, pasal yang telah ditambahkan, atau penyesuaian yang telah dilakukan...">{{ old('catatan_revisi') }}</textarea>
                        </div>

                        <div class="mb-5">
                            <label class="form-label"><i class="bi bi-cloud-arrow-up me-1 text-success"></i> File Draf Baru</label>
                            <div class="upload-box position-relative">
                                <i class="bi bi-file-earmark-word display-4 text-secondary mb-3 d-block opacity-75"></i>
                                <h6 class="fw-bold text-dark mb-1">Seret File atau Klik di Sini</h6>
                                <p class="text-muted small mb-3">Format yang diizinkan: Word (.doc/.docx) atau PDF. (Max: 10MB)</p>
                                <input type="file" name="file_draf" class="form-control" accept=".pdf,.doc,.docx" required style="opacity: 0; position: absolute; top:0; left:0; width:100%; height:100%; cursor: pointer; z-index: 2;">
                                <div class="btn btn-outline-success btn-sm btn-custom px-4">Jelajahi File Komputer</div>
                            </div>
                        </div>

                        <hr class="mb-4" style="opacity: 0.1;">

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-custom py-3 fs-6 d-flex justify-content-center align-items-center shadow">
                                <i class="bi bi-send-check fs-5 me-2"></i> Kirim Draf V{{ $versi_baru }} ke Kasubbag
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
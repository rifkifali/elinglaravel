<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disposisi Tugas - E-LETING</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7f6;
            color: #334155;
        }
        .navbar-custom {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .card-custom {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            background: #ffffff;
            overflow: hidden;
        }
        .form-label {
            font-weight: 600;
            color: #475569;
            font-size: 0.9rem;
        }
        .form-control, .form-select {
            border-radius: 8px;
            padding: 0.75rem 1rem;
            border: 1px solid #cbd5e1;
            font-size: 0.95rem;
            transition: all 0.2s;
        }
        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
            border-color: #0d6efd;
        }
        .doc-info-box {
            background-color: #f8fafc;
            border-left: 4px solid #1e293b;
            border-radius: 8px;
        }
        .btn-custom {
            border-radius: 8px;
            font-weight: 600;
            padding: 0.6rem 1.5rem;
            transition: all 0.2s;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom mb-5 py-3">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ route('kabag.index') }}">
            <i class="bi bi-shield-check fs-4 me-2"></i>
            E-LETING <span class="fw-light ms-2 fs-6 opacity-75">| Kabag Hukum</span>
        </a>
    </div>
</nav>

<div class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-9">
            
            <div class="card card-custom">
                <div class="card-header bg-white border-bottom p-4 pb-3 d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 45px; height: 45px;">
                        <i class="bi bi-send-check fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">Lembar Disposisi Tugas</h5>
                        <small class="text-muted">Delegasikan draf produk hukum ke Perancang</small>
                    </div>
                </div>

                <div class="card-body p-4 p-md-5 pt-4">
                    
                    {{-- Alert Error Validasi Laravel --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <div class="doc-info-box p-3 mb-4 shadow-sm">
                        <span class="badge bg-secondary mb-2">ID Dokumen: #{{ $dokumen->id }}</span>
                        <h6 class="fw-bold text-dark mb-1" style="line-height: 1.4;">{{ $dokumen->judul_draf }}</h6>
                        <small class="text-muted"><i class="bi bi-building me-1"></i> {{ $dokumen->instansi_pemohon }}</small>
                    </div>
                    
                    <form method="POST" action="{{ route('kabag.store-disposisi', $dokumen->id) }}">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label"><i class="bi bi-person-badge me-1 text-primary"></i> Tunjuk Perancang (Legal Drafter)</label>
                            <select name="perancang_id" class="form-select" required>
                                <option value="" disabled selected>-- Pilih Nama Pegawai / Perancang --</option>
                                @foreach($perancangList as $p)
                                    <option value="{{ $p->id }}">{{ $p->nama_lengkap }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label"><i class="bi bi-calendar-event me-1 text-primary"></i> Tenggat Waktu (Deadline)</label>
                            <input type="date" name="deadline" class="form-control" value="{{ old('deadline') }}" required>
                            <small class="text-muted mt-1 d-block"><i class="bi bi-info-circle me-1"></i>Tentukan batas waktu penyelesaian draf awal.</small>
                        </div>

                        <div class="mb-5">
                            <label class="form-label"><i class="bi bi-chat-left-text me-1 text-primary"></i> Catatan Arahan Pimpinan</label>
                            <textarea name="catatan" class="form-control" rows="4" placeholder="Contoh: Segera buat draf awal, perhatikan konsideran menimbang sesuai UU No. 1 Tahun 2022..." required>{{ old('catatan') }}</textarea>
                        </div>

                        <hr class="mb-4" style="opacity: 0.1;">

                        <div class="d-flex justify-content-end gap-3">
                            <a href="{{ route('kabag.index') }}" class="btn btn-light btn-custom text-secondary border">Batal</a>
                            <button type="submit" class="btn btn-primary btn-custom shadow-sm">
                                <i class="bi bi-paperclip me-1"></i> Kirim Tugas Disposisi
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
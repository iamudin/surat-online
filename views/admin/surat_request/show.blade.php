@extends('cms::backend.layout.app', ['title' => 'Detail Permohonan Surat'])
@section('content')
    <div class="row">
        <div class="col-md-7 mb-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Data Permohonan</h4>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-bordered">
                        <tr>
                            <th width="30%">Nomor Tiket</th>
                            <td><code>{{ $req->ticket_number }}</code></td>
                        </tr>
                        <tr>
                            <th>Waktu Pengajuan</th>
                            <td>{{ $req->created_at->format('d M Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Jenis Surat</th>
                            <td><strong>{{ $req->type?->name }}</strong></td>
                        </tr>
                        <tr>
                            <th>Nama Pemohon</th>
                            <td>{{ $req->warga?->name }}</td>
                        </tr>
                        <tr>
                            <th>NIK Pemohon</th>
                            <td>{{ $req->warga?->nik }}</td>
                        </tr>
                        <tr>
                            <th>No HP Pemohon</th>
                            <td>{{ $req->warga?->phone ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>RT / RW</th>
                            <td>
                                RT {{ $req->warga?->rt?->nomor_rt ?? '-' }} ({{ $req->warga?->rt?->name ?? '-' }}) /
                                RW {{ $req->warga?->rw?->nomor_rw ?? '-' }} ({{ $req->warga?->rw?->name ?? '-' }})
                            </td>
                        </tr>
                        <tr>
                            <th>Status Saat Ini</th>
                            <td>
                                @if($req->status == 'pending')
                                    <span class="badge badge-warning">Menunggu</span>
                                @elseif($req->status == 'processing')
                                    <span class="badge badge-info">Diproses</span>
                                @elseif($req->status == 'approved')
                                    <span class="badge badge-success">Selesai/Disetujui</span>
                                @else
                                    <span class="badge badge-danger">Ditolak</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Validasi RT</th>
                            <td>
                                @if($req->is_rt_approved)
                                    <span class="badge badge-success"><i class="fa fa-check"></i> Telah Divalidasi RT
                                        (Valid)</span>
                                @elseif($req->status == 'rejected')
                                    <span class="badge badge-danger"><i class="fa fa-times"></i> Telah Divalidasi RT
                                        (Ditolak)</span>
                                @else
                                    <span class="badge badge-warning"><i class="fa fa-clock"></i> Belum Divalidasi RT</span>
                                @endif
                            </td>
                        </tr>
                        @if($req->catatan_rt)
                            <tr>
                                <th>Catatan RT</th>
                                <td class="text-danger">
                                    <em>{{ $req->catatan_rt }}</em>
                                </td>
                            </tr>
                        @endif
                    </table>

                    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
                        <h5 class="mb-0">Isian Form Warga:</h5>
                        <a href="{{ route('surat-requests.edit', $req->id) }}" class="btn btn-warning btn-sm"><i
                                class="fa fa-edit"></i> Edit Data</a>
                    </div>
                    <table class="table table-bordered">
                        @foreach($req->type->fields as $field)
                            @php $data = $req->data->where('surat_field_id', $field->id)->first(); @endphp
                            @if($field->field_type == 'break')
                                <tr>
                                    <td colspan="2" class="bg-light pt-3">
                                        <h6 class="mb-0 font-weight-bold"><i
                                                class="fa fa-folder-open text-primary mr-2"></i>{{ $field->field_name }}</h6>
                                    </td>
                                </tr>
                            @elseif($data)
                                <tr>
                                    <th width="35%">{{ $field->field_name }}</th>
                                    <td>
                                        @if($field->field_type == 'file' && $data->file_path)
                                            <button data-media="{{ media($data->file_path)->url() }}"
                                                data-ext="{{ media($data->file_path)->extension() }}" target="_blank"
                                                class="btn btn-sm btn-info btn-view-media">Lihat/Download Lampiran</a>
                                        @elseif($field->field_type == 'array')
                                                @php
                                                    $arrayData = json_decode($data->field_value, true) ?? [];
                                                @endphp
                                                @if(count($arrayData) > 0)
                                                    <table class="table table-sm table-bordered mb-0">
                                                        <thead class="bg-light">
                                                            <tr>
                                                                <th>No.</th>
                                                                @foreach(array_keys($arrayData[0]) as $colName)
                                                                    <th>{{ $colName }}</th>
                                                                @endforeach
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($arrayData as $index => $row)
                                                                <tr>
                                                                    <td>{{ $index + 1 }}</td>
                                                                    @foreach($row as $val)
                                                                        <td>{{ $val }}</td>
                                                                    @endforeach
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                @else
                                                    <span class="text-muted">- Kosong -</span>
                                                @endif
                                            @else
                                                {{ $data->field_value }}
                                            @endif
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-5 mb-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Proses Permohonan</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if(!$req->is_rt_approved)
                        <div class="alert alert-warning mb-3">
                            <i class="fa fa-exclamation-triangle"></i> <strong>Perhatian!</strong> Permohonan ini <b>belum
                                divalidasi</b> oleh RT setempat. Anda tetap dapat memprosesnya jika diperlukan.
                        </div>
                    @endif

                    <form action="{{ route('surat-requests.update', $req->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label>Update Status</label>
                            <select name="status" class="form-control" required>
                                <option value="pending" {{ $req->status == 'pending' ? 'selected' : '' }}>Menunggu (Pending)
                                </option>
                                <option value="processing" {{ $req->status == 'processing' ? 'selected' : '' }}>Sedang
                                    Diproses (Processing)</option>
                                <option value="approved" {{ $req->status == 'approved' ? 'selected' : '' }}>Disetujui &
                                    Selesai (Approved)</option>
                                <option value="rejected" {{ $req->status == 'rejected' ? 'selected' : '' }}>Ditolak (Rejected)
                                </option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label>Catatan Admin (Opsional)</label>
                            <textarea name="admin_notes" class="form-control" rows="4"
                                placeholder="Misal: Surat sedang dicetak, silakan ambil di balai desa besok. ATAU KTP buram, silakan ajukan ulang.">{{ old('admin_notes', $req->admin_notes) }}</textarea>
                            <small class="text-muted">Catatan ini akan dapat dilihat oleh pemohon saat mengecek
                                tiket.</small>
                        </div>

                        <button class="btn btn-primary w-100 mb-2">Simpan Update Status</button>
                        <a href="{{ route('surat-requests.index') }}" class="btn btn-secondary w-100">Kembali ke Daftar</a>
                    </form>

                    <hr class="my-4">

                    <h5 class="mb-3">Dokumen Surat</h5>
                    @if($req->pdf_path)
                        <div class="mb-2">
                            <a href="{{ url($req->pdf_path) }}" target="_blank" class="btn btn-outline-info w-100 text-left">
                                <i class="fa fa-download"></i> Download Surat (Tanda Tangan Basah)
                            </a>
                        </div>
                    @endif
                    @if($req->is_signed && $req->signed_pdf_path)
                        <div class="mb-3">
                            <a href="{{ url($req->signed_pdf_path) }}" target="_blank"
                                class="btn btn-outline-success w-100 text-left">
                                <i class="fa fa-download"></i> Download Surat (TTE Kades)
                            </a>
                            <small class="text-muted d-block mt-1">Ditandatangani secara elektronik pada:
                                {{ \Carbon\Carbon::parse($req->signed_at)->format('d M Y H:i:s') }}</small>
                        </div>
                    @endif
                    @if(!$req->pdf_path && !$req->signed_pdf_path)
                        <div class="alert alert-light border">
                            Belum ada dokumen PDF yang di-generate. Ubah status menjadi <strong>Diproses</strong> untuk
                            meng-generate.
                        </div>
                    @endif

                    <hr class="my-4">

                    <h5 class="mb-3">Upload Surat Final</h5>
                    <form action="{{ route('surat-requests.uploadFinal', $req->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label>Pilih File (PDF/JPG/PNG max 5MB)</label>
                            <input type="file" name="final_file" class="form-control-file" required>
                            <small class="text-muted d-block mt-1">File yang diunggah di sini akan otomatis mengubah status
                                menjadi "Disetujui & Selesai" dan akan tersedia untuk didownload oleh Warga di portal
                                mereka.</small>
                        </div>
                        <button class="btn btn-success w-100"><i class="fa fa-upload"></i> Upload & Selesaikan
                            Permohonan</button>
                    </form>

                    <hr class="my-4">
                    <form action="{{ route('surat-requests.destroy', $req->id) }}" method="POST"
                        onsubmit="return confirm('Hapus permanen data permohonan ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger w-100">Hapus Data Permohonan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        @include('cms::backend.layout.js')
    @endpush
@endsection
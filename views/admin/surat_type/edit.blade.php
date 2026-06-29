@extends('cms::backend.layout.app', ['title' => 'Edit Jenis Surat & Form'])
@section('content')
    <div class="row">
        <div class="col-md-5 mb-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit Jenis Surat</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('surat-types.update', $type->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group mb-3">
                            <label>Nama Surat</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $type->name) }}"
                                required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Slug (URL)</label>
                            <input type="text" name="slug" class="form-control" value="{{ old('slug', $type->slug) }}"
                                required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Deskripsi Singkat</label>
                            <textarea name="description" class="form-control"
                                rows="3">{{ old('description', $type->description) }}</textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label>Status</label>
                            <select name="is_active" class="form-control">
                                <option value="1" {{ $type->is_active ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ !$type->is_active ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                        </div>
                        <button class="btn btn-primary">Simpan Perubahan</button>
                        <a href="{{ route('surat-types.index') }}" class="btn btn-secondary">Kembali</a>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-7 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Form Builder (Fields)</h4>
                    <button type="button" class="btn btn-sm btn-primary" onclick="$('#addFieldModal').modal('show')">
                        <i class="fa fa-plus"></i> Tambah Field
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Label Field</th>
                                    <th>Tipe</th>
                                    <th>Wajib?</th>
                                    <th>Opsi (Select)</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($type->fields as $field)
                                    <tr>
                                        <td>{{ $field->field_name }}</td>
                                        <td><code>{{ $field->field_type }}</code></td>
                                        <td>{{ $field->is_required ? 'Ya' : 'Tidak' }}</td>
                                        <td>
                                            @if($field->field_options)
                                                {{ implode(', ', $field->field_options) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex" style="gap:5px;">
                                                <!-- Sort Up -->
                                                <form action="{{ route('surat-types.moveField', $field->id) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="direction" value="up">
                                                    <button type="submit" class="btn btn-secondary btn-sm" {{ $loop->first ? 'disabled' : '' }}><i class="fa fa-arrow-up"></i></button>
                                                </form>

                                                <!-- Sort Down -->
                                                <form action="{{ route('surat-types.moveField', $field->id) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="direction" value="down">
                                                    <button type="submit" class="btn btn-secondary btn-sm" {{ $loop->last ? 'disabled' : '' }}><i class="fa fa-arrow-down"></i></button>
                                                </form>

                                                <!-- Edit -->
                                                <button type="button" class="btn btn-warning btn-sm"
                                                    onclick="openEditFieldModal({{ $field->id }}, '{{ addslashes($field->field_name) }}', '{{ $field->field_type }}', '{{ addslashes($field->description) }}', '{{ $field->field_options ? implode(',', $field->field_options) : '' }}', {{ $field->is_required }})">
                                                    <i class="fa fa-edit"></i>
                                                </button>

                                                <!-- Delete -->
                                                <form action="{{ route('surat-types.destroyField', $field->id) }}" method="POST"
                                                    onsubmit="return confirm('Hapus field ini?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger btn-sm"><i
                                                            class="fa fa-trash"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Belum ada field dinamis.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Field -->
    <div class="modal fade" id="addFieldModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('surat-types.storeField', $type->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Field Baru</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label>Label Field (Contoh: Pekerjaan / Scan KTP)</label>
                            <input type="text" name="field_name" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Deskripsi/Penjelasan (Opsional)</label>
                            <textarea name="description" class="form-control" rows="2"
                                placeholder="Muncul sebagai bantuan teks di bawah inputan warga"></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label>Tipe Input</label>
                            <select name="field_type" class="form-control" id="fieldTypeSelect" required
                                onchange="checkFieldType()">
                                <option value="text">Text Biasa</option>
                                <option value="number">Angka (Number)</option>
                                <option value="date">Tanggal (Date)</option>
                                <option value="textarea">Teks Panjang (Textarea)</option>
                                <option value="select">Pilihan (Select/Dropdown)</option>
                                <option value="file">Upload File (PDF/Image max 1MB)</option>
                                <option value="array">Tabel Dinamis (Banyak Baris)</option>
                                <option value="break">Pemisah Bagian (Break/Section)</option>
                            </select>
                        </div>
                        <div class="form-group mb-3" id="fieldOptionsDiv" style="display:none;">
                            <label>Opsi Pilihan ATAU Kolom Tabel (Pisahkan dengan koma)</label>
                            <input type="text" name="field_options" class="form-control"
                                placeholder="Contoh: PNS, Wiraswasta ATAU Nama, NIK, Hubungan">
                            <small class="text-muted">Hanya untuk tipe 'Pilihan' atau 'Tabel Dinamis'</small>
                        </div>
                        <div class="form-group mb-3">
                            <label>Wajib Diisi?</label>
                            <select name="is_required" class="form-control">
                                <option value="1">Ya</option>
                                <option value="0">Tidak</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            onclick="$('#addFieldModal').modal('hide')">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan Field</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Field -->
    <div class="modal fade" id="editFieldModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editFieldForm" action="" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Field</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label>Label Field</label>
                            <input type="text" name="field_name" id="edit_field_name" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Deskripsi/Penjelasan (Opsional)</label>
                            <textarea name="description" id="edit_description" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label>Tipe Input</label>
                            <select name="field_type" class="form-control" id="edit_fieldTypeSelect" required
                                onchange="checkEditFieldType()">
                                <option value="text">Text Biasa</option>
                                <option value="number">Angka (Number)</option>
                                <option value="date">Tanggal (Date)</option>
                                <option value="textarea">Teks Panjang (Textarea)</option>
                                <option value="select">Pilihan (Select/Dropdown)</option>
                                <option value="file">Upload File (PDF/Image max 1MB)</option>
                                <option value="array">Tabel Dinamis (Banyak Baris)</option>
                                <option value="break">Pemisah Bagian (Break/Section)</option>
                            </select>
                        </div>
                        <div class="form-group mb-3" id="edit_fieldOptionsDiv" style="display:none;">
                            <label>Opsi Pilihan / Kolom Tabel</label>
                            <input type="text" name="field_options" id="edit_field_options" class="form-control"
                                placeholder="Contoh: PNS, Wiraswasta ATAU Nama, NIK, Hubungan">
                        </div>
                        <div class="form-group mb-3">
                            <label>Wajib Diisi?</label>
                            <select name="is_required" id="edit_is_required" class="form-control">
                                <option value="1">Ya</option>
                                <option value="0">Tidak</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Update Field</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function checkFieldType() {
            var type = document.getElementById('fieldTypeSelect').value;
            if (type === 'select' || type === 'array') {
                document.getElementById('fieldOptionsDiv').style.display = 'block';
            } else {
                document.getElementById('fieldOptionsDiv').style.display = 'none';
            }
        }

        function checkEditFieldType() {
            var type = document.getElementById('edit_fieldTypeSelect').value;
            if (type === 'select' || type === 'array') {
                document.getElementById('edit_fieldOptionsDiv').style.display = 'block';
            } else {
                document.getElementById('edit_fieldOptionsDiv').style.display = 'none';
            }
        }

        function openEditFieldModal(id, name, type, description, options, required) {
            document.getElementById('editFieldForm').action = '/{{ admin_path() }}/surat-types/fields/' + id + '/update';
            document.getElementById('edit_field_name').value = name;
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_fieldTypeSelect').value = type;
            document.getElementById('edit_field_options').value = options;
            document.getElementById('edit_is_required').value = required ? 1 : 0;
            checkEditFieldType();
            $('#editFieldModal').modal('show');
        }
    </script>
@endsection
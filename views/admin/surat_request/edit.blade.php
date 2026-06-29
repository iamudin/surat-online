@extends('cms::backend.layout.app', ['title' => 'Edit Data Permohonan'])
@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Edit Data Pengajuan: {{ $req->ticket_number }}</h4>
                <a href="{{ route('surat-requests.show', $req->id) }}" class="btn btn-secondary btn-sm">Kembali</a>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                
                <form action="{{ route('surat-requests.update', $req->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="update_type" value="data">
                    
                    @foreach($req->type->fields as $field)
                        @php $data = $req->data->where('surat_field_id', $field->id)->first(); @endphp
                        @if($field->field_type == 'break')
                            <div class="form-group mb-4 mt-5">
                                <h5 class="font-weight-bold border-bottom pb-2"><i class="fa fa-folder-open text-primary mr-2"></i>{{ $field->field_name }}</h5>
                            </div>
                        @elseif($data)
                        <div class="form-group mb-4">
                            <label class="font-weight-bold">{{ $field->field_name }}</label>
                            
                            @if($field->field_type == 'file')
                                <div class="mt-2">
                                    @if($data->file_path)
                                        <a href="{{ url($data->file_path) }}" target="_blank" class="btn btn-sm btn-info"><i class="fa fa-paperclip"></i> Lihat Lampiran Saat Ini</a>
                                    @else
                                        <span class="text-muted">Belum ada lampiran.</span>
                                    @endif
                                    <small class="d-block text-danger mt-1">Admin tidak dapat mengubah file lampiran. Silakan tolak permohonan jika file salah.</small>
                                </div>
                            @elseif($field->field_type == 'array')
                                @php
                                    $arrayData = json_decode($data->field_value, true) ?? [];
                                @endphp
                                @if(count($arrayData) > 0)
                                    <div class="table-responsive mt-2">
                                        <table class="table table-sm table-bordered">
                                            <thead class="bg-light">
                                                <tr>
                                                    @foreach(array_keys($arrayData[0]) as $colName)
                                                        <th>{{ $colName }}</th>
                                                    @endforeach
                                                    <th width="50" class="text-center"><i class="fa fa-cog"></i></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($arrayData as $index => $row)
                                                    <tr class="array-row">
                                                        @foreach($row as $colName => $val)
                                                            <td>
                                                                <input type="text" name="field_data[{{ $data->id }}][{{ $index }}][{{ $colName }}]" value="{{ $val }}" class="form-control form-control-sm">
                                                            </td>
                                                        @endforeach
                                                        <td class="text-center align-middle">
                                                            <button type="button" class="btn btn-sm btn-danger btn-remove-row" title="Hapus Baris"><i class="fa fa-trash"></i></button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <span class="text-muted d-block mt-2">- Kosong -</span>
                                @endif
                            @else
                                <input type="text" name="field_data[{{ $data->id }}]" value="{{ $data->field_value }}" class="form-control mt-2">
                            @endif
                        </div>
                        @endif
                    @endforeach

                    <hr>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan Perubahan Data</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('click', function (e) {
        let removeBtn = e.target.closest('.btn-remove-row');
        if (removeBtn) {
            if(confirm('Hapus baris ini?')) {
                removeBtn.closest('.array-row').remove();
            }
        }
    });
</script>
@endsection

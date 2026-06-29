@extends('cms::backend.layout.app', ['title' => 'Daftar Permohonan Surat'])

@section('title', 'Edit Data RT')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Edit Data RT</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('rts.update', $rt->id) }}" method="POST">
                @csrf
                @method('PUT')

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="form-group">
                    <label>Pilih RW <span class="text-danger">*</span></label>
                    <select name="rw_id" class="form-control" required>
                        <option value="">-- Pilih RW --</option>
                        @foreach($rws as $rw)
                            <option value="{{ $rw->id }}" {{ old('rw_id', $rt->rw_id) == $rw->id ? 'selected' : '' }}>RW
                                {{ $rw->nomor_rw }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Nomor RT <span class="text-danger">*</span></label>
                    <input type="text" name="nomor_rt" class="form-control" value="{{ old('nomor_rt', $rt->nomor_rt) }}"
                        required>
                </div>

                <div class="form-group">
                    <label>Nama Ketua RT (Opsional)</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $rt->name) }}">
                </div>

                <hr>
                <h6 class="font-weight-bold">Akses Login RT</h6>

                <div class="form-group">
                    <label>Username <span class="text-danger">*</span></label>
                    <input type="text" name="username" class="form-control" value="{{ old('username', $rt->username) }}"
                        required>
                </div>

                <div class="form-group">
                    <label>Password (Biarkan kosong jika tidak ingin mengubah password)</label>
                    <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter">
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Update RT</button>
                    <a href="{{ route('rts.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection

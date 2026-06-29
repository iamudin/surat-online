@extends('cms::backend.layout.app', ['title' => 'Daftar Permohonan Surat'])


@section('title', 'Edit Data RW')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Edit Data RW</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('rws.update', $rw->id) }}" method="POST">
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
                    <label>Nomor RW <span class="text-danger">*</span></label>
                    <input type="text" name="nomor_rw" class="form-control" value="{{ old('nomor_rw', $rw->nomor_rw) }}"
                        required>
                </div>

                <div class="form-group">
                    <label>Nama Ketua RW (Opsional)</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $rw->name) }}">
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Update RW</button>
                    <a href="{{ route('rws.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection

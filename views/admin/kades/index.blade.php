@extends('cms::backend.layout.app', ['title' => 'Data Kepala Desa'])

@section('title', 'Data Kepala Desa')

@section('content')
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('rws.index') }}">Data RW</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('rts.index') }}">Data RT</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="{{ route('admin.kades') }}">Data Kepala Desa</a>
        </li>
    </ul>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Kredensial Kepala Desa</h6>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('admin.kades') }}" method="POST">
                @csrf
                <div class="form-group mb-3">
                    <label>Nama Lengkap (Kepala Desa) <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control" value="{{ old('nama', $data['nama'] ?? '') }}" required>
                </div>
                
                <div class="form-group mb-3">
                    <label>NIP (Jika ASN)</label>
                    <input type="text" name="nip" class="form-control" value="{{ old('nip', $data['nip'] ?? '') }}">
                </div>

                <div class="form-group mb-3">
                    <label>NIK (Username Login Portal) <span class="text-danger">*</span></label>
                    <input type="text" name="nik" class="form-control" value="{{ old('nik', $data['nik'] ?? '') }}" required>
                    <small class="text-muted">Gunakan NIK ini sebagai Username untuk login di Portal Surat Online.</small>
                </div>

                <div class="form-group mb-4">
                    <label>Kata Sandi (Passphrase TTE) @if(isset($data['password'])) <small class="text-success">(Sudah Diatur)</small> @else <span class="text-danger">*</span> @endif</label>
                    <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah sandi" @if(!isset($data['password'])) required @endif>
                    <small class="text-muted">Sandi ini digunakan saat login dan sebagai <b>Passphrase</b> saat melakukan Tanda Tangan Elektronik.</small>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan Data</button>
            </form>
        </div>
    </div>
@endsection

@extends('cms::backend.layout.app', ['title' => 'Daftar Permohonan Surat'])

@section('title', 'Data RW')

@section('content')
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link active" href="{{ route('rws.index') }}">Data RW</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('rts.index') }}">Data RT</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.kades') }}">Data Kepala Desa</a>
        </li>
    </ul>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Kelola Data RW</h6>
            <a href="{{ route('rws.create') }}" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Tambah RW</a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            @if(config('modules.multisite_enabled') && is_main_domain())
                                <th>Tenant</th>
                            @endif
                            <th>Nomor RW</th>
                            <th>Nama Ketua RW</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rws as $rw)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                @if(config('modules.multisite_enabled') && is_main_domain())
                                    <td>{{ $rw->tenant->domain ?? '-' }}</td>
                                @endif
                                <td>{{ $rw->nomor_rw }}</td>
                                <td>{{ $rw->name ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('rws.edit', $rw->id) }}" class="btn btn-warning btn-sm"><i
                                            class="fa fa-edit"></i></a>
                                    @if($rw->rts()->count() == 0 && $rw->wargas()->count() == 0)
                                    <form action="{{ route('rws.destroy', $rw->id) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Yakin ingin menghapus RW ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach

                        @if($rws->isEmpty())
                            <tr>
                                <td colspan="5" class="text-center">Belum ada data RW.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

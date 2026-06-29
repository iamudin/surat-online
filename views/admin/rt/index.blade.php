@extends('cms::backend.layout.app', ['title' => 'Daftar Permohonan Surat'])


@section('title', 'Data RT')

@section('content')
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('rws.index') }}">Data RW</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="{{ route('rts.index') }}">Data RT</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.kades') }}">Data Kepala Desa</a>
        </li>
    </ul>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Kelola Data RT</h6>
            <a href="{{ route('rts.create') }}" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Tambah RT</a>
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
                            <th>RW</th>
                            <th>Nomor RT</th>
                            <th>Nama Ketua RT</th>
                            <th>Username Login</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rts as $rt)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                @if(config('modules.multisite_enabled') && is_main_domain())
                                    <td>{{ $rt->tenant->domain ?? '-' }}</td>
                                @endif
                                <td>{{ $rt->rw->nomor_rw ?? '-' }}</td>
                                <td>{{ $rt->nomor_rt }}</td>
                                <td>{{ $rt->name ?? '-' }}</td>
                                <td><code>{{ $rt->username }}</code></td>
                                <td>
                                    <a href="{{ route('rts.edit', $rt->id) }}" class="btn btn-warning btn-sm"><i
                                            class="fa fa-edit"></i></a>
                                    @if($rt->wargas()->count() == 0)
                                    <form action="{{ route('rts.destroy', $rt->id) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Yakin ingin menghapus RT ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach

                        @if($rts->isEmpty())
                            <tr>
                                <td colspan="7" class="text-center">Belum ada data RT.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

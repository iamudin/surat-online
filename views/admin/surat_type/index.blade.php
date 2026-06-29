@extends('cms::backend.layout.app', ['title' => 'Jenis Surat'])
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Daftar Jenis Surat</h4>
                <a href="{{ route('surat-types.create') }}" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Tambah</a>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                @if(config('modules.multisite_enabled') && is_main_domain())
                                    <th>Tenant</th>
                                @endif
                                <th>Nama Surat</th>
                                <th>Slug</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($types as $key => $type)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                @if(config('modules.multisite_enabled') && is_main_domain())
                                    <td>{{ $type->tenant->domain ?? '-' }}</td>
                                @endif
                                <td>{{ $type->name }}</td>
                                <td>{{ $type->slug }}</td>
                                <td>
                                    @if($type->is_active)
                                        <span class="badge badge-success">Aktif</span>
                                    @else
                                        <span class="badge badge-danger">Tidak Aktif</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('surat-types.edit', $type->id) }}" class="btn btn-info btn-sm">Edit & Form</a>
                                    @if($type->requests()->count() == 0)
                                    <form action="{{ route('surat-types.destroy', $type->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada jenis surat</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

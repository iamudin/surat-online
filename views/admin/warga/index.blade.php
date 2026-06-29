@extends('cms::backend.layout.app', ['title' => 'Data Warga'])


@section('content')
    <div class="row">
        <div class="col-12">
            <h3 style="font-weight:normal;"><i class="fa fa-users aria-hidden=" true"></i> Kelola Data Warga
            </h3>
            <br>
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <table id="wargaTable" class="table table-bordered table-striped bg-white" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>NIK</th>
                        <th>Nama Lengkap</th>
                        @if(config('modules.multisite_enabled') && is_main_domain())
                            <th>Tenant</th>
                        @endif
                        <th>No. HP</th>
                        <th>RW / RT</th>
                        <th>Foto KTP</th>
                        <th>Status Verifikasi</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#wargaTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('wargas.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'nik', name: 'nik' },
                    { data: 'name', name: 'name' },
                    @if(config('modules.multisite_enabled') && is_main_domain())
                    { data: 'tenant', name: 'tenant' },
                    @endif
                    { data: 'phone', name: 'phone' },
                    { data: 'rw_rt', name: 'rw_rt', searchable: false, orderable: false },
                    { data: 'ktp', name: 'ktp', searchable: false, orderable: false },
                    { data: 'status', name: 'status', searchable: false, orderable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ], responsive: true

            });
        });
    </script>

@endpush
@push('styles')
    {{datatable_asset('style')}}
@endpush
@push('scripts')
    {{datatable_asset('js')}}

@endpush

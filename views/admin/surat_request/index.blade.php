@extends('cms::backend.layout.app', ['title' => 'Daftar Permohonan Surat'])
@section('content')
    <style>
        .stat-card {
            cursor: pointer;
            transition: transform 0.2s, opacity 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        .stat-card.inactive {
            opacity: 0.5;
        }
    </style>
    <div class="row mb-4">
        <div class="col-12">
            <h3 style="font-weight:normal;"><i class="fa fa-envelope aria-hidden=" true"></i> Daftar Permohonan Surat
            </h3>
            <br>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white shadow stat-card" id="card-all" onclick="filterStatus('all')">
                <div class="card-body">
                    <div class="font-weight-bold text-uppercase mb-1">Total Permohonan</div>
                    <div class="h3 mb-0 font-weight-bold">{{ number_format($stats['total']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white shadow stat-card inactive" id="card-pending"
                onclick="filterStatus('pending')">
                <div class="card-body">
                    <div class="font-weight-bold text-uppercase mb-1">Menunggu</div>
                    <div class="h3 mb-0 font-weight-bold">{{ number_format($stats['pending']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white shadow stat-card inactive" id="card-processing"
                onclick="filterStatus('processing')">
                <div class="card-body">
                    <div class="font-weight-bold text-uppercase mb-1">Diproses</div>
                    <div class="h3 mb-0 font-weight-bold">{{ number_format($stats['processing']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white shadow stat-card inactive" id="card-approved"
                onclick="filterStatus('approved')">
                <div class="card-body">
                    <div class="font-weight-bold text-uppercase mb-1">Disetujui</div>
                    <div class="h3 mb-0 font-weight-bold">{{ number_format($stats['approved']) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">

                    <form action="{{ route('surat-requests.report') }}" method="GET" target="_blank" class="form-inline">
                        <input type="hidden" name="status" id="printStatus" value="all">
                        <div class="input-group input-group-sm mr-2">
                            <div class="input-group-prepend"><span class="input-group-text">Awal</span></div>
                            <input type="date" name="start_date" id="start_date" class="form-control" required
                                value="{{ date('Y-m-01') }}" onchange="$('#suratTable').DataTable().ajax.reload();">
                        </div>
                        <div class="input-group input-group-sm mr-2">
                            <div class="input-group-prepend"><span class="input-group-text">Akhir</span></div>
                            <input type="date" name="end_date" id="end_date" class="form-control" required
                                value="{{ date('Y-m-t') }}" onchange="$('#suratTable').DataTable().ajax.reload();">
                        </div>
                        <button type="submit" class="btn btn-sm btn-secondary shadow-sm"><i
                                class="fa fa-print fa-sm text-white-50"></i> Cetak Laporan</button>
                    </form>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    <div class="table-responsive">
                        <table id="suratTable" class="table table-bordered table-striped bg-white" width="100%">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    @if(config('modules.multisite_enabled') && is_main_domain())
                                        <th>Tenant</th>
                                    @endif
                                    <th>No. Tiket</th>
                                    <th>Waktu Pengajuan</th>
                                    <th>Jenis Surat</th>
                                    <th>Pemohon (NIK)</th>
                                    <th>Status</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
@endsection

    @push('scripts')
        <script>
            var currentStatus = 'all';

            function filterStatus(status) {
                currentStatus = status;

                // Update active card styling
                $('.stat-card').addClass('inactive');
                $('#card-' + status).removeClass('inactive');

                // Update print form status
                $('#printStatus').val(status);

                // Reload DataTable
                $('#suratTable').DataTable().ajax.reload();
            }

            $(document).ready(function () {
                $('#suratTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('surat-requests.index') }}",
                        data: function (d) {
                            d.status = currentStatus;
                            d.start_date = $('#start_date').val();
                            d.end_date = $('#end_date').val();
                        }
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        @if(config('modules.multisite_enabled') && is_main_domain())
                        { data: 'tenant', name: 'tenant' },
                        @endif
                        { data: 'ticket', name: 'ticket_number' },
                        { data: 'created_at', name: 'created_at' },
                        { data: 'surat_type', name: 'type.name', searchable: false, orderable: false },
                        { data: 'pemohon', name: 'warga.name', searchable: false, orderable: false },
                        { data: 'status', name: 'status', searchable: false, orderable: false },
                        { data: 'action', name: 'action', orderable: false, searchable: false }
                    ],
                    responsive: true,

                });
            });
        </script>
        @push('styles')
            {{datatable_asset('style')}}
        @endpush
        @push('scripts')
            {{datatable_asset('js')}}
        @endpush
    @endpush

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Permohonan Surat - {{ date('d/m/Y') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .mb-1 { margin-bottom: 5px; }
        .mb-2 { margin-bottom: 10px; }
        .mb-4 { margin-bottom: 20px; }
        .mt-4 { margin-top: 20px; }
        
        /* Kop Surat */
        .kop-surat {
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .kop-surat h2, .kop-surat h3, .kop-surat p {
            margin: 2px 0;
        }
        
        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        
        /* Print Specific */
        @media print {
            body {
                padding: 0;
                font-size: 11px;
            }
            .no-print {
                display: none;
            }
            @page { margin: 1.5cm; }
        }
    </style>
</head>
<body onload="window.print()">

    <!-- Print Button (Hidden on Print) -->
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 15px; background: #4e73df; color: white; border: none; border-radius: 5px; cursor: pointer;">
            Cetak Laporan
        </button>
    </div>

    <!-- Kop Laporan -->
    <div class="kop-surat text-center">
        <h2>PEMERINTAH DESA</h2>
        <h3>LAPORAN REKAPITULASI PERMOHONAN SURAT</h3>
        <p>Sistem Layanan Administrasi Desa Digital (SuratDesa)</p>
    </div>

    <div class="mb-4 text-center">
        <strong>PERIODE:</strong> {{ \Carbon\Carbon::parse($start_date)->format('d F Y') }} s/d {{ \Carbon\Carbon::parse($end_date)->format('d F Y') }}
        <br>
        <strong>STATUS:</strong> 
        @if($status == 'pending') Menunggu Validasi
        @elseif($status == 'processing') Sedang Diproses
        @elseif($status == 'approved') Selesai / Disetujui
        @elseif($status == 'rejected') Ditolak
        @else Semua Status
        @endif
    </div>

    <!-- Data Table -->
    <table>
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                @if(config('modules.multisite_enabled') && is_main_domain())
                    <th width="15%">Tenant</th>
                @endif
                <th width="15%">No. Tiket</th>
                <th width="15%">Waktu Pengajuan</th>
                <th width="20%">Jenis Surat</th>
                <th width="20%">Pemohon (NIK)</th>
                <th width="15%" class="text-center">Status</th>
                <th width="10%">Catatan RT</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $req)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    @if(config('modules.multisite_enabled') && is_main_domain())
                        <td>{{ $req->tenant->domain ?? '-' }}</td>
                    @endif
                    <td><code>{{ $req->ticket_number }}</code></td>
                    <td>{{ $req->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $req->type->name ?? '-' }}</td>
                    <td>
                        {{ $req->warga->name ?? '-' }}<br>
                        <small>{{ $req->warga->nik ?? '-' }}</small>
                    </td>
                    <td class="text-center">
                        @if($req->status == 'pending')
                            Menunggu
                        @elseif($req->status == 'processing')
                            Diproses
                        @elseif($req->status == 'approved')
                            Disetujui
                        @else
                            Ditolak
                        @endif
                    </td>
                    <td>{{ $req->catatan_rt ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    @php $colspan = config('modules.multisite_enabled') && is_main_domain() ? 8 : 7; @endphp
                    <td colspan="{{ $colspan }}" class="text-center">Belum ada data permohonan untuk filter ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Tanda Tangan Section -->
    <div style="margin-top: 50px; display: flex; justify-content: flex-end;">
        <div class="text-center" style="width: 250px;">
            <p>Dicetak pada: {{ date('d F Y') }}</p>
            <p class="mb-4">Admin Desa,</p>
            <br><br><br>
            <p><strong>_____________________</strong></p>
        </div>
    </div>

</body>
</html>

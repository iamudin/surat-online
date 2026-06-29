<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Surat - {{ $req->ticket_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { background: white; margin: 0; padding: 0; }
            .no-print { display: none !important; }
            .print-area { box-shadow: none !important; border: none !important; padding: 0 !important; max-width: 100% !important; }
        }
        body { background: #f3f4f6; }
        .print-area { max-width: 210mm; min-height: 297mm; background: white; margin: 2rem auto; padding: 2.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="text-gray-900 font-serif">

    <div class="text-center py-4 no-print bg-white border-b shadow-sm mb-4 sticky top-0 z-50">
        <button onclick="window.print()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium font-sans">
            <svg class="w-5 h-5 inline-block mr-1 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Cetak Surat
        </button>
    </div>

    <div class="print-area relative">
        <!-- Kop Surat -->
        <div class="border-b-4 border-double border-black pb-4 mb-8 text-center flex items-center justify-center space-x-6">
            <!-- Ganti dengan logo desa jika ada -->
            <div class="w-20 h-24 border-2 border-gray-400 flex items-center justify-center text-gray-400 font-sans text-xs">LOGO</div>
            <div class="flex-1">
                <h1 class="text-xl font-bold uppercase tracking-wider">Pemerintah Kabupaten/Kota</h1>
                <h2 class="text-lg font-bold uppercase tracking-wider">Kecamatan</h2>
                <h3 class="text-2xl font-extrabold uppercase tracking-widest mt-1">Desa</h3>
                <p class="text-sm mt-1">Alamat Kantor Desa / Kelurahan, Kode Pos 12345</p>
            </div>
            <div class="w-20 h-24"></div> <!-- Spacer for balance -->
        </div>

        <!-- Judul Surat -->
        <div class="text-center mb-10">
            <h4 class="text-xl font-bold underline uppercase">{{ $req->type->name }}</h4>
            <p class="text-sm mt-1">Nomor Registrasi: {{ $req->ticket_number }}</p>
        </div>

        <!-- Isi Surat -->
        <div class="leading-relaxed mb-6">
            <p class="mb-4 text-justify">Yang bertanda tangan di bawah ini Kepala Desa, menerangkan dengan sesungguhnya bahwa:</p>
            
            <table class="w-full mb-6 ml-4">
                <tr>
                    <td class="w-1/3 py-1 align-top">Nama Lengkap</td>
                    <td class="w-4 py-1 align-top">:</td>
                    <td class="py-1 align-top font-semibold">{{ $req->warga->name }}</td>
                </tr>
                <tr>
                    <td class="py-1 align-top">Nomor Induk Kependudukan (NIK)</td>
                    <td class="py-1 align-top">:</td>
                    <td class="py-1 align-top">{{ $req->warga->nik }}</td>
                </tr>
                <tr>
                    <td class="py-1 align-top">Alamat</td>
                    <td class="py-1 align-top">:</td>
                    <td class="py-1 align-top">{{ $req->warga->address }}</td>
                </tr>
                
                <!-- Data Dinamis dari Form -->
                @foreach($req->type->fields as $field)
                    @php $data = $req->data->where('surat_field_id', $field->id)->first(); @endphp
                    @if($field->field_type == 'break')
                    <tr>
                        <td colspan="3" class="py-2 pt-4">
                            <h4 class="font-bold underline">{{ $field->field_name }}</h4>
                        </td>
                    </tr>
                    @elseif($data && $field->field_type !== 'file' && $field->field_type !== 'array')
                    <tr>
                        <td class="py-1 align-top">{{ $field->field_name }}</td>
                        <td class="py-1 align-top">:</td>
                        <td class="py-1 align-top">{{ $data->field_value }}</td>
                    </tr>
                    @elseif($data && $field->field_type == 'array')
                    <tr>
                        <td colspan="3" class="py-2">
                            <div class="mt-2 mb-2">
                                <p class="font-semibold mb-1">{{ $field->field_name }}:</p>
                                @php
                                    $arrayData = json_decode($data->field_value, true) ?? [];
                                @endphp
                                @if(count($arrayData) > 0)
                                <table class="w-full border-collapse border border-gray-400 text-sm mt-1">
                                    <thead>
                                        <tr>
                                            <th class="border border-gray-400 px-2 py-1 bg-gray-100 w-10 text-center">No.</th>
                                            @foreach(array_keys($arrayData[0]) as $colName)
                                            <th class="border border-gray-400 px-2 py-1 bg-gray-100 text-left">{{ $colName }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($arrayData as $index => $row)
                                        <tr>
                                            <td class="border border-gray-400 px-2 py-1 text-center">{{ $index + 1 }}</td>
                                            @foreach($row as $val)
                                            <td class="border border-gray-400 px-2 py-1">{{ $val }}</td>
                                            @endforeach
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @else
                                <p class="text-gray-500 italic text-sm">- Tidak ada data -</p>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endif
                @endforeach
            </table>

            <p class="text-justify indent-8">
                Demikian surat keterangan ini dibuat dengan sebenarnya dan untuk dipergunakan sebagaimana mestinya. Surat ini sah dan diterbitkan melalui sistem informasi desa online secara elektronik.
            </p>
        </div>

        <!-- Tanda Tangan -->
        <div class="flex justify-end mt-16 text-center">
            <div class="w-64">
                <p class="mb-1">Ditetapkan di: Kantor Desa</p>
                <p class="mb-20">Pada Tanggal: {{ date('d F Y') }}</p>
                
                <p class="font-bold underline">Nama Kepala Desa</p>
                <p>Kepala Desa</p>
            </div>
        </div>
        
        <!-- Watermark / Bukti Sistem -->
        <div class="absolute bottom-8 left-8 text-xs text-gray-400 font-sans border-t border-gray-200 pt-2 w-[80%]">
            Dicetak secara elektronik melalui Sistem Informasi Desa.<br>
            Tiket: {{ $req->ticket_number }} | Waktu Cetak: {{ date('d-m-Y H:i:s') }}
        </div>
    </div>

</body>
</html>

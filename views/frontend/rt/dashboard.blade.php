<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Dasbor RT - Layanan Desa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { background-color: #f3f4f6; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; padding-bottom: 80px; }
    </style>
</head>
<body class="antialiased text-gray-900">

    <!-- Top App Bar -->
    <div class="fixed top-0 left-0 right-0 bg-green-600 text-white shadow-md z-40 px-4 py-4 flex justify-between items-center safe-top">
        <div class="text-lg font-semibold truncate"><i class="fa fa-map-marker-alt mr-2"></i> Dasbor RT {{ $rt->nomor_rt }} / RW {{ $rt->rw->nomor_rw ?? '-' }}</div>
        <div class="flex items-center space-x-3">
            <span class="text-sm font-medium opacity-90 hidden sm:block">Bpk/Ibu {{ session('rt_name') }}</span>
            <form action="{{ plugin_route('portal.logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="bg-green-700 hover:bg-green-800 text-white w-9 h-9 rounded-full flex items-center justify-center transition-colors">
                    <i class="fa fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="pt-24 px-4 max-w-lg mx-auto sm:max-w-4xl pb-10">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl relative mb-6 shadow-sm text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if($unverifiedWargas->count() > 0)
        <div class="mb-8">
            <div class="flex justify-between items-end mb-4">
                <div>
                    <h2 class="text-xl font-bold text-red-600"><i class="fa fa-exclamation-circle"></i> Verifikasi Warga Baru</h2>
                    <p class="text-sm text-gray-500">Ada warga yang menunggu verifikasi KTP</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($unverifiedWargas as $warga)
                <div class="bg-white rounded-2xl shadow-sm border border-red-100 p-4 flex items-start space-x-4">
                    <div class="w-24 h-24 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                        <a href="{{ url($warga->ktp_path) }}" target="_blank">
                            <img src="{{ url($warga->ktp_path) }}" alt="KTP" class="w-full h-full object-cover">
                        </a>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-800">{{ $warga->name }}</h4>
                        <p class="text-xs text-gray-500 mb-1">NIK: {{ $warga->nik }}</p>
                        <p class="text-xs text-gray-500 mb-3 line-clamp-2">{{ $warga->address }}</p>
                        
                        <form action="{{ plugin_route('portal.rt.verify_warga', $warga->id) }}" method="POST" onsubmit="return confirm('Yakin data KTP valid?')">
                            @csrf
                            <button type="submit" class="w-full bg-blue-600 text-white text-xs font-bold px-3 py-2 rounded-lg hover:bg-blue-700 transition"><i class="fa fa-check mr-1"></i> Verifikasi Valid</button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Statistics -->
        <div class="grid grid-cols-2 gap-4 mb-6">
            <a href="{{ plugin_route('portal.dashboard', ['validasi' => 'belum']) }}" class="block bg-white rounded-2xl p-4 border {{ request('validasi') == 'belum' ? 'border-yellow-400 ring-2 ring-yellow-200' : 'border-gray-100 hover:border-yellow-300' }} shadow-sm text-center transition-all">
                <div class="text-sm text-gray-500 mb-1">Belum Divalidasi</div>
                <div class="text-2xl font-bold text-yellow-600">{{ $stats['belum_divalidasi'] }}</div>
            </a>
            <a href="{{ plugin_route('portal.dashboard', ['validasi' => 'sudah']) }}" class="block bg-white rounded-2xl p-4 border {{ request('validasi') == 'sudah' ? 'border-green-400 ring-2 ring-green-200' : 'border-gray-100 hover:border-green-300' }} shadow-sm text-center transition-all">
                <div class="text-sm text-gray-500 mb-1">Sudah Divalidasi</div>
                <div class="text-2xl font-bold text-green-600">{{ $stats['sudah_divalidasi'] }}</div>
            </a>
        </div>

        <div class="mb-6 flex justify-between items-end">
            <div>
                <h2 class="text-xl font-bold text-gray-800">
                    Permohonan Surat Warga
                    @if(request('validasi'))
                        <span class="text-sm font-normal text-gray-500">({{ request('validasi') == 'sudah' ? 'Sudah Divalidasi' : 'Belum Divalidasi' }})</span>
                    @endif
                </h2>
                <p class="text-sm text-gray-500">Daftar permohonan surat dari warga RT {{ $rt->nomor_rt }}</p>
            </div>
            @if(request('validasi'))
                <a href="{{ plugin_route('portal.dashboard') }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium bg-blue-50 px-3 py-1 rounded-full"><i class="fa fa-sync-alt mr-1"></i>Tampilkan Semua</a>
            @endif
        </div>

        <div class="space-y-4">
            @forelse($requests as $req)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 relative overflow-hidden">
                    @if($req->is_rt_approved)
                        <div class="absolute top-0 right-0 bg-green-100 text-green-700 px-3 py-1 text-xs font-bold rounded-bl-xl">
                            <i class="fa fa-check-circle mr-1"></i> Telah Divalidasi (Valid)
                        </div>
                    @elseif($req->status == 'rejected')
                        <div class="absolute top-0 right-0 bg-red-100 text-red-700 px-3 py-1 text-xs font-bold rounded-bl-xl">
                            <i class="fa fa-times-circle mr-1"></i> Telah Divalidasi (Ditolak)
                        </div>
                    @else
                        <div class="absolute top-0 right-0 bg-yellow-100 text-yellow-700 px-3 py-1 text-xs font-bold rounded-bl-xl">
                            <i class="fa fa-clock mr-1"></i> Menunggu Validasi
                        </div>
                    @endif

                    <div class="mt-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-xs font-mono text-gray-500 bg-gray-100 px-2 py-1 rounded-md">{{ $req->ticket_number }}</span>
                            <span class="text-xs text-gray-500"><i class="fa fa-calendar-alt mr-1"></i> {{ $req->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        
                        <h3 class="text-lg font-bold text-gray-800 mb-2">{{ $req->type->name ?? 'Surat' }}</h3>
                        
                        <div class="bg-gray-50 p-3 rounded-xl text-sm border border-gray-100 mb-4">
                            <p class="font-semibold text-gray-800">{{ $req->warga->name }}</p>
                            <p class="text-gray-600 text-xs mt-1">NIK: {{ $req->warga->nik }}</p>
                            <p class="text-gray-600 text-xs">Alamat: {{ $req->warga->address }}</p>
                        </div>

                        @if($req->admin_notes)
                            <div class="bg-blue-50 rounded-xl p-3 text-sm text-blue-800 border border-blue-100 mb-4">
                                <span class="font-semibold text-xs text-blue-600 block mb-1">Catatan Admin:</span>
                                {{ $req->admin_notes }}
                            </div>
                        @endif

                        @if($req->catatan_rt)
                            <div class="bg-red-50 rounded-xl p-3 text-sm text-red-800 border border-red-100 mb-4">
                                <span class="font-semibold text-xs text-red-600 block mb-1">Catatan RT:</span>
                                {{ $req->catatan_rt }}
                            </div>
                        @endif

                        <div class="flex justify-between items-center">
                            <div>
                                @if($req->status == 'pending')
                                    <span class="bg-gray-100 text-gray-600 text-xs font-semibold px-2 py-1 rounded-lg">Status Desa: Menunggu</span>
                                @elseif($req->status == 'processing')
                                    <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-1 rounded-lg">Status Desa: Diproses</span>
                                @elseif($req->status == 'approved')
                                    <span class="bg-green-100 text-green-800 text-xs font-semibold px-2 py-1 rounded-lg">Status Desa: Selesai</span>
                                @else
                                    <span class="bg-red-100 text-red-800 text-xs font-semibold px-2 py-1 rounded-lg">Status Desa: Ditolak</span>
                                @endif
                            </div>

                            <div class="flex items-center space-x-2">
                                <button type="button" onclick="openDetailModal({{ $req->id }})" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium px-4 py-2 rounded-xl transition-colors shadow-sm">
                                    <i class="fa fa-eye mr-1"></i> Detail
                                </button>
                                @if(!$req->is_rt_approved && $req->status == 'pending')
                                    <div class="flex gap-2">
                                        <button type="button" onclick="openValidateModal({{ $req->id }}, true)" class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2 rounded-xl transition-colors shadow-sm">
                                            <i class="fa fa-check mr-1"></i> Valid
                                        </button>
                                        <button type="button" onclick="openValidateModal({{ $req->id }}, false)" class="bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-4 py-2 rounded-xl transition-colors shadow-sm">
                                            <i class="fa fa-times mr-1"></i> Tolak
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Hidden detail content -->
                        <div id="detail_content_{{ $req->id }}" class="hidden">
                            <div class="space-y-4">
                                @foreach($req->type->fields as $field)
                                    @php $data = $req->data->where('surat_field_id', $field->id)->first(); @endphp
                                    @if($field->field_type == 'break')
                                        <div class="pt-2">
                                            <h4 class="text-sm font-bold text-blue-800 border-b pb-1"><i class="fa fa-folder-open mr-2"></i>{{ $field->field_name }}</h4>
                                        </div>
                                    @elseif($data)
                                    <div>
                                        <h4 class="text-xs font-semibold text-gray-500 mb-1">{{ $field->field_name }}</h4>
                                        <div class="text-sm text-gray-800 bg-gray-50 p-3 rounded-lg border border-gray-100">
                                            @if($field->field_type == 'file' && $data->file_path)
                                                <a href="{{ media($data->file_path)->url() }}" target="_blank" class="text-blue-600 hover:underline"><i class="fa fa-paperclip mr-1"></i> Lihat Lampiran</a>
                                            @elseif($field->field_type == 'array')
                                                @php $arr = json_decode($data->field_value, true) ?? []; @endphp
                                                @if(count($arr) > 0)
                                                    <div class="overflow-x-auto">
                                                    <table class="min-w-full text-xs">
                                                        <thead>
                                                            <tr class="border-b">
                                                                @foreach(array_keys($arr[0]) as $col)
                                                                    <th class="py-1 pr-3 text-left font-semibold text-gray-600">{{ $col }}</th>
                                                                @endforeach
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($arr as $row)
                                                                <tr class="border-b last:border-0">
                                                                    @foreach($row as $val)
                                                                        <td class="py-1 pr-3">{{ $val }}</td>
                                                                    @endforeach
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                    </div>
                                                @else
                                                    <span class="text-gray-400 italic">Kosong</span>
                                                @endif
                                            @else
                                                {{ $data->field_value }}
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10 text-center">
                    <div class="w-16 h-16 mx-auto bg-gray-50 text-gray-300 rounded-full flex items-center justify-center mb-4">
                        <i class="fa fa-inbox text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-1">Belum Ada Permohonan</h3>
                    <p class="text-gray-500 text-sm">Saat ini belum ada warga yang mengajukan surat.</p>
                </div>
            @endforelse
        </div>
        
        <div class="mt-6">
            {{ $requests->withQueryString()->links('pagination::tailwind') }}
        </div>
    </div>
    </div>

    <!-- Modal Detail Pengajuan -->
    <div id="detailModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75" aria-hidden="true" onclick="closeDetailModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="relative inline-block w-full xl:max-w-[40%] overflow-hidden text-left align-bottom transition-all transform bg-white rounded-t-3xl sm:rounded-3xl sm:my-8 sm:align-middle shadow-xl">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex justify-between items-center mb-4 border-b pb-3">
                        <h3 class="text-lg font-bold leading-6 text-gray-900">Detail Pengajuan</h3>
                        <button type="button" onclick="closeDetailModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-full text-sm p-1.5 ml-auto inline-flex items-center">
                            <i class="fa fa-times text-xl"></i>
                        </button>
                    </div>
                    <div id="detailModalBody" class="mt-2 max-h-[70vh] overflow-y-auto px-1 hide-scrollbar">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Validasi -->
    <div id="validateModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75" aria-hidden="true" onclick="closeValidateModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="relative inline-block w-full max-w-md overflow-hidden text-left align-bottom transition-all transform bg-white rounded-t-3xl sm:rounded-3xl sm:my-8 sm:align-middle shadow-xl">
                <form id="validateForm" method="POST" action="">
                    @csrf
                    <input type="hidden" name="is_valid" id="is_valid_input" value="1">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex justify-between items-center mb-4 border-b pb-3">
                            <h3 class="text-lg font-bold leading-6 text-gray-900" id="validateModalTitle">Validasi Pengajuan</h3>
                            <button type="button" onclick="closeValidateModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-full text-sm p-1.5 ml-auto inline-flex items-center">
                                <i class="fa fa-times text-xl"></i>
                            </button>
                        </div>
                        <div class="mt-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan RT <span id="catatanRequired" class="text-red-500 hidden">*</span></label>
                            <textarea name="catatan_rt" id="catatan_rt" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500" placeholder="Tambahkan catatan..."></textarea>
                            <p class="text-xs text-gray-500 mt-1" id="catatanHelpText">Opsional jika Valid, Wajib jika Ditolak.</p>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                        <button type="submit" id="btnSubmitValidate" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Konfirmasi</button>
                        <button type="button" onclick="closeValidateModal()" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openValidateModal(id, isValid) {
            let form = document.getElementById('validateForm');
            form.action = '{{ plugin_route("portal.rt.surat_validate", ["id" => "__ID__"]) }}'.replace('__ID__', id);
            document.getElementById('is_valid_input').value = isValid ? 1 : 0;
            document.getElementById('catatan_rt').value = '';
            
            let btn = document.getElementById('btnSubmitValidate');
            let title = document.getElementById('validateModalTitle');
            
            if(isValid) {
                btn.className = "w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none sm:ml-3 sm:w-auto sm:text-sm bg-green-600 hover:bg-green-700";
                btn.innerHTML = "Tandai Valid";
                title.innerHTML = "Validasi Pengajuan";
                document.getElementById('catatan_rt').required = false;
                document.getElementById('catatanRequired').classList.add('hidden');
            } else {
                btn.className = "w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none sm:ml-3 sm:w-auto sm:text-sm bg-red-600 hover:bg-red-700";
                btn.innerHTML = "Tolak Pengajuan";
                title.innerHTML = "Tolak Pengajuan";
                document.getElementById('catatan_rt').required = true;
                document.getElementById('catatanRequired').classList.remove('hidden');
            }
            
            document.getElementById('validateModal').classList.remove('hidden');
        }

        function closeValidateModal() {
            document.getElementById('validateModal').classList.add('hidden');
        }

        function openDetailModal(id) {
            let content = document.getElementById('detail_content_' + id).innerHTML;
            document.getElementById('detailModalBody').innerHTML = content;
            document.getElementById('detailModal').classList.remove('hidden');
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }
    </script>
</body>
</html>

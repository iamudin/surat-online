<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Dashboard Layanan Warga</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #f3f4f6;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            padding-bottom: 80px;
        }

        /* Hide scrollbar for horizontal scroll */
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="antialiased text-gray-900">

    <!-- Top App Bar -->
    <div
        class="fixed top-0 left-0 right-0 bg-blue-600 text-white shadow-md z-40 px-4 py-4 flex justify-between items-center safe-top">
        <div class="text-lg font-semibold truncate"><i class="fa fa-university mr-2"></i> Desa Online</div>
        <div class="flex items-center space-x-3">
            <span class="text-sm font-medium opacity-90 hidden sm:block">Halo, {{ session('warga_name') }}</span>

            <form action="{{ route('portal.logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-white hover:text-gray-200 focus:outline-none">
                    <i class="fa fa-sign-out-alt text-xl"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="pt-20 px-4 max-w-lg mx-auto sm:max-w-2xl lg:max-w-4xl pb-10">

        @if(session('success'))
            <div
                class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl relative mb-6 shadow-sm text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl relative mb-6 shadow-sm text-sm">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(!$warga->is_verified)
            <div class="mb-8 bg-yellow-50 border border-yellow-200 rounded-2xl p-6 shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0 mt-1">
                        <i class="fa fa-exclamation-triangle text-yellow-500 text-2xl"></i>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-lg font-bold text-yellow-800">Akun Belum Terverifikasi</h3>
                        <p class="text-sm text-yellow-700 mt-1">Anda harus memverifikasi data kependudukan (KTP) terlebih
                            dahulu sebelum dapat mengajukan permohonan surat.</p>

                        @if(empty($warga->ktp_path))
                            <div class="mt-4 bg-white p-4 rounded-xl border border-yellow-100">
                                <h4 class="font-semibold text-gray-800 mb-2">Upload Foto KTP</h4>
                                <form action="{{ route('portal.warga.upload_ktp') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <input type="file" name="ktp_image" accept="image/jpeg,image/png"
                                        class="mb-3 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                        required>
                                    <button type="submit"
                                        class="bg-blue-600 text-white px-4 py-2 rounded-xl text-sm hover:bg-blue-700 font-medium"><i
                                            class="fa fa-upload mr-2"></i>Upload KTP</button>
                                </form>
                            </div>
                        @else
                            <div class="mt-4 bg-blue-50 p-4 rounded-xl border border-blue-100 flex items-center">
                                <i class="fa fa-clock text-blue-500 text-2xl mr-3"></i>
                                <div>
                                    <h4 class="font-semibold text-blue-800">Menunggu Verifikasi RT</h4>
                                    <p class="text-xs text-blue-600 mt-1">Foto KTP Anda sedang dalam proses verifikasi oleh
                                        RT/Admin. Harap bersabar.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <!-- Profile Card -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 mb-8 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-bl-full -z-10"></div>
                
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-2xl font-bold border-4 border-white shadow-sm">
                        {{ substr($warga->name, 0, 1) }}
                    </div>
                    <div class="flex-1">
                        <h2 class="text-xl font-bold text-gray-800">{{ $warga->name }}</h2>
                        <p class="text-sm text-gray-500">NIK: {{ $warga->nik }}</p>
                        <div class="mt-2 text-xs text-gray-600 flex flex-wrap gap-2">
                            <span class="inline-flex items-center bg-gray-100 px-2 py-1 rounded-md"><i class="fa fa-map-marker-alt text-gray-400 mr-1"></i> RT {{ $warga->rt?->nomor_rt }} / RW {{ $warga->rw?->nomor_rw }}</span>
                            <span class="inline-flex items-center bg-gray-100 px-2 py-1 rounded-md"><i class="fa fa-home text-gray-400 mr-1"></i> {{ $warga->address }}</span>
                            @if($warga->is_verified && $warga->ktp_path)
                                <a href="{{ url($warga->ktp_path) }}" target="_blank" class="inline-flex items-center bg-blue-100 text-blue-700 px-2 py-1 rounded-md hover:bg-blue-200 transition-colors"><i class="fa fa-id-card mr-1"></i> Lihat KTP</a>
                            @endif
                            <span class="inline-flex items-center bg-green-100 text-green-700 px-2 py-1 rounded-md"><i class="fa fa-check-circle mr-1"></i> Terverifikasi</span>
                        </div>
                    </div>
                </div>
                
                <button onclick="openProfileModal()" class="absolute top-4 right-4 text-blue-600 bg-blue-50 hover:bg-blue-100 w-10 h-10 rounded-full flex items-center justify-center transition-colors" title="Edit Profil">
                    <i class="fa fa-pencil-alt"></i>
                </button>
            </div>

            <!-- Statistics -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
                <a href="{{ route('portal.dashboard', ['status' => 'pending']) }}" class="block bg-white rounded-2xl p-4 border {{ request('status') == 'pending' ? 'border-yellow-400 ring-2 ring-yellow-200' : 'border-gray-100 hover:border-yellow-300' }} shadow-sm text-center transition-all">
                    <div class="text-sm text-gray-500 mb-1">Menunggu</div>
                    <div class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</div>
                </a>
                <a href="{{ route('portal.dashboard', ['status' => 'processing']) }}" class="block bg-white rounded-2xl p-4 border {{ request('status') == 'processing' ? 'border-blue-400 ring-2 ring-blue-200' : 'border-gray-100 hover:border-blue-300' }} shadow-sm text-center transition-all">
                    <div class="text-sm text-gray-500 mb-1">Diproses</div>
                    <div class="text-2xl font-bold text-blue-600">{{ $stats['processing'] }}</div>
                </a>
                <a href="{{ route('portal.dashboard', ['status' => 'approved']) }}" class="block bg-white rounded-2xl p-4 border {{ request('status') == 'approved' ? 'border-green-400 ring-2 ring-green-200' : 'border-gray-100 hover:border-green-300' }} shadow-sm text-center transition-all">
                    <div class="text-sm text-gray-500 mb-1">Selesai</div>
                    <div class="text-2xl font-bold text-green-600">{{ $stats['approved'] }}</div>
                </a>
                <a href="{{ route('portal.dashboard', ['status' => 'rejected']) }}" class="block bg-white rounded-2xl p-4 border {{ request('status') == 'rejected' ? 'border-red-400 ring-2 ring-red-200' : 'border-gray-100 hover:border-red-300' }} shadow-sm text-center transition-all">
                    <div class="text-sm text-gray-500 mb-1">Ditolak</div>
                    <div class="text-2xl font-bold text-red-600">{{ $stats['rejected'] }}</div>
                </a>
            </div>

            <div class="mb-8 flex justify-center">
                <button onclick="openLayananModal()" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-2xl shadow-lg hover:shadow-xl transition-all flex items-center justify-center space-x-3 text-md">
                    <i class="fa fa-plus-circle text-2xl"></i>
                    <span>Buat Permohonan Baru</span>
                </button>
            </div>
        @endif

        <!-- Riwayat Pengajuan -->
        <div>
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-gray-800">
                    Riwayat Pengajuan 
                    @if(request('status')) 
                        <span class="text-sm font-normal text-gray-500">({{ ucfirst(request('status')) }})</span>
                    @endif
                </h2>
                @if(request('status'))
                    <a href="{{ route('portal.dashboard') }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium bg-blue-50 px-3 py-1 rounded-full"><i class="fa fa-sync-alt mr-1"></i>Tampilkan Semua</a>
                @endif
            </div>

            <div class="space-y-4">
                @forelse($requests as $req)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <span
                                    class="text-xs font-mono text-gray-500 bg-gray-100 px-2 py-1 rounded-md">{{ $req->ticket_number }}</span>
                            </div>
                            <div>
                                @if($req->status == 'pending')
                                    <span
                                        class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">Menunggu</span>
                                @elseif($req->status == 'processing')
                                    <span
                                        class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">Diproses</span>
                                @elseif($req->status == 'approved')
                                    <span
                                        class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">Selesai</span>
                                @else
                                    <span
                                        class="bg-red-100 text-red-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">Ditolak</span>
                                @endif
                            </div>
                        </div>
                        <h3 class="text-base font-bold text-gray-800 mb-1">{{ $req->type?->name }}</h3>
                        <p class="text-xs text-gray-500 mb-3"><i class="fa fa-clock mr-1"></i>
                            {{ $req->created_at->format('d M Y, H:i') }}</p>

                        @if($req->admin_notes)
                            <div class="bg-gray-50 rounded-xl p-3 text-sm text-gray-700 border border-gray-100 mb-3">
                                <span class="font-semibold text-xs text-gray-500 block mb-1">Catatan Admin:</span>
                                {{ $req->admin_notes }}
                            </div>
                        @endif
                        @if($req->catatan_rt)
                            <div class="bg-red-50 rounded-xl p-3 text-sm text-red-700 border border-red-100 mb-3">
                                <span class="font-semibold text-xs text-red-500 block mb-1">Catatan RT:</span>
                                {{ $req->catatan_rt }}
                            </div>
                        @endif

                        <div class="mt-3 flex justify-end space-x-2">
                            <button type="button" onclick="openDetailModal({{ $req->id }})" class="inline-flex items-center px-4 py-2 bg-gray-50 text-gray-600 text-sm font-medium rounded-xl border border-gray-200 hover:bg-gray-100 transition-colors">
                                <i class="fa fa-eye mr-2"></i> Detail
                            </button>
                            @if($req->status == 'approved')
                                <a href="{{ route('warga.surat.download', $req->id) }}" target="_blank"
                                    class="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-600 text-sm font-medium rounded-xl border border-blue-100 hover:bg-blue-100 transition-colors">
                                    <i class="fa fa-download mr-2"></i> Download / Cetak
                                </a>
                            @endif
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
                                                <a href="{{ url($data->file_path) }}" target="_blank" class="text-blue-600 hover:underline"><i class="fa fa-paperclip mr-1"></i> Lihat Lampiran</a>
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
                @empty
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center">
                        <div
                            class="w-16 h-16 mx-auto bg-gray-50 text-gray-300 rounded-full flex items-center justify-center mb-3">
                            <i class="fa fa-box-open text-2xl"></i>
                        </div>
                        <p class="text-gray-500 text-sm">Belum ada riwayat permohonan surat.</p>
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

    <!-- Modal Pilihan Layanan -->
    <div id="layananModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75" aria-hidden="true" onclick="closeLayananModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="relative inline-block w-full sm:max-w-3xl overflow-hidden text-left align-bottom transition-all transform bg-white rounded-t-3xl sm:rounded-3xl sm:my-8 sm:align-middle shadow-xl">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex justify-between items-center mb-4 border-b pb-3">
                        <h3 class="text-lg font-bold leading-6 text-gray-900">Pilih Layanan Permohonan</h3>
                        <button type="button" onclick="closeLayananModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-full text-sm p-1.5 ml-auto inline-flex items-center">
                            <i class="fa fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="mt-4 max-h-[70vh] overflow-y-auto px-1 hide-scrollbar">
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 pb-4">
                            @foreach($suratTypes as $type)
                                <a href="{{ route('warga.form.get', $type->slug) }}" class="block bg-white rounded-2xl shadow-sm border border-gray-100 p-4 text-center cursor-pointer active:scale-95 transition-transform hover:shadow-md hover:border-blue-200">
                                    <div class="w-12 h-12 mx-auto bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mb-3">
                                        <i class="fa fa-file-alt text-xl"></i>
                                    </div>
                                    <h3 class="text-sm font-semibold text-gray-800 line-clamp-2 leading-tight">{{ $type->name }}</h3>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Profil -->
    <div id="profileModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75" aria-hidden="true" @if(!$isProfileIncomplete) onclick="closeProfileModal()" @endif></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="relative inline-block w-full xl:max-w-[40%] overflow-hidden text-left align-bottom transition-all transform bg-white rounded-t-3xl sm:rounded-3xl sm:my-8 sm:align-middle shadow-xl">
                <form action="{{ route('portal.warga.update_profile') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex justify-between items-center mb-4 border-b pb-3">
                            <h3 class="text-lg font-bold leading-6 text-gray-900">
                                @if($isProfileIncomplete)
                                    <span class="text-yellow-600"><i class="fa fa-exclamation-triangle mr-2"></i>Lengkapi Profil Anda</span>
                                @else
                                    Edit Profil
                                @endif
                            </h3>
                            @if(!$isProfileIncomplete)
                            <button type="button" onclick="closeProfileModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-full text-sm p-1.5 ml-auto inline-flex items-center">
                                <i class="fa fa-times text-xl"></i>
                            </button>
                            @endif
                        </div>
                        <div class="mt-2 space-y-4 px-1 max-h-[70vh] overflow-y-auto hide-scrollbar">
                            @if(session('success'))
                                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl relative shadow-sm text-sm">
                                    {{ session('success') }}
                                </div>
                            @endif
                            @if($errors->any())
                                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl relative shadow-sm text-sm">
                                    <ul class="list-disc pl-5">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if($isProfileIncomplete)
                                <div class="bg-yellow-50 text-yellow-800 p-3 rounded-lg text-sm mb-4 border border-yellow-200">
                                    Selamat datang! Karena Anda login menggunakan nomor WhatsApp, Anda diwajibkan melengkapi NIK, Nama asli, dan data domisili sebelum dapat menggunakan layanan desa. Data ini akan menunggu verifikasi dari RT.
                                </div>
                            @endif
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">NIK</label>
                                @if($isProfileIncomplete)
                                    <input type="text" name="nik" value="" class="w-full px-4 py-2 border border-yellow-300 rounded-xl focus:ring-blue-500 focus:border-blue-500" required minlength="16" maxlength="16" pattern="\d{16}" placeholder="Masukkan 16 digit NIK Anda">
                                @else
                                    <input type="text" value="{{ $warga->nik }}" class="w-full px-4 py-2 border border-gray-300 rounded-xl bg-gray-100 text-gray-500 cursor-not-allowed focus:outline-none" readonly>
                                @endif
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                                <input type="text" name="name" value="{{ $warga->name }}" required class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor HP</label>
                                @if($isProfileIncomplete)
                                    <input type="text" name="phone" value="{{ $warga->phone }}" class="w-full px-4 py-2 border border-gray-300 rounded-xl bg-gray-100 text-gray-500 cursor-not-allowed focus:outline-none" readonly>
                                    <p class="text-xs text-gray-500 mt-1"><i class="fa fa-info-circle mr-1"></i>Nomor HP bawaan dari WhatsApp tidak dapat diubah.</p>
                                @else
                                    <input type="text" name="phone" value="{{ $warga->phone }}" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500">
                                @endif
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                                <textarea name="address" required class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500" rows="3">{{ $warga->address }}</textarea>
                            </div>
                            <div class="flex space-x-4">
                                @if($isProfileIncomplete)
                                    <div class="flex-1">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">RW</label>
                                        <select name="rw_id" id="rw_id" class="w-full px-4 py-2 border border-yellow-300 rounded-xl focus:ring-blue-500 focus:border-blue-500" required>
                                            <option value="">-- Pilih RW --</option>
                                            @foreach($rws as $rw)
                                                <option value="{{ $rw->id }}">RW {{ $rw->nomor_rw }} - {{ $rw->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="flex-1">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">RT</label>
                                        <select name="rt_id" id="rt_id" class="w-full px-4 py-2 border border-yellow-300 rounded-xl focus:ring-blue-500 focus:border-blue-500" required disabled>
                                            <option value="">-- Pilih RW terlebih dahulu --</option>
                                        </select>
                                    </div>
                                @else
                                    <div class="flex-1">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">RT</label>
                                        <input type="text" value="{{ $warga->rt?->nomor_rt }} - {{ $warga->rt?->name }}" class="w-full px-4 py-2 border border-gray-300 rounded-xl bg-gray-100 text-gray-500 cursor-not-allowed focus:outline-none" readonly>
                                    </div>
                                    <div class="flex-1">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">RW</label>
                                        <input type="text" value="{{ $warga->rw?->nomor_rw }} - {{ $warga->rw?->name }}" class="w-full px-4 py-2 border border-gray-300 rounded-xl bg-gray-100 text-gray-500 cursor-not-allowed focus:outline-none" readonly>
                                    </div>
                                @endif
                            </div>
                            <div class="bg-blue-50 p-3 rounded-xl border border-blue-100 mt-2">
                                <label class="block text-sm font-medium text-blue-800 mb-1">Set Password (opsional)</label>
                                <input type="password" name="password" placeholder="Bisa dipakai untuk login nanti selain WhatsApp" class="w-full px-4 py-2 border border-blue-200 rounded-xl focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            @if(!$isProfileIncomplete)
                                <p class="text-xs text-gray-500"><i class="fa fa-info-circle mr-1"></i>NIK, RT, dan RW tidak dapat diubah secara mandiri. Silakan hubungi Admin desa jika terdapat kesalahan.</p>
                            @endif
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t">
                        <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Simpan Perubahan</button>
                        @if(!$isProfileIncomplete)
                            <button type="button" onclick="closeProfileModal()" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Batal</button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
        </div>
    </div>

    @if($isProfileIncomplete)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            openProfileModal();

            // Setup RT dropdown loading
            const rwSelect = document.getElementById('rw_id');
            const rtSelect = document.getElementById('rt_id');
            
            const rwData = @json($rws);
            
            if (rwSelect) {
                rwSelect.addEventListener('change', function() {
                    const rwId = this.value;
                    if (rwId) {
                        rtSelect.innerHTML = '<option value="">Memuat RT...</option>';
                        rtSelect.disabled = true;
                        
                        let selectedRw = rwData.find(rw => rw.id == rwId);
                        if (selectedRw && selectedRw.rts && selectedRw.rts.length > 0) {
                            rtSelect.innerHTML = '<option value="">-- Pilih RT --</option>';
                            selectedRw.rts.forEach(rt => {
                                rtSelect.innerHTML += `<option value="${rt.id}">RT ${rt.nomor_rt}</option>`;
                            });
                            rtSelect.disabled = false;
                        } else {
                            rtSelect.innerHTML = '<option value="">Data RT tidak tersedia</option>';
                        }
                    } else {
                        rtSelect.innerHTML = '<option value="">-- Pilih RW terlebih dahulu --</option>';
                        rtSelect.disabled = true;
                    }
                });
            }
        });
    </script>
    @endif

    <script>
        function openLayananModal() {
            document.getElementById('layananModal').classList.remove('hidden');
        }

        function closeLayananModal() {
            document.getElementById('layananModal').classList.add('hidden');
        }

        function openDetailModal(id) {
            let content = document.getElementById('detail_content_' + id).innerHTML;
            document.getElementById('detailModalBody').innerHTML = content;
            document.getElementById('detailModal').classList.remove('hidden');
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }

        function openProfileModal() {
            document.getElementById('profileModal').classList.remove('hidden');
        }

        function closeProfileModal() {
            document.getElementById('profileModal').classList.add('hidden');
        }

        // Handle AJAX Submit
        document.addEventListener('submit', function (e) {
            if (e.target && e.target.id === 'dynamicSuratForm') {
                e.preventDefault();
                let form = e.target;
                let submitBtn = form.querySelector('button[type="submit"]');
                let originalBtnText = submitBtn.innerHTML;

                // Cek file size (Max 1MB)
                let files = form.querySelectorAll('input[type="file"]');
                let isFileSizeValid = true;
                files.forEach(function (fileInput) {
                    if (fileInput.files.length > 0) {
                        let sizeMB = fileInput.files[0].size / 1024 / 1024;
                        if (sizeMB > 1) {
                            isFileSizeValid = false;
                        }
                    }
                });

                if (!isFileSizeValid) {
                    Swal.fire('Peringatan', 'Ukuran file maksimal 1MB per file.', 'warning');
                    return;
                }

                submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin mr-2"></i> Memproses...';
                submitBtn.disabled = true;

                let formData = new FormData(form);

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => response.json().then(data => ({ status: response.status, body: data })))
                    .then(res => {
                        if (res.status === 200 && res.body.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                html: 'Permohonan berhasil dikirim.<br><br><span class="text-sm text-gray-500">Nomor Tiket:</span><br><b class="text-xl text-blue-600 tracking-wider">' + res.body.ticket + '</b>',
                                icon: 'success',
                                confirmButtonText: 'Tutup',
                                customClass: {
                                    confirmButton: 'bg-blue-600 text-white rounded-xl px-6 py-2'
                                }
                            }).then(() => {
                                window.location.reload();
                            });
                        } else if (res.status === 422) {
                            // Validation errors
                            let errors = res.body.errors;
                            let errorMsg = '<ul class="text-left text-sm space-y-1">';
                            for (let key in errors) {
                                errorMsg += '<li><i class="fa fa-times-circle text-red-500 mr-1"></i> ' + errors[key][0] + '</li>';
                            }
                            errorMsg += '</ul>';
                            Swal.fire({ title: 'Validasi Gagal', html: errorMsg, icon: 'error' });
                            submitBtn.innerHTML = originalBtnText;
                            submitBtn.disabled = false;
                        } else {
                            Swal.fire('Error', res.body.message || 'Terjadi kesalahan sistem.', 'error');
                            submitBtn.innerHTML = originalBtnText;
                            submitBtn.disabled = false;
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error', 'Gagal terhubung ke server.', 'error');
                        submitBtn.innerHTML = originalBtnText;
                        submitBtn.disabled = false;
                    });
            }
        });

        // Handle dynamic array rows via event delegation
        document.addEventListener('click', function (e) {
            let addBtn = e.target.closest('.btn-add-row');
            if (addBtn) {
                let fieldId = addBtn.getAttribute('data-field-id');
                let columnsStr = addBtn.getAttribute('data-columns');
                let isRequired = addBtn.getAttribute('data-required') === '1' ? 'required' : '';

                let columns = [];
                if (columnsStr) {
                    try { columns = JSON.parse(columnsStr); } catch (e) { }
                }

                let tbody = document.getElementById('tbody_array_' + fieldId);
                let newIndex = Date.now(); // unique index

                let divRow = document.createElement('div');
                divRow.className = 'bg-white p-3 rounded-lg border border-gray-200 relative array-row';

                let html = '<button type="button" class="absolute top-2 right-2 text-red-400 hover:text-red-600 p-1 btn-remove-row" title="Hapus"><i class="fa fa-times"></i></button>';
                html += '<div class="space-y-3 sm:space-y-0 sm:flex sm:gap-3 pr-6">';
                columns.forEach(function (col) {
                    html += `<div class="flex-1">
                                <label class="block text-xs text-gray-500 mb-1">${col}</label>
                                <input type="text" name="field_${fieldId}[${newIndex}][${col}]" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-blue-500" ${isRequired}>
                             </div>`;
                });
                html += '</div>';

                divRow.innerHTML = html;
                tbody.appendChild(divRow);
            }

            let removeBtn = e.target.closest('.btn-remove-row');
            if (removeBtn) {
                removeBtn.closest('.array-row').remove();
            }
        });
    </script>
</body>

</html>

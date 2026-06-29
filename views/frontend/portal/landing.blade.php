<!DOCTYPE html>
<html lang="id">

<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }

        .glass {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .hero-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%233b82f6' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .animate-fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        .animate-slide-up {
            animation: slideUp 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body class="text-gray-800 antialiased min-h-screen flex flex-col">

    <!-- Navigation -->
    <nav class="glass fixed w-full z-50 border-b border-gray-100 shadow-sm transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-500/30">
                            <i class="fa fa-envelope-open-text text-xl"></i>
                        </div>
                        <span class="font-bold text-xl tracking-tight text-gray-900">Surat<span
                                class="text-blue-600">Online</span></span>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    @if(session('warga_id') || session('kades_id') || session('rt_id'))
                        <a href="{{ route('portal.dashboard') }}"
                            class="text-gray-600 hover:text-blue-600 font-medium px-3 py-2 transition-colors">Dashboard</a>
                    @else
                        <a href="{{ route('portal.login') }}"
                            class="text-gray-600 hover:text-blue-600 font-medium px-3 py-2 transition-colors hidden sm:block">Masuk</a>
                        <a href="{{ route('warga.register') }}"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-medium transition-all shadow-md shadow-blue-500/20 hover:shadow-lg hover:shadow-blue-500/40 transform hover:-translate-y-0.5">Daftar
                            Akun</a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section with Tracking -->
    <div class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden hero-pattern">
        <div class="absolute inset-0 bg-gradient-to-b from-blue-50/50 to-white/90 pointer-events-none"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center animate-fade-in">
            <span
                class="inline-block py-1 px-3 rounded-full bg-blue-100 text-blue-700 font-semibold text-sm mb-6 border border-blue-200 shadow-sm">
                Layanan Administrasi Desa Digital
            </span>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-gray-900 tracking-tight mb-6 leading-tight">
                Urus Surat Pengantar <br class="hidden sm:block" />
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">Lebih Cepat &
                    Mudah</span>
            </h1>
            <p class="mt-4 max-w-2xl text-lg text-gray-600 mx-auto mb-10">
                Lacak status permohonan surat Anda secara real-time atau buat permohonan baru tanpa harus datang ke
                balai desa.
            </p>

            <!-- Tracking Form -->
            <div
                class="max-w-xl mx-auto bg-white p-3 rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 mb-12 animate-slide-up">
                <form action="{{ route('portal.landing') }}" method="GET" class="flex items-center">
                    <div class="relative flex-grow">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fa fa-search text-gray-400"></i>
                        </div>
                        <input type="text" name="ticket" value="{{ request('ticket') }}" required
                            placeholder="Masukkan Nomor Tiket (Contoh: SRQ-2023...)"
                            class="block w-full pl-11 pr-4 py-4 border-0 text-gray-900 rounded-xl ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 bg-gray-50/50 focus:bg-white transition-all">
                    </div>
                    <button type="submit"
                        class="ml-3 bg-gray-900 hover:bg-gray-800 text-white px-6 py-4 rounded-xl font-semibold transition-colors shadow-md">
                        Lacak
                    </button>
                </form>
            </div>

            <!-- Tracking Result -->
            @if(request()->filled('ticket'))
                <div class="max-w-2xl mx-auto text-left animate-slide-up">
                    @if($trackResult)
                        <div
                            class="bg-white rounded-3xl p-8 border border-gray-100 shadow-xl shadow-gray-200/40 relative overflow-hidden">
                            <div
                                class="absolute top-0 left-0 w-2 h-full 
                                                        {{ $trackResult->status == 'pending' ? 'bg-yellow-400' : ($trackResult->status == 'processing' ? 'bg-blue-400' : ($trackResult->status == 'approved' ? 'bg-green-400' : 'bg-red-400')) }}">
                            </div>

                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                                <div>
                                    <p class="text-sm font-semibold text-gray-500 mb-1">Status Tiket <span
                                            class="font-mono bg-gray-100 px-2 py-0.5 rounded text-gray-700 ml-2">{{ $trackResult->ticket_number }}</span>
                                    </p>
                                    <h3 class="text-2xl font-bold text-gray-900">{{ $trackResult->type->name }}</h3>
                                </div>
                                <div class="mt-4 sm:mt-0">
                                    @if($trackResult->status == 'pending')
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800">
                                            <span class="w-2 h-2 rounded-full bg-yellow-500 mr-2 animate-pulse"></span> Menunggu
                                            Validasi
                                        </span>
                                    @elseif($trackResult->status == 'processing')
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">
                                            <span class="w-2 h-2 rounded-full bg-blue-500 mr-2 animate-pulse"></span> Sedang
                                            Diproses
                                        </span>
                                    @elseif($trackResult->status == 'approved')
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                                            <i class="fa fa-check-circle mr-1.5"></i> Selesai
                                        </span>
                                    @elseif($trackResult->status == 'rejected')
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800">
                                            <i class="fa fa-times-circle mr-1.5"></i> Ditolak
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                                    <p class="text-xs text-gray-500 font-medium mb-1">Tanggal Pengajuan</p>
                                    <p class="text-sm font-semibold text-gray-900"><i
                                            class="fa fa-calendar-alt text-gray-400 mr-2"></i>{{ $trackResult->created_at->format('d F Y, H:i') }}
                                    </p>
                                </div>
                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                                    <p class="text-xs text-gray-500 font-medium mb-1">Pemohon</p>
                                    <p class="text-sm font-semibold text-gray-900"><i
                                            class="fa fa-user text-gray-400 mr-2"></i>{{ $trackResult->warga->name }}</p>
                                </div>
                            </div>

                            @if($trackResult->admin_notes || $trackResult->catatan_rt)
                                <div class="space-y-3">
                                    @if($trackResult->catatan_rt)
                                        <div class="bg-red-50/50 border border-red-100 rounded-xl p-4 flex gap-3 items-start">
                                            <i class="fa fa-exclamation-circle text-red-500 mt-0.5"></i>
                                            <div>
                                                <h4 class="text-sm font-bold text-red-800 mb-1">Catatan RT:</h4>
                                                <p class="text-sm text-red-700">{{ $trackResult->catatan_rt }}</p>
                                            </div>
                                        </div>
                                    @endif
                                    @if($trackResult->admin_notes)
                                        <div class="bg-blue-50/50 border border-blue-100 rounded-xl p-4 flex gap-3 items-start">
                                            <i class="fa fa-info-circle text-blue-500 mt-0.5"></i>
                                            <div>
                                                <h4 class="text-sm font-bold text-blue-800 mb-1">Catatan Admin Desa:</h4>
                                                <p class="text-sm text-blue-700">{{ $trackResult->admin_notes }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="bg-red-50 rounded-2xl p-6 text-center border border-red-100">
                            <div
                                class="w-12 h-12 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fa fa-search text-xl"></i>
                            </div>
                            <h3 class="text-lg font-bold text-red-800 mb-1">Tiket Tidak Ditemukan</h3>
                            <p class="text-red-600 text-sm">Maaf, permohonan dengan nomor tiket
                                <strong>{{ request('ticket') }}</strong> tidak ditemukan dalam sistem.
                            </p>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Statistics Section -->
    <div class="bg-white py-16 border-y border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Statistik Pelayanan</h2>
                <p class="text-gray-500 mt-2">Transparansi data pengajuan surat oleh warga</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <!-- Total -->
                <div
                    class="bg-gray-50 rounded-2xl p-6 border border-gray-100 text-center hover:shadow-md transition-shadow">
                    <div
                        class="w-12 h-12 bg-gray-200 text-gray-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="fa fa-file-alt text-xl"></i>
                    </div>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
                    <p class="text-sm font-medium text-gray-500 mt-1">Total Pengajuan</p>
                </div>

                <!-- Pending -->
                <div
                    class="bg-yellow-50 rounded-2xl p-6 border border-yellow-100 text-center hover:shadow-md transition-shadow">
                    <div
                        class="w-12 h-12 bg-yellow-200 text-yellow-700 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="fa fa-clock text-xl"></i>
                    </div>
                    <p class="text-3xl font-bold text-yellow-800">{{ number_format($stats['pending']) }}</p>
                    <p class="text-sm font-medium text-yellow-600 mt-1">Menunggu Validasi</p>
                </div>

                <!-- Processing -->
                <div
                    class="bg-blue-50 rounded-2xl p-6 border border-blue-100 text-center hover:shadow-md transition-shadow">
                    <div
                        class="w-12 h-12 bg-blue-200 text-blue-700 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="fa fa-cog text-xl"></i>
                    </div>
                    <p class="text-3xl font-bold text-blue-800">{{ number_format($stats['processing']) }}</p>
                    <p class="text-sm font-medium text-blue-600 mt-1">Sedang Diproses</p>
                </div>

                <!-- Approved -->
                <div
                    class="bg-green-50 rounded-2xl p-6 border border-green-100 text-center hover:shadow-md transition-shadow">
                    <div
                        class="w-12 h-12 bg-green-200 text-green-700 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="fa fa-check text-xl"></i>
                    </div>
                    <p class="text-3xl font-bold text-green-800">{{ number_format($stats['approved']) }}</p>
                    <p class="text-sm font-medium text-green-600 mt-1">Selesai / Disetujui</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Services Section -->
    <div class="bg-gray-50 py-20 flex-grow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-end mb-10">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Layanan Surat Tersedia</h2>
                    <p class="text-gray-500 mt-2">Pilih jenis surat yang ingin Anda ajukan</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($services as $service)
                    <div
                        class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 group">
                        <div
                            class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-5 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                            <i class="fa fa-file-signature text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $service->name }}</h3>
                        <p class="text-sm text-gray-500 mb-6 line-clamp-2">Layanan pengajuan {{ $service->name }} secara
                            online untuk warga.</p>

                        <a href="{{ route('warga.form.get', $service->slug) }}"
                            class="inline-flex items-center text-sm font-semibold text-blue-600 group-hover:text-blue-700">
                            Ajukan Sekarang <i
                                class="fa fa-arrow-right ml-2 transform group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                @empty
                    <div class="col-span-full bg-white p-10 rounded-2xl text-center border border-gray-100">
                        <i class="fa fa-exclamation-triangle text-3xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">Belum ada layanan surat yang diaktifkan.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-100 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-sm text-gray-500 font-medium">
                &copy; {{ date('Y') }} SuratDesa. Hak Cipta Dilindungi.<br>
                Sistem Informasi Pelayanan Masyarakat
            </p>
        </div>
    </footer>

</body>

</html>
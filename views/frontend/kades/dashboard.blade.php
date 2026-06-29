<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Dashboard Kepala Desa - TTE Surat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { background-color: #f3f4f6; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; padding-bottom: 80px; }
    </style>
</head>
<body class="antialiased text-gray-900">

    <!-- Top App Bar -->
    <div class="fixed top-0 left-0 right-0 bg-indigo-600 text-white shadow-md z-40 px-4 py-4 flex justify-between items-center safe-top">
        <div class="text-lg font-semibold truncate"><i class="fa fa-user-tie mr-2"></i> Dasbor Kepala Desa</div>
        <div class="flex items-center space-x-3">
            <span class="text-sm font-medium opacity-90 hidden sm:block">Halo, {{ session('kades_name') }}</span>
            <form action="{{ route('portal.logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="bg-indigo-700 hover:bg-indigo-800 text-white w-9 h-9 rounded-full flex items-center justify-center transition-colors">
                    <i class="fa fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="pt-24 px-4 max-w-lg mx-auto sm:max-w-4xl pb-10">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Tanda Tangan Elektronik (TTE)</h1>
            <p class="text-gray-600">Daftar permohonan surat yang menunggu tanda tangan Anda.</p>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl relative mb-6 shadow-sm text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl relative mb-6 shadow-sm text-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="mb-6 grid grid-cols-2 gap-4">
            <a href="?tab=waiting" class="rounded-2xl p-4 flex flex-col items-center justify-center text-center transition-all duration-200 {{ $statusTab == 'waiting' ? 'bg-indigo-100 border-2 border-indigo-400 shadow-md transform scale-[1.02]' : 'bg-indigo-50 border border-indigo-100 hover:bg-indigo-100/50' }}">
                <span class="text-3xl font-bold text-indigo-700 mb-1">{{ $stats['waiting'] }}</span>
                <span class="text-xs font-medium text-indigo-600 uppercase tracking-wide">Menunggu TTE</span>
            </a>
            <a href="?tab=signed" class="rounded-2xl p-4 flex flex-col items-center justify-center text-center transition-all duration-200 {{ $statusTab == 'signed' ? 'bg-green-100 border-2 border-green-400 shadow-md transform scale-[1.02]' : 'bg-green-50 border border-green-100 hover:bg-green-100/50' }}">
                <span class="text-3xl font-bold text-green-700 mb-1">{{ $stats['signed'] }}</span>
                <span class="text-xs font-medium text-green-600 uppercase tracking-wide">Selesai (Di-TTE)</span>
            </a>
        </div>

        @if($requests->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($requests as $req)
            <div class="bg-white rounded-2xl shadow-sm border border-indigo-100 p-4 flex flex-col space-y-4">
                <div class="flex justify-between items-start border-b border-gray-100 pb-3">
                    <div>
                        <span class="inline-block px-2 py-1 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-lg mb-1">Tiket: {{ $req->ticket_number }}</span>
                        <h3 class="text-lg font-bold text-gray-800">{{ $req->type->name ?? '-' }}</h3>
                        <p class="text-xs text-gray-500"><i class="fa fa-clock mr-1"></i> {{ $req->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                
                <div class="flex-1">
                    <p class="text-sm text-gray-600 mb-1"><strong>Pemohon:</strong> {{ $req->warga->name ?? '-' }}</p>
                    <p class="text-sm text-gray-600 mb-2"><strong>NIK:</strong> {{ $req->warga->nik ?? '-' }}</p>
                </div>

                <div class="pt-3 border-t border-gray-100 space-y-2">
                    @if($req->is_signed)
                        <div class="w-full text-center py-2 px-4 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm font-medium mb-2">
                            <i class="fa fa-check-circle mr-1"></i> Telah di-TTE
                        </div>
                        @if($req->signed_pdf_path)
                        <a href="{{ url($req->signed_pdf_path) }}" target="_blank" class="w-full inline-flex justify-center items-center px-4 py-2 border border-indigo-200 text-sm font-medium rounded-xl text-indigo-700 bg-indigo-50 hover:bg-indigo-100 focus:outline-none transition-colors">
                            <i class="fa fa-file-pdf mr-2"></i> Lihat Surat (TTE)
                        </a>
                        @endif
                    @else
                        @if($req->pdf_path)
                        <a href="{{ url($req->pdf_path) }}" target="_blank" class="w-full inline-flex justify-center items-center px-4 py-2 border border-indigo-200 text-sm font-medium rounded-xl text-indigo-700 bg-indigo-50 hover:bg-indigo-100 focus:outline-none transition-colors">
                            <i class="fa fa-file-pdf mr-2"></i> Lihat Surat (PDF)
                        </a>
                        @endif
                        <button onclick="openTteModal({{ $req->id }}, '{{ $req->ticket_number }}', '{{ $req->warga->name ?? '-' }}')" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-xl shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none transition-colors">
                            <i class="fa fa-pen-nib mr-2"></i> Sign (TTE)
                        </button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        
        @if($requests->hasPages())
        <div class="mt-6">
            {{ $requests->withQueryString()->links('pagination::tailwind') }}
        </div>
        @endif
        
        @else
        <div class="bg-white rounded-2xl shadow-sm p-8 text-center border border-gray-100">
            <i class="fa fa-check-circle text-5xl text-gray-300 mb-4 block"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-1">Semua Selesai!</h3>
            <p class="text-gray-500">Tidak ada permohonan surat yang menunggu tanda tangan saat ini.</p>
        </div>
        @endif
    </div>

    <!-- TTE Modal -->
    <div id="tteModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeTteModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fa fa-shield-alt text-indigo-600 text-xl"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Autentikasi Tanda Tangan Elektronik
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 mb-4">
                                    Menandatangani surat untuk <strong class="text-gray-800" id="modal-pemohon"></strong> (Tiket: <span class="font-mono text-indigo-600" id="modal-tiket"></span>).
                                </p>
                                <form id="tteForm" onsubmit="submitTte(event)">
                                    <input type="hidden" id="surat_id" name="surat_id">
                                    <div>
                                        <label for="passphrase" class="block text-sm font-medium text-gray-700 mb-1">Passphrase</label>
                                        <input type="password" name="passphrase" id="passphrase" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-shadow" required placeholder="Masukkan kata sandi / passphrase">
                                    </div>
                                    <div id="tteError" class="mt-3 text-sm text-red-600 hidden bg-red-50 p-2 rounded-lg border border-red-100"></div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                    <button type="button" onclick="submitTte(event)" id="btnSubmitTte" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        <i class="fa fa-signature mr-2 mt-1"></i> Tandatangani
                    </button>
                    <button type="button" onclick="closeTteModal()" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openTteModal(id, ticket, pemohon) {
            document.getElementById('surat_id').value = id;
            document.getElementById('modal-tiket').innerText = ticket;
            document.getElementById('modal-pemohon').innerText = pemohon;
            document.getElementById('passphrase').value = '';
            document.getElementById('tteError').classList.add('hidden');
            document.getElementById('tteModal').classList.remove('hidden');
        }

        function closeTteModal() {
            document.getElementById('tteModal').classList.add('hidden');
        }

        function submitTte(e) {
            if(e) e.preventDefault();
            
            const form = document.getElementById('tteForm');
            if(!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const btn = document.getElementById('btnSubmitTte');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin mr-2"></i> Memproses...';
            
            const errorDiv = document.getElementById('tteError');
            errorDiv.classList.add('hidden');

            fetch("{{ route('portal.kades.sign_surat') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    surat_id: document.getElementById('surat_id').value,
                    passphrase: document.getElementById('passphrase').value
                })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        confirmButtonColor: '#4f46e5'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    errorDiv.innerText = data.message;
                    errorDiv.classList.remove('hidden');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa fa-signature mr-2"></i> Tandatangani';
                }
            })
            .catch(error => {
                errorDiv.innerText = 'Terjadi kesalahan sistem.';
                errorDiv.classList.remove('hidden');
                btn.disabled = false;
                btn.innerHTML = '<i class="fa fa-signature mr-2"></i> Tandatangani';
            });
        }
    </script>
</body>
</html>

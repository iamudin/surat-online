<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Daftar Akun Warga - Layanan Desa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f3f4f6; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }
    </style>
</head>
<body class="antialiased text-gray-900 min-h-screen flex flex-col justify-center py-6 sm:py-12">
    <div class="relative py-3 sm:max-w-md sm:mx-auto w-full px-4 sm:px-0">
        <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-blue-600 shadow-lg transform -skew-y-6 sm:skew-y-0 sm:-rotate-6 sm:rounded-3xl hidden sm:block"></div>
        <div class="relative px-6 py-10 bg-white shadow-lg sm:rounded-3xl sm:p-20">
            <div class="max-w-md mx-auto">
                <div>
                    <h1 class="text-2xl font-bold text-center text-blue-600 mb-2">Daftar Akun</h1>
                    <p class="text-gray-500 text-center text-sm mb-8">Lengkapi data diri Anda</p>
                </div>
                
                @if($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 text-sm">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('warga.register.submit') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nomor Induk Kependudukan (NIK)</label>
                        <input type="text" name="nik" class="mt-1 block w-full px-3 py-3 bg-gray-50 border border-gray-300 rounded-xl text-sm shadow-sm placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" value="{{ old('nik') }}" placeholder="16 digit NIK" minlength="16" maxlength="16" pattern="\d{16}" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input type="text" name="name" class="mt-1 block w-full px-3 py-3 bg-gray-50 border border-gray-300 rounded-xl text-sm shadow-sm placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" value="{{ old('name') }}" placeholder="Sesuai KTP" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">No. HP / WhatsApp</label>
                        <input type="number" name="phone" class="mt-1 block w-full px-3 py-3 bg-gray-50 border border-gray-300 rounded-xl text-sm shadow-sm placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Pilih RW</label>
                        <select name="rw_id" id="rw_id" class="mt-1 block w-full px-3 py-3 bg-gray-50 border border-gray-300 rounded-xl text-sm shadow-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required>
                            <option value="">-- Pilih RW --</option>
                            @foreach($rws as $rw)
                                <option value="{{ $rw->id }}" {{ old('rw_id') == $rw->id ? 'selected' : '' }}>RW {{ $rw->nomor_rw }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Pilih RT</label>
                        <select name="rt_id" id="rt_id" class="mt-1 block w-full px-3 py-3 bg-gray-50 border border-gray-300 rounded-xl text-sm shadow-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required disabled>
                            <option value="">-- Pilih RW terlebih dahulu --</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
                        <textarea name="address" class="mt-1 block w-full px-3 py-3 bg-gray-50 border border-gray-300 rounded-xl text-sm shadow-sm placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" rows="2" required>{{ old('address') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" name="password" class="mt-1 block w-full px-3 py-3 bg-gray-50 border border-gray-300 rounded-xl text-sm shadow-sm placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="mt-1 block w-full px-3 py-3 bg-gray-50 border border-gray-300 rounded-xl text-sm shadow-sm placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required>
                    </div>
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors mt-6">
                        Daftar Sekarang
                    </button>
                </form>
                
                <div class="mt-8 text-center">
                    <p class="text-sm text-gray-600">
                        Sudah punya akun? <a href="{{ route('portal.login') }}" class="font-medium text-blue-600 hover:text-blue-500">Masuk di sini</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        const rwData = @json($rws);
        
        document.getElementById('rw_id').addEventListener('change', function() {
            let rwId = this.value;
            let rtSelect = document.getElementById('rt_id');
            
            rtSelect.innerHTML = '<option value="">Memuat RT...</option>';
            rtSelect.disabled = true;

            if(rwId) {
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
            }
        });
    </script>
</body>
</html>

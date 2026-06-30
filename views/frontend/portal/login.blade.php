<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Portal Login - Surat Online</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f3f4f6;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }
    </style>
</head>

<body class="antialiased text-gray-900 min-h-screen flex flex-col justify-center sm:py-12">
    <div class="relative py-3 sm:max-w-md sm:mx-auto w-full px-4 sm:px-0">
        <div
            class="absolute inset-0 bg-gradient-to-r from-blue-400 to-indigo-600 shadow-lg transform -skew-y-6 sm:skew-y-0 sm:-rotate-6 sm:rounded-3xl">
        </div>
        <div class="relative px-6 py-10 bg-white shadow-lg sm:rounded-3xl sm:p-20">
            <div class="max-w-md mx-auto">
                <div>
                    <h1 class="text-2xl font-bold text-center text-indigo-600 mb-2">Portal Surat Online</h1>
                    <p class="text-gray-500 text-center text-sm mb-8">Login Warga & RT</p>
                </div>

                @if(session('success'))
                    <div
                        class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 text-sm">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Standard Login Form -->
                <div id="standardLoginForm">
                    <form action="{{ plugin_route('portal.login.submit') }}" method="POST" class="space-y-6">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700">NIK (Warga) / Username (RT)</label>
                            <input type="text" name="username_or_nik"
                                class="mt-1 block w-full px-3 py-3 bg-gray-50 border border-gray-300 rounded-xl text-sm shadow-sm placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                                placeholder="Masukkan NIK atau Username" required autofocus>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Password</label>
                            <input type="password" name="password"
                                class="mt-1 block w-full px-3 py-3 bg-gray-50 border border-gray-300 rounded-xl text-sm shadow-sm placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                                placeholder="Masukkan password" required>
                        </div>
                        <button type="submit"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                            Masuk
                        </button>
                    </form>
                    <div class="mt-4 text-center">
                        <button type="button" onclick="toggleOtpForm()"
                            class="text-sm font-medium text-green-600 hover:text-green-500 focus:outline-none">
                            <i class="fab fa-whatsapp mr-1"></i> Login Warga dengan WhatsApp OTP
                        </button>
                    </div>
                </div>

                <!-- OTP Request Form -->
                <div id="otpRequestForm" class="hidden">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nomor WhatsApp Warga</label>
                            <input type="tel" inputmode="numeric"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')" id="otpPhone"
                                class="mt-1 block w-full px-3 py-3 bg-gray-50 border border-gray-300 rounded-xl text-sm shadow-sm placeholder-gray-400 focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500"
                                placeholder="Contoh: 081234567890">
                        </div>
                        <button type="button" id="btnRequestOtp" onclick="requestOtp()"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                            Kirim Kode OTP
                        </button>
                    </div>
                    <div class="mt-4 text-center">
                        <button type="button" onclick="toggleOtpForm()"
                            class="text-sm font-medium text-indigo-600 hover:text-indigo-500 focus:outline-none">
                            Kembali ke Login Standar
                        </button>
                    </div>
                </div>

                <!-- OTP Verify Form -->
                <div id="otpVerifyForm" class="hidden">
                    <div class="space-y-6">
                        <div class="bg-green-50 text-green-700 p-3 rounded-lg text-sm mb-4">
                            Kode OTP telah dikirim ke WhatsApp Anda. Kode ini berlaku selama <span id="otpTimerDisplay"
                                class="font-bold text-red-600">01:00</span>.
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Masukkan Kode OTP 6 Digit</label>
                            <input type="tel" inputmode="numeric"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')" id="otpCode" maxlength="6"
                                class="mt-1 block w-full px-3 py-3 bg-gray-50 border border-gray-300 rounded-xl text-center text-2xl tracking-widest font-mono shadow-sm placeholder-gray-400 focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500"
                                placeholder="------">
                        </div>
                        <button type="button" id="btnVerifyOtp" onclick="verifyOtp()"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                            Verifikasi & Masuk
                        </button>
                        <button type="button" id="btnResendOtp" onclick="resendOtp()"
                            class="hidden w-full flex justify-center py-3 px-4 border border-gray-300 rounded-xl shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                            Kirim Ulang OTP
                        </button>
                    </div>
                    <div class="mt-4 text-center">
                        <button type="button" id="btnCancelOtp" onclick="toggleOtpForm()"
                            class="hidden text-sm font-medium text-indigo-600 hover:text-indigo-500 focus:outline-none">
                            Batal & Kembali
                        </button>
                    </div>
                </div>

                <div class="mt-8 text-center">
                    <p class="text-sm text-gray-600">
                        Warga Baru? <a href="{{ plugin_route('warga.register') }}"
                            class="font-medium text-indigo-600 hover:text-indigo-500">Daftar Akun</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        let otpCountdown;
        let otpTimeLeft = 60;
        let activePhone = '';

        // Check if there's an active OTP session on page load
        document.addEventListener('DOMContentLoaded', function () {
            let expiresAt = localStorage.getItem('otp_expires_at');
            let savedPhone = localStorage.getItem('otp_phone');

            if (expiresAt && savedPhone) {
                let now = Date.now();
                if (now < parseInt(expiresAt)) {
                    // Still valid
                    activePhone = savedPhone;
                    otpTimeLeft = Math.ceil((parseInt(expiresAt) - now) / 1000);

                    document.getElementById('otpPhone').value = activePhone;
                    document.getElementById('standardLoginForm').classList.add('hidden');
                    document.getElementById('otpRequestForm').classList.add('hidden');
                    document.getElementById('otpVerifyForm').classList.remove('hidden');

                    startOtpTimer(false); // false = don't reset expires_at
                } else {
                    // Expired
                    localStorage.removeItem('otp_expires_at');
                    localStorage.removeItem('otp_phone');
                }
            }
        });

        function startOtpTimer(isNewRequest = true) {
            clearInterval(otpCountdown);

            if (isNewRequest) {
                otpTimeLeft = 60;
                localStorage.setItem('otp_expires_at', Date.now() + 60000);
                localStorage.setItem('otp_phone', activePhone);
            }

            document.getElementById('btnVerifyOtp').classList.remove('hidden');
            document.getElementById('btnResendOtp').classList.add('hidden');
            document.getElementById('btnCancelOtp').classList.add('hidden');
            document.getElementById('otpCode').disabled = false;

            updateTimerDisplay();

            otpCountdown = setInterval(() => {
                otpTimeLeft--;
                updateTimerDisplay();

                if (otpTimeLeft <= 0) {
                    clearInterval(otpCountdown);
                    document.getElementById('otpTimerDisplay').innerText = "Waktu Habis";
                    document.getElementById('btnVerifyOtp').classList.add('hidden');
                    document.getElementById('btnResendOtp').classList.remove('hidden');
                    document.getElementById('btnCancelOtp').classList.remove('hidden');
                    document.getElementById('otpCode').disabled = true;

                    localStorage.removeItem('otp_expires_at');
                    localStorage.removeItem('otp_phone');
                }
            }, 1000);
        }

        function updateTimerDisplay() {
            if (otpTimeLeft > 0) {
                let m = Math.floor(otpTimeLeft / 60);
                let s = otpTimeLeft % 60;
                document.getElementById('otpTimerDisplay').innerText = (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
            }
        }

        function resendOtp() {
            document.getElementById('otpVerifyForm').classList.add('hidden');
            document.getElementById('otpRequestForm').classList.remove('hidden');
            localStorage.removeItem('otp_expires_at');
            localStorage.removeItem('otp_phone');
        }

        function toggleOtpForm() {
            clearInterval(otpCountdown);
            document.getElementById('standardLoginForm').classList.toggle('hidden');
            document.getElementById('otpRequestForm').classList.toggle('hidden');
            document.getElementById('otpVerifyForm').classList.add('hidden');
            localStorage.removeItem('otp_expires_at');
            localStorage.removeItem('otp_phone');
        }

        async function requestOtp() {
            let phone = document.getElementById('otpPhone').value;
            if (!phone) {
                alert('Silakan masukkan nomor WhatsApp Anda.');
                return;
            }
            if (phone.length < 11) {
                alert('Nomor WhatsApp tidak valid (minimal 11 angka).');
                return;
            }

            activePhone = phone;
            let btn = document.getElementById('btnRequestOtp');
            btn.innerHTML = 'Mengirim...';
            btn.disabled = true;

            try {
                let response = await fetch('{{ plugin_route('portal.login.otp.request') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ phone: phone })
                });

                let data = await response.json();
                if (data.success) {
                    document.getElementById('otpRequestForm').classList.add('hidden');
                    document.getElementById('otpVerifyForm').classList.remove('hidden');
                    document.getElementById('otpCode').value = '';
                    document.getElementById('otpCode').focus();
                    startOtpTimer();
                } else {
                    alert(data.message);
                }
            } catch (e) {
                alert('Terjadi kesalahan.');
            }

            btn.innerHTML = 'Kirim Kode OTP';
            btn.disabled = false;
        }

        async function verifyOtp() {
            let phone = document.getElementById('otpPhone').value;
            let otp = document.getElementById('otpCode').value;

            if (!otp || otp.length !== 6) {
                alert('Silakan masukkan 6 digit kode OTP.');
                return;
            }

            let btn = document.getElementById('btnVerifyOtp');
            btn.innerHTML = 'Memverifikasi...';
            btn.disabled = true;

            try {
                let response = await fetch('{{ plugin_route('portal.login.otp.verify') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ phone: phone, otp: otp })
                });

                let data = await response.json();
                if (data.success) {
                    localStorage.removeItem('otp_expires_at');
                    localStorage.removeItem('otp_phone');
                    window.location.href = data.redirect;
                } else {
                    alert(data.message);
                }
            } catch (e) {
                alert('Terjadi kesalahan.');
            }

            btn.innerHTML = 'Verifikasi & Masuk';
            btn.disabled = false;
        }
    </script>
</body>

</html>
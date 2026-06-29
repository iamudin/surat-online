<?php

namespace App\Http\Controllers\Plugins\SuratOnline\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plugins\SuratOnline\Warga;
use App\Models\Plugins\SuratOnline\Rt;
use App\Models\Plugins\SuratOnline\Rw;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
class PortalAuthController extends Controller
{
    public function landing(Request $request)
    {
        page_name('Portal Layanan Surat Desa');

        $stats = [
            'pending' => \App\Models\Plugins\SuratOnline\SuratRequest::where('status', 'pending')->count(),
            'processing' => \App\Models\Plugins\SuratOnline\SuratRequest::where('status', 'processing')->count(),
            'approved' => \App\Models\Plugins\SuratOnline\SuratRequest::where('status', 'approved')->count(),
            'rejected' => \App\Models\Plugins\SuratOnline\SuratRequest::where('status', 'rejected')->count(),
            'total' => \App\Models\Plugins\SuratOnline\SuratRequest::count(),
        ];

        $services = \App\Models\Plugins\SuratOnline\SuratType::where('is_active', true)->get();

        $trackResult = null;
        if ($request->filled('ticket')) {
            $trackResult = \App\Models\Plugins\SuratOnline\SuratRequest::with(['type', 'warga'])->where('ticket_number', $request->ticket)->first();
            if (!$trackResult) {
                session()->flash('error', 'Nomor tiket tidak ditemukan.');
            } else {
                page_name('Status Permohonan #' . $request->ticket . ' - Surat Online');

            }

        }

        return view('surat-online::frontend.portal.landing', compact('stats', 'services', 'trackResult'));
    }

    public function showLogin(Request $request)
    {
        if ($request->session()->has('kades_id') || $request->session()->has('warga_id') || $request->session()->has('rt_id')) {
            return redirect()->route('portal.dashboard');
        }
        page_name('Masuk Akun - Surat Online');
        return view('surat-online::frontend.portal.login');
    }

    public function login(Request $request)
    {

        $request->validate([
            'username_or_nik' => 'required',
            'password' => 'required'
        ]);

        $input = $request->username_or_nik;
        $password = $request->password;

        // Try to login as Warga first (usually numeric NIK)
        if (is_numeric($input)) {
            $warga = Warga::where('nik', $input)->first();
            if ($warga && Hash::check($password, $warga->password)) {
                if ($warga->is_blocked) {
                    return back()->with('error', 'Akun Anda telah diblokir. Silakan hubungi admin desa.');
                }
                $request->session()->put('warga_id', $warga->id);
                $request->session()->put('warga_nik', $warga->nik);
                $request->session()->put('warga_name', $warga->name);
                return redirect()->route('portal.dashboard');
            }
        }

        // Try to login as RT
        $rt = Rt::where('username', $input)->first();
        if ($rt && Hash::check($password, $rt->password)) {
            $request->session()->put('rt_id', $rt->id);
            $request->session()->put('rt_name', $rt->name);
            $request->session()->put('rt_nomor', $rt->nomor_rt);
            return redirect()->route('portal.dashboard');
        }

        // Try to login as Kades
        $kadesOption = \Leazycms\Web\Models\Option::where('name', 'data_pimpinan')->first();
        if ($kadesOption) {
            $dataPimpinan = json_decode($kadesOption->value, true);
            if ($dataPimpinan && isset($dataPimpinan['nik']) && $input == $dataPimpinan['nik']) {
                if (isset($dataPimpinan['password']) && Hash::check($password, $dataPimpinan['password']) || $password == $dataPimpinan['password']) {
                    $request->session()->put('kades_id', true);
                    $request->session()->put('kades_name', $dataPimpinan['nama'] ?? 'Kepala Desa');
                    $request->session()->put('kades_nip', $dataPimpinan['nip'] ?? '-');
                    return redirect()->route('portal.dashboard');
                }
            }
        }

        return back()->with('error', 'NIK / Username atau Password salah.');
    }

    public function requestOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required'
        ]);

        $phone = $request->phone;
        // Format phone number to start with 62
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $warga = Warga::where('phone', $phone)->orWhere('phone', $request->phone)->first();
        $name = $warga ? $warga->name : 'Warga Baru';

        $otp = rand(100000, 999999);
        $cacheKey = 'otp_' . $phone;
        Cache::put($cacheKey, $otp, now()->addMinutes(1)); // Valid for 3 minutes

        $waUrl = env('WA_SENDER_URL');
        $waSession = env('WA_SENDER_SESSION');

        if (!$waUrl) {
            return response()->json(['success' => false, 'message' => 'Konfigurasi WhatsApp Gateway belum diatur.']);
        }

        $message = "Halo {$name},\n\nKode OTP Anda untuk login Portal Surat Desa adalah: *{$otp}*\n\nKode ini hanya berlaku selama 1 menit. JANGAN berikan kode ini kepada siapapun.";

        dispatch(function () use ($waUrl, $phone, $message, $waSession) {
            Http::post(rtrim($waUrl, '/') . '/message/send-text', [
                'to' => $phone,
                'text' => $message,
                'session' => $waSession
            ]);
        })->afterResponse();



        return response()->json(['success' => true, 'message' => 'Kode OTP sedang dikirim ke WhatsApp Anda.', 'phone' => $phone]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'otp' => 'required|numeric'
        ]);

        $phone = $request->phone;
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }


        $otp = $request->otp;
        $cacheKey = 'otp_' . $phone;

        $cachedOtp = Cache::get($cacheKey);

        if (!$cachedOtp) {
            return response()->json(['success' => false, 'message' => 'Kode OTP telah kedaluwarsa atau tidak valid. Silakan minta kode baru.']);
        }

        if ($cachedOtp == $otp) {
            $warga = Warga::where('phone', $phone)->orWhere('phone', '0' . substr($phone, 2))->first();

            if (!$warga) {
                // Buat warga baru dengan data dummy jika belum terdaftar
                $warga = Warga::create([
                    'phone' => $phone,
                    'nik' => 'WA-' . time(),
                    'name' => 'Warga Baru',
                    'address' => '-',
                    'is_verified' => false,
                    'password' => \Illuminate\Support\Facades\Hash::make(uniqid())
                ]);
            }

            if ($warga) {
                Cache::forget($cacheKey);
                $request->session()->put('warga_id', $warga->id);
                $request->session()->put('warga_nik', $warga->nik);
                $request->session()->put('warga_name', $warga->name);
                return response()->json(['success' => true, 'redirect' => route('portal.dashboard')]);
            }
        }

        return response()->json(['success' => false, 'message' => 'Kode OTP salah.']);
    }

    public function showRegister()
    {
        page_name('Daftar Akun - Surat Online');

        $rws = Rw::with('rts')->orderBy('nomor_rw', 'asc')->get();
        return view('surat-online::frontend.warga.register', compact('rws'));
    }

    public function register(Request $request)
    {
        $nikRule = 'unique:wargas,nik';
        if (config('modules.multisite_enabled') && app()->has('tenant')) {
            $nikRule = \Illuminate\Validation\Rule::unique('wargas', 'nik')->where('tenant_id', tenant()->id);
        }

        $request->validate([
            'nik' => ['required', 'string', 'size:16', $nikRule],
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'rw_id' => 'required|exists:rws,id',
            'rt_id' => 'required|exists:rts,id',
            'password' => 'required|confirmed'
        ]);

        $warga = Warga::create([
            'nik' => $request->nik,
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'rw_id' => $request->rw_id,
            'rt_id' => $request->rt_id,
            'password' => Hash::make($request->password)
        ]);

        $request->session()->put('warga_id', $warga->id);
        $request->session()->put('warga_nik', $warga->nik);
        $request->session()->put('warga_name', $warga->name);

        return redirect()->route('portal.dashboard')->with('success', 'Pendaftaran berhasil!');
    }

    public function getRts(Request $request)
    {
        $rw_id = $request->rw_id;
        $rts = Rt::where('rw_id', $rw_id)->get();
        return response()->json($rts);
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['kades_id', 'kades_nip', 'kades_name', 'warga_id', 'warga_nik', 'warga_name', 'rt_id', 'rt_name', 'rt_nomor']);
        return redirect()->route('portal.login');
    }
}

<?php

namespace App\Http\Controllers\Plugins\SuratOnline\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plugins\SuratOnline\Warga;
use App\Models\Plugins\SuratOnline\Rt;
use App\Models\Plugins\SuratOnline\Rw;
use App\Models\Plugins\SuratOnline\SuratRequest;
use App\Models\Plugins\SuratOnline\SuratType;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Closure;
class PortalDashboardController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            RedirectMiddleware::handle(),
            function (Request $request, Closure $next) {
                if (!$request->session()->has('warga_id') && !$request->session()->has('rt_id') && !$request->session()->has('kades_id')) {
                    return redirect(plugin_route('portal.login'));
                }
                return $next($request);
            },
        ];
    }
    public function index(Request $request)
    {
        if ($request->session()->has('warga_id')) {
            return $this->wargaDashboard($request);
        }

        if ($request->session()->has('rt_id')) {
            return $this->rtDashboard($request);
        }

        if ($request->session()->has('kades_id')) {
            return $this->kadesDashboard($request);
        }
    }

    protected function wargaDashboard(Request $request)
    {
        $warga_id = $request->session()->get('warga_id');
        $warga = Warga::with(['rw', 'rt'])->find($warga_id);

        if (!$warga) {
            $request->session()->forget(['warga_id', 'warga_nik', 'warga_name', 'rt_id', 'rt_name', 'rt_nomor']);
            return redirect(plugin_route('portal.login'))->with('error', 'Akun Anda telah dihapus atau tidak ditemukan.');
        }
        $suratTypes = SuratType::where('is_active', true)->get();

        $query = SuratRequest::with(['type', 'data.field'])->where('warga_id', $warga_id);
        if ($request->has('status') && in_array($request->status, ['pending', 'processing', 'approved', 'rejected'])) {
            $query->where('status', $request->status);
        }
        $requests = $query->orderBy('id', 'desc')->paginate(5);

        $stats = [
            'pending' => SuratRequest::where('warga_id', $warga_id)->where('status', 'pending')->count(),
            'processing' => SuratRequest::where('warga_id', $warga_id)->where('status', 'processing')->count(),
            'approved' => SuratRequest::where('warga_id', $warga_id)->where('status', 'approved')->count(),
            'rejected' => SuratRequest::where('warga_id', $warga_id)->where('status', 'rejected')->count(),
        ];

        $isProfileIncomplete = str_starts_with($warga->nik, 'WA-');
        $rws = $isProfileIncomplete ? Rw::with('rts')->orderBy('nomor_rw', 'asc')->get() : collect();

        return view('surat-online::frontend.warga.dashboard', compact('warga', 'suratTypes', 'requests', 'stats', 'isProfileIncomplete', 'rws'));
    }

    protected function rtDashboard(Request $request)
    {
        $rt_id = $request->session()->get('rt_id');
        $rt = Rt::with('rw')->find($rt_id);

        if (!$rt) {
            $request->session()->forget(['warga_id', 'warga_nik', 'warga_name', 'rt_id', 'rt_name', 'rt_nomor']);
            return redirect(plugin_route('portal.login'))->with('error', 'Akun RT Anda telah dihapus atau tidak ditemukan.');
        }
        page_name('Dashboard RT - Surat Online');

        $query = SuratRequest::with(['type', 'warga', 'data.field'])
            ->whereHas('warga', function ($q) use ($rt_id) {
                $q->where('rt_id', $rt_id);
            });

        if ($request->has('validasi') && in_array($request->validasi, ['belum', 'sudah'])) {
            if ($request->validasi == 'belum') {
                $query->where('is_rt_approved', 0)->where('status', 'pending');
            } else {
                $query->where(function ($q) {
                    $q->where('is_rt_approved', 1)->orWhere('status', '!=', 'pending');
                });
            }
        }

        $requests = $query->orderBy('id', 'desc')->paginate(5);

        // Stats
        $stats = [
            'belum_divalidasi' => SuratRequest::whereHas('warga', function ($q) use ($rt_id) {
                $q->where('rt_id', $rt_id);
            })->where('is_rt_approved', 0)->where('status', 'pending')->count(),
            'sudah_divalidasi' => SuratRequest::whereHas('warga', function ($q) use ($rt_id) {
                $q->where('rt_id', $rt_id);
            })->where(function ($q) {
                $q->where('is_rt_approved', 1)->orWhere('status', '!=', 'pending');
            })->count(),
        ];

        // Get wargas needing verification
        $unverifiedWargas = Warga::where('rt_id', $rt_id)
            ->where('is_verified', false)
            ->whereNotNull('ktp_path')
            ->get();

        return view('surat-online::frontend.rt.dashboard', compact('requests', 'rt', 'unverifiedWargas', 'stats'));
    }

    public function uploadKtp(Request $request)
    {
        $request->validate([
            'ktp_image' => 'required|file|mimes:jpeg,png,jpg,webp,pdf|max:2048',
        ]);
        $warga_id = $request->session()->get('warga_id');
        $warga = Warga::findOrFail($warga_id);
        // Use Fileable trait to upload file
        $path = $warga->addFile([
            'file' => $request->file('ktp_image'),
            'purpose' => 'ktp',
            'mime_type' => ['image/png', 'image/jpeg', 'image/jpg', 'application/pdf', 'image/webp'],
            'random_name' => true
        ]);

        $warga->update([
            'ktp_path' => $path
        ]);

        if ($path) {
            return back()->with('success', 'KTP berhasil diupload! Menunggu verifikasi dari RT atau Admin.');
        } else {
            return back()->with('danger', 'KTP gagal diupload. Pastikan format file sesuai dan ukuran tidak melebihi 2MB.');
        }
    }

    public function verifyWarga(Request $request, $warga_id)
    {
        $rt_id = $request->session()->get('rt_id');

        $warga = Warga::where('rt_id', $rt_id)->findOrFail($warga_id);

        $warga->update([
            'is_verified' => true
        ]);

        if ($warga->ktp_path) {
            try {
                $warga->removeFileByPurposeAndChild('ktp');
            } catch (\Exception $e) {
            }

            $file_path = public_path($warga->ktp_path);
            if (file_exists($file_path) && is_file($file_path)) {
                @unlink($file_path);
            }

            $warga->update(['ktp_path' => null]);
        }

        return back()->with('success', 'Warga ' . $warga->name . ' berhasil diverifikasi.');
    }

    public function validateSurat(Request $request, $id)
    {
        $request->validate([
            'is_valid' => 'required|boolean',
            'catatan_rt' => 'nullable|string'
        ]);

        $rt_id = $request->session()->get('rt_id');

        $suratRequest = SuratRequest::whereHas('warga', function ($q) use ($rt_id) {
            $q->where('rt_id', $rt_id);
        })->findOrFail($id);

        if ($request->is_valid) {
            $suratRequest->update([
                'is_rt_approved' => true,
                'catatan_rt' => $request->catatan_rt
            ]);
            return back()->with('success', 'Surat berhasil ditandai valid.');
        } else {
            $suratRequest->update([
                'is_rt_approved' => false,
                'status' => 'rejected',
                'catatan_rt' => $request->catatan_rt
            ]);
            return back()->with('success', 'Surat ditandai tidak valid (ditolak).');
        }
    }

    public function updateProfile(Request $request)
    {
        $wargaId = $request->session()->get('warga_id');
        if (!$wargaId)
            return redirect()->back()->with('error', 'Sesi telah habis.');

        $warga = Warga::find($wargaId);
        if (!$warga)
            return redirect()->back()->with('error', 'Data warga tidak ditemukan.');

        $isProfileIncomplete = str_starts_with($warga->nik, 'WA-');

        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'required|string',
            'password' => 'nullable|string|min:6'
        ];

        if ($isProfileIncomplete) {
            $nikRule = 'unique:wargas,nik,' . $warga->id;
            if (config('modules.multisite_enabled') && app()->has('tenant')) {
                $nikRule = \Illuminate\Validation\Rule::unique('wargas', 'nik')->ignore($warga->id)->where('tenant_id', tenant()->id);
            }
            $rules['nik'] = ['required', 'string', 'size:16', $nikRule];
            $rules['rw_id'] = 'required|exists:rws,id';
            $rules['rt_id'] = 'required|exists:rts,id';
        }

        $request->validate($rules);

        $data = [
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
        ];

        if ($isProfileIncomplete) {
            $data['nik'] = $request->nik;
            $data['rw_id'] = $request->rw_id;
            $data['rt_id'] = $request->rt_id;
            // set is_verified to false to ensure they need to be verified
            $data['is_verified'] = false;
        }

        if ($request->filled('password')) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $warga->update($data);

        session(['warga_name' => $warga->name]);
        return redirect()->back()->with('success', 'Profil berhasil diperbarui.');
    }
    protected function kadesDashboard(Request $request)
    {
        page_name('Dashboard Kepala Desa - TTE Surat');

        $statusTab = $request->query('tab', 'waiting');

        $query = SuratRequest::with(['type', 'warga']);

        if ($statusTab === 'signed') {
            $query->where('is_signed', true);
        } else {
            $query->where('status', 'processing')->where('is_signed', false);
        }

        $requests = $query->orderBy('updated_at', 'desc')->paginate(4);

        $stats = [
            'waiting' => SuratRequest::where('status', 'processing')->where('is_signed', false)->count(),
            'signed' => SuratRequest::where('is_signed', true)->count(),
        ];

        return view('surat-online::frontend.kades.dashboard', compact('requests', 'stats', 'statusTab'));
    }

    public function kadesSignSurat(Request $request)
    {
        if (!$request->session()->has('kades_id')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized']);
        }

        $request->validate([
            'surat_id' => 'required',
            'passphrase' => 'required'
        ]);


        $suratId = $request->surat_id;
        $suratReq = SuratRequest::with(['type', 'warga', 'data.field'])->findOrFail($suratId);

        if ($suratReq->status !== 'processing' || $suratReq->is_signed) {
            return response()->json(['success' => false, 'message' => 'Surat tidak valid untuk di-TTE.']);
        }

        // Generate base64 dari TTE template (mengandung watermark)
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadview('surat-online::admin.surat_request.pdf_template', ['req' => $suratReq, 'is_tte' => true])->setPaper('a4', 'portrait');
        $pdfContent = $pdf->output();
        $base64Pdf = base64_encode($pdfContent);

        // Simulasi request ke API TTE eksternal
        $apiEndpoint = config('services.tte.endpoint', url('/api/sign'));
        try {
            // Uncomment the following lines to make the real API request
            // $response = Http::post($apiEndpoint, [
            //     'file' => $base64Pdf,
            //     'passphrase' => $request->passphrase
            // ]);
            // if (!$response->successful()) {
            //     throw new \Exception('API Error: ' . $response->body());
            // }
            // $signedPdfContent = base64_decode($response->json('signed_file'));

            // For now, we simulate success by using the generated PDF
            $signedPdfContent = $pdfContent;
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal terhubung ke API Sign: ' . $e->getMessage()]);
        }

        $filename = 'signed_' . $suratReq->ticket_number . '_' . time() . '.pdf';
        $tempPath = sys_get_temp_dir() . '/' . $filename;
        file_put_contents($tempPath, $signedPdfContent);
        $uploadedFile = new \Illuminate\Http\UploadedFile($tempPath, $filename, 'application/pdf', null, true);

        $path = $suratReq->addFile([
            'file' => $uploadedFile,
            'purpose' => 'signed_pdf',
            'mime_type' => ['application/pdf']
        ]);

        $suratReq->update([
            'is_signed' => true,
            'signed_at' => now(),
            'signed_pdf_path' => $path
        ]);

        return response()->json(['success' => true, 'message' => 'Surat berhasil ditandatangani secara elektronik.']);
    }
}

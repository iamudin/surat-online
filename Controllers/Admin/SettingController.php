<?php

namespace App\Http\Controllers\Plugins\SuratOnline\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    public function index()
    {
        $customDomain = get_option('surat-online-domain');
        return view('surat-online::admin.setting.index', compact('customDomain'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'custom_domain' => 'nullable|string|max:100',
        ]);

        $domain = $request->custom_domain;
        if (filter_var($domain, FILTER_VALIDATE_URL)) {
            $domain = parse_url($domain, PHP_URL_HOST);
        }

        if ($domain) {
            DB::table('options')->updateOrInsert(
                ['name' => 'surat-online-domain', 'tenant_id' => tenant() ? tenant()->id : null],
                ['value' => $domain, 'autoload' => 1]
            );
        } else {
            DB::table('options')
                ->where('name', 'surat-online-domain')
                ->where('tenant_id', tenant() ? tenant()->id : null)
                ->delete();
        }

        // Hapus cache agar domain baru langsung terdeteksi
        $host = request()->getHost();
        if (function_exists('tenant') && tenant()) {
            \Illuminate\Support\Facades\Cache::forget("tenant:{$host}:options");
        }

        return back()->with('success', 'Pengaturan plugin Surat Online berhasil disimpan.');
    }
}

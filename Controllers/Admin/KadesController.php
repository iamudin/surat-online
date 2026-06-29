<?php

namespace App\Http\Controllers\Plugins\SuratOnline\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Leazycms\Web\Models\Option;
use Illuminate\Support\Facades\Hash;

class KadesController extends Controller
{
    public function index(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'nama' => 'required',
                'nik' => 'required',
                'password' => 'nullable',
            ]);

            $option = Option::firstOrCreate(['name' => 'data_pimpinan']);

            $currentData = $option->value ? json_decode($option->value, true) : [];

            $newData = [
                'nama' => $request->nama,
                'nik' => $request->nik,
                'nip' => $request->nip,
            ];

            // Only hash and update password if provided
            if ($request->filled('password')) {
                $newData['password'] = Hash::make($request->password);
            } elseif (isset($currentData['password'])) {
                $newData['password'] = $currentData['password'];
            }

            $option->value = json_encode($newData);
            $option->autoload = '0';
            $option->save();

            return redirect()->back()->with('success', 'Data Kepala Desa berhasil disimpan.');
        }

        $option = Option::where('name', 'data_pimpinan')->first();
        $data = $option ? json_decode($option->value, true) : [];

        return view('surat-online::admin.kades.index', compact('data'));
    }
}

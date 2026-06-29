<?php

namespace App\Http\Controllers\Plugins\SuratOnline\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plugins\SuratOnline\Rw;
use Illuminate\Http\Request;

class RwController extends Controller
{
    public function index()
    {
        $rws = Rw::with('tenant')->orderBy('nomor_rw', 'asc')->get();
        return view('surat-online::admin.rw.index', compact('rws'));
    }

    public function create()
    {
        return view('surat-online::admin.rw.create');
    }

    public function store(Request $request)
    {
        $nomorRwRule = 'unique:rws,nomor_rw';
        if (config('modules.multisite_enabled') && app()->has('tenant')) {
            $nomorRwRule = \Illuminate\Validation\Rule::unique('rws', 'nomor_rw')->where('tenant_id', tenant()->id);
        }

        $request->validate([
            'nomor_rw' => ['required', $nomorRwRule],
            'name' => 'nullable|string'
        ]);

        Rw::create($request->all());
        return redirect()->route('rws.index')->with('success', 'Data RW berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $rw = Rw::findOrFail($id);
        return view('surat-online::admin.rw.edit', compact('rw'));
    }

    public function update(Request $request, $id)
    {
        $rw = Rw::findOrFail($id);
        
        $nomorRwRule = 'unique:rws,nomor_rw,'.$id;
        if (config('modules.multisite_enabled') && app()->has('tenant')) {
            $nomorRwRule = \Illuminate\Validation\Rule::unique('rws', 'nomor_rw')->ignore($id)->where('tenant_id', tenant()->id);
        }

        $request->validate([
            'nomor_rw' => ['required', $nomorRwRule],
            'name' => 'nullable|string'
        ]);

        $rw->update($request->all());
        return redirect()->route('rws.index')->with('success', 'Data RW berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $rw = Rw::findOrFail($id);
        if ($rw->rts()->count() > 0 || $rw->wargas()->count() > 0) {
            return redirect()->route('rws.index')->with('danger', 'Data RW tidak dapat dihapus karena masih memiliki data RT atau warga yang terkait.');
        }
        $rw->delete();
        return redirect()->route('rws.index')->with('success', 'Data RW berhasil dihapus.');
    }
}

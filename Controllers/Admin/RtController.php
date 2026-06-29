<?php

namespace App\Http\Controllers\Plugins\SuratOnline\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plugins\SuratOnline\Rt;
use App\Models\Plugins\SuratOnline\Rw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RtController extends Controller
{
    public function index()
    {
        $rts = Rt::with(['rw', 'tenant'])->orderBy('rw_id', 'asc')->orderBy('nomor_rt', 'asc')->get();
        return view('surat-online::admin.rt.index', compact('rts'));
    }

    public function create()
    {
        $rws = Rw::orderBy('nomor_rw', 'asc')->get();
        return view('surat-online::admin.rt.create', compact('rws'));
    }

    public function store(Request $request)
    {
        $usernameRule = 'unique:rts,username';
        if (config('modules.multisite_enabled') && app()->has('tenant')) {
            $usernameRule = \Illuminate\Validation\Rule::unique('rts', 'username')->where('tenant_id', tenant()->id);
        }

        $request->validate([
            'rw_id' => 'required|exists:rws,id',
            'nomor_rt' => 'required',
            'name' => 'nullable|string',
            'username' => ['required', 'string', $usernameRule],
            'password' => 'required|string|min:6'
        ]);

        $data = $request->all();
        $data['password'] = Hash::make($request->password);

        Rt::create($data);
        return redirect()->route('rts.index')->with('success', 'Data RT berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $rt = Rt::findOrFail($id);
        $rws = Rw::orderBy('nomor_rw', 'asc')->get();
        return view('surat-online::admin.rt.edit', compact('rt', 'rws'));
    }

    public function update(Request $request, $id)
    {
        $rt = Rt::findOrFail($id);

        $usernameRule = 'unique:rts,username,'.$id;
        if (config('modules.multisite_enabled') && app()->has('tenant')) {
            $usernameRule = \Illuminate\Validation\Rule::unique('rts', 'username')->ignore($id)->where('tenant_id', tenant()->id);
        }

        $request->validate([
            'rw_id' => 'required|exists:rws,id',
            'nomor_rt' => 'required',
            'name' => 'nullable|string',
            'username' => ['required', 'string', $usernameRule],
        ]);

        $data = $request->except('password');
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'string|min:6'
            ]);
            $data['password'] = Hash::make($request->password);
        }

        $rt->update($data);
        return redirect()->route('rts.index')->with('success', 'Data RT berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $rt = Rt::findOrFail($id);
        if ($rt->wargas()->count() > 0) {
            return redirect()->route('rts.index')->with('danger', 'Data RT tidak dapat dihapus karena masih memiliki data warga yang terkait.');
        }
        $rt->delete();
        return redirect()->route('rts.index')->with('success', 'Data RT berhasil dihapus.');
    }
}

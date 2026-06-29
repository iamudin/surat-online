<?php

namespace App\Http\Controllers\Plugins\SuratOnline\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plugins\SuratOnline\Warga;

class WargaController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $wargas = Warga::with(['rw', 'rt', 'tenant'])->select('wargas.*');
            return \Yajra\DataTables\Facades\DataTables::of($wargas)
                ->addIndexColumn()
                ->addColumn('tenant', function ($row) {
                    return $row->tenant ? $row->tenant->domain : '-';
                })
                ->addColumn('rw_rt', function ($row) {
                    $rw = $row->rw->nomor_rw ?? '-';
                    $rt = $row->rt->nomor_rt ?? '-';
                    return 'RW ' . $rw . ' / RT ' . $rt;
                })
                ->addColumn('ktp', function ($row) {
                    if ($row->ktp_path) {
                        return '<a href="' . url($row->ktp_path) . '" target="_blank" class="btn btn-info btn-sm"><i class="fa fa-image"></i> Lihat KTP</a>';
                    }
                    return '<span class="text-danger">Belum Upload</span>';
                })
                ->addColumn('status', function ($row) {
                    if ($row->is_verified) {
                        return '<span class="badge badge-success"><i class="fa fa-check"></i> Terverifikasi</span>';
                    }
                    return '<span class="badge badge-warning"><i class="fa fa-clock"></i> Belum Terverifikasi</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    if (!$row->is_verified && $row->ktp_path) {
                        $btn .= '<form action="' . route('admin.wargas.verify', $row->id) . '" method="POST" class="d-inline" onsubmit="return confirm(\'Yakin data KTP ini valid?\')">'
                              . csrf_field()
                              . '<button type="submit" class="btn btn-primary btn-sm mb-1 mr-1" title="Verifikasi Data"><i class="fa fa-check"></i></button>'
                              . '</form>';
                    }
                    
                    $blockBtnClass = $row->is_blocked ? 'btn-success' : 'btn-dark';
                    $blockBtnIcon = $row->is_blocked ? 'fa-unlock' : 'fa-ban';
                    $blockBtnTitle = $row->is_blocked ? 'Buka Blokir' : 'Blokir Warga';
                    $blockConfirm = $row->is_blocked ? 'Yakin ingin membuka blokir warga ini?' : 'Yakin ingin memblokir akses login warga ini?';
                    
                    $btn .= '<form action="' . route('admin.wargas.toggleBlock', $row->id) . '" method="POST" class="d-inline" onsubmit="return confirm(\'' . $blockConfirm . '\')">'
                          . csrf_field()
                          . '<button type="submit" class="btn ' . $blockBtnClass . ' btn-sm mb-1 mr-1" title="' . $blockBtnTitle . '"><i class="fa ' . $blockBtnIcon . '"></i></button>'
                          . '</form>';
                    
                    if ($row->requests()->count() == 0) {
                        $btn .= '<form action="' . route('wargas.destroy', $row->id) . '" method="POST" class="d-inline" onsubmit="return confirm(\'Yakin ingin menghapus Warga ini?\')">'
                              . csrf_field()
                              . method_field('DELETE')
                              . '<button type="submit" class="btn btn-danger btn-sm mb-1"><i class="fa fa-trash"></i></button>'
                              . '</form>';
                    }
                    return $btn;
                })
                ->rawColumns(['ktp', 'status', 'action'])
                ->make(true);
        }

        return view('surat-online::admin.warga.index');
    }

    public function verify($id)
    {
        $warga = Warga::findOrFail($id);
        
        $warga->update([
            'is_verified' => true
        ]);

        if ($warga->ktp_path) {
            try {
                $warga->removeFileByPurposeAndChild('ktp');
            } catch (\Exception $e) {}

            $file_path = public_path($warga->ktp_path);
            if (file_exists($file_path) && is_file($file_path)) {
                @unlink($file_path);
            }
            
            $warga->update(['ktp_path' => null]);
        }

        return back()->with('success', 'Warga ' . $warga->name . ' berhasil diverifikasi.');
    }

    public function toggleBlock($id)
    {
        $warga = Warga::findOrFail($id);
        $warga->update([
            'is_blocked' => !$warga->is_blocked
        ]);

        $statusMsg = $warga->is_blocked ? 'diblokir' : 'dibuka blokirnya';
        return back()->with('success', 'Warga ' . $warga->name . ' berhasil ' . $statusMsg . '.');
    }

    public function destroy($id)
    {
        $warga = Warga::findOrFail($id);
        if ($warga->requests()->count() > 0) {
            return back()->with('danger', 'Data Warga tidak dapat dihapus karena sudah memiliki data permohonan surat.');
        }
        $warga->delete();

        return back()->with('success', 'Data Warga berhasil dihapus.');
    }
}

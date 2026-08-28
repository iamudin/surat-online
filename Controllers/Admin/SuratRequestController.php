<?php

namespace App\Http\Controllers\Plugins\SuratOnline\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plugins\SuratOnline\SuratRequest;
use Illuminate\Http\Request;

class SuratRequestController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $requests = SuratRequest::with(['type', 'warga', 'tenant'])->select('surat_requests.*')->latest();
            
            if ($request->filled('status') && $request->status !== 'all') {
                $requests->where('status', $request->status);
            }

            if ($request->filled('start_date')) {
                $requests->whereDate('created_at', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $requests->whereDate('created_at', '<=', $request->end_date);
            }

            return \Yajra\DataTables\Facades\DataTables::of($requests)
                ->addIndexColumn()
                ->addColumn('tenant', function ($row) {
                    return $row->tenant ? $row->tenant->domain : '-';
                })
                ->addColumn('ticket', function ($row) {
                    return '<code>' . $row->ticket_number . '</code>';
                })
                ->addColumn('created_at', function ($row) {
                    return $row->created_at->format('d M Y H:i');
                })
                ->addColumn('surat_type', function ($row) {
                    return $row->type->name ?? '-';
                })
                ->addColumn('pemohon', function ($row) {
                    $name = $row->warga->name ?? '-';
                    $nik = $row->warga->nik ?? '-';
                    return $name . '<br><small class="text-muted">' . $nik . '</small>';
                })
                ->addColumn('status', function ($row) {
                    if ($row->status == 'pending') {
                        return '<span class="badge badge-warning">Menunggu</span>';
                    } elseif ($row->status == 'processing') {
                        return '<span class="badge badge-info">Diproses</span>';
                    } elseif ($row->status == 'approved') {
                        return '<span class="badge badge-success">Selesai/Disetujui</span>';
                    } else {
                        return '<span class="badge badge-danger">Ditolak</span>';
                    }
                })
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('surat-requests.show', $row->id) . '" class="btn btn-primary btn-sm">Lihat Detail & Proses</a>';
                })
                ->rawColumns(['ticket', 'pemohon', 'status', 'action'])
                ->toJson();
        }

        $stats = [
            'total' => SuratRequest::count(),
            'pending' => SuratRequest::where('status', 'pending')->count(),
            'processing' => SuratRequest::where('status', 'processing')->count(),
            'approved' => SuratRequest::where('status', 'approved')->count(),
            'rejected' => SuratRequest::where('status', 'rejected')->count(),
        ];

        return view('surat-online::admin.surat_request.index', compact('stats'));
    }

    public function report(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $status = $request->status ?? 'all';

        $query = SuratRequest::with(['type', 'warga'])
            ->whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
            ->orderBy('created_at', 'asc');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $requests = $query->get();

        return view('surat-online::admin.surat_request.report', compact('requests', 'start_date', 'end_date', 'status'));
    }

    public function show($id)
    {
        $req = SuratRequest::with(['type', 'warga', 'data.field'])->findOrFail($id);
        return view('surat-online::admin.surat_request.show', compact('req'));
    }

    public function edit($id)
    {
        $req = SuratRequest::with(['type', 'warga', 'data.field'])->findOrFail($id);
        return view('surat-online::admin.surat_request.edit', compact('req'));
    }

    public function update(Request $request, $id)
    {
        $req = SuratRequest::findOrFail($id);

        if ($request->update_type == 'data') {
            // Update the SuratRequestData
            if ($request->has('field_data')) {
                foreach ($request->field_data as $dataId => $value) {
                    $data = \App\Models\Plugins\SuratOnline\SuratRequestData::where('surat_request_id', $id)->find($dataId);
                    if ($data) {
                        if (is_array($value)) {
                            // If it's an array type, JSON encode it preserving keys or values
                            $value = json_encode(array_values($value));
                        }
                        $data->update(['field_value' => $value]);
                    }
                }
            }
            return redirect()->route('surat-requests.show', $id)->with('success', 'Data pengajuan berhasil diperbarui.');
        }

        $request->validate([
            'status' => 'required|in:pending,processing,approved,rejected',
        ]);

        $oldStatus = $req->status;

        $req->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'is_rt_approved' => true // Auto validasi jika diproses admin
        ]);

        if ($request->status == 'processing' && $oldStatus != 'processing') {
            $suratReq = SuratRequest::with(['type', 'warga', 'data.field'])->findOrFail($id);
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadview('surat-online::admin.surat_request.pdf_template', ['req' => $suratReq, 'is_tte' => false])->setPaper('a4', 'portrait');
            
            $filename = 'surat_' . $suratReq->ticket_number . '_' . time() . '.pdf';
            $tempPath = sys_get_temp_dir() . '/' . $filename;
            file_put_contents($tempPath, $pdf->output());
            $uploadedFile = new \Illuminate\Http\UploadedFile($tempPath, $filename, 'application/pdf', null, true);
            
            $path = $suratReq->addFile([
                'file' => $uploadedFile,
                'purpose' => 'pdf',
                'mime_type' => ['application/pdf']
            ]);
            
            $suratReq->update(['pdf_path' => $path]);
        }

        return redirect()->route('surat-requests.show', $id)->with('success', 'Status permohonan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        SuratRequest::findOrFail($id)->delete();
        return redirect()->route('surat-requests.index')->with('success', 'Permohonan berhasil dihapus.');
    }

    public function uploadFinal(Request $request, $id)
    {
        $request->validate([
            'final_file' => $request->hasFile('final_file') ? 'required|file|mimes:pdf,jpg,jpeg,png|max:5120' : 'required|string'
        ]);

        $req = SuratRequest::findOrFail($id);

        if ($request->hasFile('final_file')) {
            $file = $request->file('final_file');
            
            $path = $req->addFile([
                'file' => $file,
                'purpose' => 'signed_pdf',
                'mime_type' => ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png']
            ]);
        } elseif ($request->has('final_file') && is_string($request->final_file)) {
            $path = strip_tags($request->final_file);
        }

        $req->update([
            'signed_pdf_path' => $path, // we use signed_pdf_path to store the final file for Warga
            'is_signed' => true,
            'signed_at' => now(),
            'status' => 'approved' // auto approve if final uploaded
        ]);

        return redirect()->back()->with('success', 'Surat final berhasil diunggah.');
    }
}

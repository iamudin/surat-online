<?php

namespace App\Http\Controllers\Plugins\SuratOnline\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plugins\SuratOnline\SuratType;
use App\Models\Plugins\SuratOnline\SuratRequest;
use App\Models\Plugins\SuratOnline\SuratRequestData;
use Illuminate\Support\Str;

class WargaSuratController extends Controller
{
    public function dashboard(Request $request)
    {
        if (!$request->session()->has('warga_id')) {
            return redirect()->route('warga.login');
        }

        $wargaId = $request->session()->get('warga_id');
        $requests = SuratRequest::where('warga_id', $wargaId)->with('type')->orderBy('id', 'desc')->get();
        $types = SuratType::where('is_active', true)->get();

        return view('surat-online::frontend.warga.dashboard', compact('requests', 'types'));
    }

    public function getForm(Request $request, $slug)
    {
        if (!$request->session()->has('warga_id')) {
            return redirect()->route('portal.login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $warga = \App\Models\Plugins\SuratOnline\Warga::find($request->session()->get('warga_id'));
        if (!$warga) {
            $request->session()->forget(['warga_id', 'warga_nik', 'warga_name', 'rt_id', 'rt_name', 'rt_nomor']);
            return redirect()->route('portal.login')->with('error', 'Akun Anda telah dihapus atau tidak ditemukan.');
        }

        $type = SuratType::with('fields')->where('slug', $slug)->first();

        page_name('Formulir Permohonan: ' . $type->name);
        abort_if(!$type, '404');
        return view('surat-online::frontend.warga.form_page', compact('type'));
    }

    public function storeRequest(Request $request, $slug)
    {
        if (!$request->session()->has('warga_id')) {
            return response()->json(['success' => false, 'message' => 'Silakan login terlebih dahulu.'], 401);
        }

        $warga = \App\Models\Plugins\SuratOnline\Warga::find($request->session()->get('warga_id'));
        if (!$warga) {
            $request->session()->forget(['warga_id', 'warga_nik', 'warga_name', 'rt_id', 'rt_name', 'rt_nomor']);
            return response()->json(['success' => false, 'message' => 'Akun Anda telah dihapus atau tidak ditemukan. Sesi berakhir.'], 401);
        }

        $type = SuratType::with('fields')->where('slug', $slug)->firstOrFail();

        // Build validation rules dynamically
        $rules = [];
        $messages = [];
        foreach ($type->fields as $field) {
            if ($field->field_type == 'break')
                continue;

            $rule = [];
            if ($field->is_required) {
                $rule[] = 'required';
                $messages['field_' . $field->id . '.required'] = $field->field_name . ' wajib diisi.';
            } else {
                $rule[] = 'nullable';
            }

            if ($field->field_type == 'file') {
                $rule[] = 'file';
                $rule[] = 'max:1024'; // Max 1MB
                $messages['field_' . $field->id . '.max'] = $field->field_name . ' maksimal 1MB.';
            } elseif ($field->field_type == 'array') {
                $rule[] = 'array';
                $messages['field_' . $field->id . '.array'] = $field->field_name . ' format tidak valid.';
            }

            $rules['field_' . $field->id] = implode('|', $rule);
        }

        $request->validate($rules, $messages);

        // Generate Ticket Number
        $ticket = 'SRQ-' . date('Ymd') . '-' . strtoupper(Str::random(5));

        // Create Request
        $suratReq = SuratRequest::create([
            'ticket_number' => $ticket,
            'surat_type_id' => $type->id,
            'warga_id' => $request->session()->get('warga_id'),
            'status' => 'pending'
        ]);

        // Save Data
        foreach ($type->fields as $field) {
            $inputName = 'field_' . $field->id;

            $filePath = null;
            $fieldValue = null;

            if ($field->field_type == 'file' && $request->hasFile($inputName)) {
                $file = $request->file($inputName);
                $filePath = $suratReq->addFile([
                    'file' => $file,
                    'purpose' => 'surat_field_' . $field->id,
                    'random_name' => true,
                    'mime_type' => [$file->getMimeType()]
                ]);
            } elseif ($field->field_type == 'array') {
                // Remove empty rows
                $arrayData = $request->input($inputName);
                if (is_array($arrayData)) {
                    $cleanedData = array_filter($arrayData, function ($row) {
                        return count(array_filter($row)) > 0; // Keep if at least one column is filled
                    });
                    $fieldValue = json_encode(array_values($cleanedData));
                } else {
                    $fieldValue = json_encode([]);
                }
            } else {
                $fieldValue = $request->input($inputName);
            }

            SuratRequestData::create([
                'surat_request_id' => $suratReq->id,
                'surat_field_id' => $field->id,
                'field_value' => $fieldValue,
                'file_path' => $filePath
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Permohonan berhasil diajukan.',
            'ticket' => $ticket
        ]);
    }

    public function downloadSurat(Request $request, $id)
    {
        if (!$request->session()->has('warga_id')) {
            return redirect()->route('warga.login');
        }

        $wargaId = $request->session()->get('warga_id');
        $req = SuratRequest::with(['type', 'warga', 'data.field'])->where('warga_id', $wargaId)->where('id', $id)->firstOrFail();

        if ($req->status !== 'approved') {
            abort(403, 'Surat belum disetujui atau tidak tersedia.');
        }

        if ($req->signed_pdf_path) {
            $path = media($req->signed_pdf_path)->path();
            if (file_exists($path)) {
                return response()->download($path, 'Surat_' . $req->ticket_number . '.' . pathinfo($path, PATHINFO_EXTENSION));
            }
        }

        return view('surat-online::frontend.warga.surat_print', compact('req'));
    }
}

<?php

namespace App\Http\Controllers\Plugins\SuratOnline\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plugins\SuratOnline\SuratType;
use App\Models\Plugins\SuratOnline\SuratField;
use Illuminate\Http\Request;

class SuratTypeController extends Controller
{
    public function index()
    {
        $types = SuratType::with('tenant')->orderBy('id', 'desc')->get();
        return view('surat-online::admin.surat_type.index', compact('types'));
    }

    public function create()
    {
        return view('surat-online::admin.surat_type.create');
    }

    public function store(Request $request)
    {
        $slugRule = 'unique:surat_types,slug';
        if (config('modules.multisite_enabled') && app()->has('tenant')) {
            $slugRule = \Illuminate\Validation\Rule::unique('surat_types', 'slug')->where('tenant_id', tenant()->id);
        }

        $request->validate([
            'name' => 'required',
            'slug' => ['required', $slugRule],
        ]);

        SuratType::create($request->all());
        return redirect()->route('surat-types.index')->with('success', 'Jenis Surat berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $type = SuratType::with(['fields' => function($q) {
            $q->orderBy('order_num', 'asc')->orderBy('id', 'asc');
        }])->findOrFail($id);
        return view('surat-online::admin.surat_type.edit', compact('type'));
    }

    public function update(Request $request, $id)
    {
        $type = SuratType::findOrFail($id);
        $slugRule = 'unique:surat_types,slug,'.$id;
        if (config('modules.multisite_enabled') && app()->has('tenant')) {
            $slugRule = \Illuminate\Validation\Rule::unique('surat_types', 'slug')->ignore($id)->where('tenant_id', tenant()->id);
        }

        $request->validate([
            'name' => 'required',
            'slug' => ['required', $slugRule],
        ]);

        $type->update($request->all());
        return redirect()->route('surat-types.edit', $id)->with('success', 'Jenis Surat berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $type = SuratType::findOrFail($id);
        if ($type->requests()->count() > 0) {
            return redirect()->route('surat-types.index')->with('danger', 'Jenis Surat tidak dapat dihapus karena sudah digunakan dalam permohonan surat.');
        }
        $type->delete();
        return redirect()->route('surat-types.index')->with('success', 'Jenis Surat berhasil dihapus.');
    }

    // Methods for Fields
    public function storeField(Request $request, $typeId)
    {
        $type = SuratType::findOrFail($typeId);
        $request->validate([
            'field_name' => 'required',
            'field_type' => 'required',
        ]);
        
        $data = $request->all();
        $data['surat_type_id'] = $type->id;
        
        // Handle options or array columns
        if($request->field_options) {
            $data['field_options'] = array_map('trim', explode(',', $request->field_options));
        } else {
            $data['field_options'] = null;
        }

        // Set order_num
        $maxOrder = SuratField::where('surat_type_id', $type->id)->max('order_num');
        $data['order_num'] = $maxOrder ? $maxOrder + 1 : 1;

        SuratField::create($data);
        return redirect()->route('surat-types.edit', $typeId)->with('success', 'Field berhasil ditambahkan.');
    }

    public function updateField(Request $request, $id)
    {
        $field = SuratField::findOrFail($id);
        $request->validate([
            'field_name' => 'required',
            'field_type' => 'required',
        ]);

        $data = $request->all();
        if($request->field_options) {
            $data['field_options'] = array_map('trim', explode(',', $request->field_options));
        } else {
            $data['field_options'] = null;
        }

        $field->update($data);
        return redirect()->route('surat-types.edit', $field->surat_type_id)->with('success', 'Field berhasil diperbarui.');
    }

    public function moveField(Request $request, $id)
    {
        $field = SuratField::findOrFail($id);
        $typeId = $field->surat_type_id;
        $direction = $request->input('direction');

        // Re-index all fields first to ensure sequential order_num (1, 2, 3...)
        $fields = SuratField::where('surat_type_id', $typeId)
                    ->orderBy('order_num', 'asc')
                    ->orderBy('id', 'asc')
                    ->get();
        
        $currentIndex = 0;
        $fieldIndex = 0;
        foreach ($fields as $index => $f) {
            $f->update(['order_num' => $index + 1]);
            if ($f->id == $field->id) {
                $fieldIndex = $index;
            }
        }

        // Now move up or down
        if ($direction == 'up' && $fieldIndex > 0) {
            $swap = $fields[$fieldIndex - 1];
            $temp = $field->order_num;
            $field->update(['order_num' => $swap->order_num]);
            $swap->update(['order_num' => $temp]);
        } elseif ($direction == 'down' && $fieldIndex < count($fields) - 1) {
            $swap = $fields[$fieldIndex + 1];
            $temp = $field->order_num;
            $field->update(['order_num' => $swap->order_num]);
            $swap->update(['order_num' => $temp]);
        }

        return redirect()->route('surat-types.edit', $typeId);
    }

    public function destroyField($id)
    {
        $field = SuratField::findOrFail($id);
        $typeId = $field->surat_type_id;

        // Cek apakah field ini sudah digunakan di permohonan warga
        $isUsed = \App\Models\Plugins\SuratOnline\SuratRequestData::where('surat_field_id', $field->id)->exists();
        if ($isUsed) {
            return redirect()->route('surat-types.edit', $typeId)
                ->withErrors(['Field "' . $field->field_name . '" tidak bisa dihapus karena sudah terdapat permohonan warga yang menggunakan field tersebut.']);
        }

        $field->delete();
        return redirect()->route('surat-types.edit', $typeId)->with('success', 'Field berhasil dihapus.');
    }
}

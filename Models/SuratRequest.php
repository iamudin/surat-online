<?php

namespace App\Models\Plugins\SuratOnline;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratRequest extends Model
{
    use HasFactory, \Leazycms\FLC\Traits\Fileable;
    use \Leazycms\Web\Models\Trait\BelongsToTenant;

    protected $fillable = ['ticket_number', 'surat_type_id', 'warga_id', 'status', 'admin_notes', 'is_rt_approved', 'catatan_rt', 'pdf_path', 'signed_pdf_path', 'is_signed', 'signed_at'];

    public function type()
    {
        return $this->belongsTo(\App\Models\Plugins\SuratOnline\SuratType::class, 'surat_type_id');
    }

    public function warga()
    {
        return $this->belongsTo(\App\Models\Plugins\SuratOnline\Warga::class);
    }

    public function data()
    {
        return $this->hasMany(\App\Models\Plugins\SuratOnline\SuratRequestData::class)
            ->join('surat_fields', 'surat_fields.id', '=', 'surat_request_data.surat_field_id')
            ->orderBy('surat_fields.order_num', 'asc')
            ->select('surat_request_data.*');
    }
}



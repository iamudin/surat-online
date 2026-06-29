<?php

namespace App\Models\Plugins\SuratOnline;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratRequestData extends Model
{
    use HasFactory;
    use \Leazycms\Web\Models\Trait\BelongsToTenant;

    protected $fillable = ['surat_request_id', 'surat_field_id', 'field_value', 'file_path'];

    public function request()
    {
        return $this->belongsTo(\App\Models\Plugins\SuratOnline\SuratRequest::class, 'surat_request_id');
    }

    public function field()
    {
        return $this->belongsTo(\App\Models\Plugins\SuratOnline\SuratField::class, 'surat_field_id');
    }
}



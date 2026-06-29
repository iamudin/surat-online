<?php

namespace App\Models\Plugins\SuratOnline;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratField extends Model
{
    use HasFactory;
    use \Leazycms\Web\Models\Trait\BelongsToTenant;

    protected $fillable = [
        'surat_type_id',
        'field_name',
        'description',
        'field_type',
        'field_options',
        'is_required',
        'order_num'
    ];

    protected $casts = [
        'field_options' => 'array',
        'is_required' => 'boolean',
    ];

    public function type()
    {
        return $this->belongsTo(\App\Models\Plugins\SuratOnline\SuratType::class, 'surat_type_id');
    }
}



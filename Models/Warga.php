<?php

namespace App\Models\Plugins\SuratOnline;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warga extends Model
{
    use HasFactory, \Leazycms\FLC\Traits\Fileable;
    use \Leazycms\Web\Models\Trait\BelongsToTenant;

    protected $fillable = ['rw_id', 'rt_id', 'nik', 'name', 'phone', 'address', 'password', 'ktp_path', 'is_verified', 'is_blocked'];

    protected $hidden = ['password'];

    public function requests()
    {
        return $this->hasMany(\App\Models\Plugins\SuratOnline\SuratRequest::class);
    }

    public function rw()
    {
        return $this->belongsTo(\App\Models\Plugins\SuratOnline\Rw::class);
    }

    public function rt()
    {
        return $this->belongsTo(\App\Models\Plugins\SuratOnline\Rt::class);
    }
}



<?php

namespace App\Models\Plugins\SuratOnline;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rt extends Model
{
    use HasFactory;
    use \Leazycms\Web\Models\Trait\BelongsToTenant;

    protected $fillable = ['rw_id', 'nomor_rt', 'name', 'username', 'password'];

    protected $hidden = ['password'];

    public function rw()
    {
        return $this->belongsTo(\App\Models\Plugins\SuratOnline\Rw::class);
    }

    public function wargas()
    {
        return $this->hasMany(\App\Models\Plugins\SuratOnline\Warga::class);
    }
}



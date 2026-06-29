<?php

namespace App\Models\Plugins\SuratOnline;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rw extends Model
{
    use HasFactory;
    use \Leazycms\Web\Models\Trait\BelongsToTenant;

    protected $fillable = ['nomor_rw', 'name'];

    public function rts()
    {
        return $this->hasMany(\App\Models\Plugins\SuratOnline\Rt::class);
    }
}



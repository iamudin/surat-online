<?php

namespace App\Models\Plugins\SuratOnline;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratType extends Model
{
    use HasFactory;
    use \Leazycms\Web\Models\Trait\BelongsToTenant;

    protected $fillable = ['name', 'slug', 'description', 'is_active'];

    public function fields()
    {
        return $this->hasMany(\App\Models\Plugins\SuratOnline\SuratField::class)->orderBy('order_num', 'asc');
    }

    public function requests()
    {
        return $this->hasMany(\App\Models\Plugins\SuratOnline\SuratRequest::class);
    }
}



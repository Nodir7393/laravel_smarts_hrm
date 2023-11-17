<?php

namespace Modules\ALL\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\hasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TgChannelText extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $guarded =[];

    protected $casts = [
        'performers_id' => 'array',
    ];


    public function comments(): hasMany
    {
        return $this->hasMany(TgGroupText::class);
    }

}

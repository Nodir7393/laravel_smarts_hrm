<?php

namespace Modules\ALL\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TgChannel extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $guarded = [];

    public function hrmProject() : \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(HrmProject::class);
    }
}

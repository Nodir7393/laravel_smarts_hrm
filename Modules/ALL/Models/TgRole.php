<?php

namespace Modules\ALL\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TgRole extends Model
{
    protected $table = 'tg_roles';
    use HasFactory;
    protected $guarded = [];

}

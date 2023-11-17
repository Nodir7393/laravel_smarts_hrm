<?php

namespace Modules\ALL\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\ALL\Models\TgRole;

class TgUser extends Model
{
    use HasFactory;
    protected $guarded = [];
    public $timestamps = true;

    public function role(){
        return $this->belongsToMany(TgRole::class, 'tg_user_roles', 'tg_user_id', 'tg_role_id');
    }
}

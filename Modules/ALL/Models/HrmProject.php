<?php

namespace Modules\ALL\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrmProject extends Model
{
    use HasFactory;

    protected $casts = [
        'users_dev' => 'array',
        'users_pm' => 'array',
        'users_qa' => 'array',
    ];
}

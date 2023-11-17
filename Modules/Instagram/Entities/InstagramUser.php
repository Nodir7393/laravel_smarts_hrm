<?php

namespace Modules\Instagram\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InstagramUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_name',
        'password',
        'csrftoken',
        'rur',
        'mid',
        'ds_user_id',
        'ig_did',
        'session_id',
        'ig_cb',
    ];

    protected static function newFactory()
    {
        return \Modules\Instagram\Database\factories\InstagramUserFactory::new();
    }
}

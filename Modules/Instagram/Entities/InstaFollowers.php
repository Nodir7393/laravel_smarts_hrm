<?php

namespace Modules\Instagram\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InstaFollowers extends Model
{
    use HasFactory;

    protected $fillable = [
        'insta_id',
        'insta_user_id',
        'username',
        'fullName',
        'profilePicUrl',
        'isVerified',
        'followedByViewer',
        'requestedByViewer'
    ];
}

<?php

namespace Modules\Instagram\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InstaFollower extends Model
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

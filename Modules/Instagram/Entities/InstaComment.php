<?php

namespace Modules\Instagram\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class InstaComment extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'isNew',
        'isLoaded',
        'isLoadEmpty',
        'isFake',
        'isAutoConstruct',
        'modified',
        'insta_id',
        'insta_user_id',
        'text',
        'createdAt',
        'childCommentsCount',
        'hasMoreChildComments',
        'childCommentsNextPage',
    ];
}

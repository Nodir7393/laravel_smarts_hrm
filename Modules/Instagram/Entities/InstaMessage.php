<?php

namespace Modules\Instagram\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InstaMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'isNew',
        'isLoaded',
        'isLoadedEmpty',
        'isFake',
        'isAutoConstruct',
        'modified',
        'insta_id',
        'insta_chat_id',
        'type',
        'text',
        'time',
        'userId',
        'reelShare'
    ];
}

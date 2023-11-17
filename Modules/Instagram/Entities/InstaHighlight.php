<?php

namespace Modules\Instagram\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InstaHighlight extends Model
{
    use HasFactory;

    protected $fillable = [
        'isNew',
        'isLoaded',
        'isLoadEmpty',
        'isFake',
        'isAutoConstruct',
        'modified',
        'id',
        'title',
        'imageThumbnailUrl',
        'imageCroppedThumbnailUrl',
        'ownerId'
    ];
}

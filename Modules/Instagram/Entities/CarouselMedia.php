<?php

namespace Modules\Instagram\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CarouselMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'insta_id',
        'type',
        'imageLowResolutionUrl',
        'imageThumbnailUrl',
        'imageStandardResolutionUrl',
        'imageHighResolutionUrl',
        'videoLowResolutionUrl',
        'videoStandardResolutionUrl',
        'videoLowBandwidthUrl',
        'videoViews',
        'insta_user_id'
    ];
}

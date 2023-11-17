<?php

namespace Modules\Instagram\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InstaMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'isNew',
        'isLoaded',
        'isLoadEmpty',
        'isFake',
        'isAutoConstruct',
        'modified',
        'insta_id',
        'insta_user_id',
        'shortCode',
        'createdTime',
        'type',
        'link',
        'imageLowResolutionUrl',
        'imageThumbnailUrl',
        'imageStandardResolutionUrl',
        'imageHighResolutionUrl',
        'caption',
        'isCaptionEdited',
        'isAd',
        'videoLowResolutionUrl',
        'videoStandardResolutionUrl',
        'videoDuration',
        'videoLowBandwidthUrl',
        'videoViews',
        'owner',
        'post_id',
        'ownerId',
        'likesCount',
        'hasLiked',
        'locationId',
        'locationName',
        'commentsDisabled',
        'commentsCount',
        'hasMoreComments',
        'commentsNextPage',
        'locationSlug',
        'altText',
        'locationAddressJson',
        'InstagramScraperModelMediamediatype'
    ];
}

<?php

namespace Modules\Instagram\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InstaPost extends Model
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
        'imageHighResolutionUrl',
        'caption',
        'isCaptionEdited',
        'isAd',
        'videoLowResolutionUrl',
        'videoStandardResolutionUrl',
        'videoDuration',
        'videoLowBandwidthUrl',
        'videoViews',
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

    public function sidecar(): HasMany
    {
        return $this->hasMany(InstaSidecar::class, 'insta_post_id', 'insta_id');
    }

}

<?php

namespace Modules\Instagram\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InstaSidecar extends Model
{
    use HasFactory;

    protected $fillable = [
        'isNew',
        'isLoaded',
        'isLoadEmpty',
        'isFake',
        'isAutoConstruct',
        'modified',
        'data',
        'insta_id',
        'shortCode',
        'createdTime',
        'type',
        'link',
        'imageLowResolutionUrl',
        'imageThumbnailUrl',
        'imageStandardResolutionUrl',
        'imageHighResolutioUrl',
        'squareImages',
        'carouselMedia',
        'caption',
        'isCaptionEdited',
        'isAd',
        'videoLowResolutionUrl',
        'videoStandardResolutionUrl',
        'videoDuration',
        'videoLowBandwidthUrl',
        'videoViews',
        'owner',
        'ownerId',
        'likesCount',
        'hasLiked',
        'locationId',
        'locationName',
        'commentsDisabled',
        'commentsCount',
        'comments',
        'previewComments',
        'hasMoreComments',
        'commentsNextPage',
        'sidecarMedias',
        'locationSlug',
        'altText',
        'locationAddressJson',
        'taggedUsers',
        'taggedUsersIds',
        'type',
    ];

    protected static function newFactory()
    {
        return \Modules\Instagram\Database\factories\InstaSidecarFactory::new();
    }
}

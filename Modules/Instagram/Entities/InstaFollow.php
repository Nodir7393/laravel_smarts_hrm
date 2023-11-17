<?php

namespace Modules\Instagram\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InstaFollow extends Model
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
        'fbid',
        'username',
        'fullName',
        'profilePicUrl',
        'profilePicUrlHd',
        'biography',
        'externalUrl',
        'followsCount',
        'followedByCount',
        'mediaCount',
        'isPrivate',
        'isVerified',
        'blockedByViewer',
        'countryBlock',
        'followedByViewer',
        'followsViewer',
        'hasChannel',
        'hasClips',
        'hasGuides',
        'hasBlockedViewer',
        'highlightReelCount',
        'hasRequestedViewer',
        'isBusinessAccount',
        'isProfessionalAccount',
        'isJoinedRecently',
        'businessCategoryName',
        'businessEmail',
        'businessPhoneNumber',
        'businessAddressJson',
        'requestedByViewer',
        'connectedFbPage',
        'categoryName',
    ];
}

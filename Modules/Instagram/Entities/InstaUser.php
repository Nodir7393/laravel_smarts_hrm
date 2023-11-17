<?php

namespace Modules\Instagram\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InstaUser extends Model
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
        'insta_id',
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
        'categoryName'
    ];

    public function followers(): HasMany
    {
        return $this->hasMany(InstaFollowers::class, 'insta_user_id', 'insta_id');
    }

    public function medias(): HasMany
    {
        return $this->hasMany(InstaMedia::class, 'insta_user_id', 'insta_id');
    }
}

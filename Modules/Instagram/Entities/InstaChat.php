<?php

namespace Modules\Instagram\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InstaChat extends Model
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
        'title',
        'type',
        'archived',
        'hasNewer',
        'hasOlder',
        'isGroup',
        'isPin',
        'readState'
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(InstaMessage::class, 'insta_chat_id', 'insta_id');
    }

}

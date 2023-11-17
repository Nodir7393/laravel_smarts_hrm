<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('insta_posts', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('isNew');
            $table->tinyInteger('isLoaded');
            $table->tinyInteger('isLoadEmpty');
            $table->tinyInteger('isFake');
            $table->tinyInteger('isAutoConstruct');
            $table->integer('modified');
            $table->string('insta_id');
            $table->string('insta_user_id');
            $table->string('shortCode');
            $table->integer('createdTime');
            $table->string('type');
            $table->string('link');
            $table->text('imageLowResolutionUrl');
            $table->text('imageThumbnailUrl');
            $table->text('imageHighResolutionUrl');
            $table->string('caption');
            $table->tinyInteger('isCaptionEdited');
            $table->tinyInteger('isAd');
            $table->text('videoLowResolutionUrl');
            $table->text('videoStandardResolutionUrl');
            $table->string('videoDuration');
            $table->text('videoLowBandwidthUrl');
            $table->integer('videoViews');
            $table->string('ownerId');
            $table->integer('likesCount');
            $table->string('hasLiked');
            $table->string('locationId');
            $table->string('locationName');
            $table->tinyInteger('commentsDisabled');
            $table->integer('commentsCount');
            $table->tinyInteger('hasMoreComments');
            $table->string('commentsNextPage');
            $table->string('locationSlug');
            $table->string('altText');
            $table->string('locationAddressJson');
            $table->string('InstagramScraperModelMediamediatype');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('insta_posts');
    }
};

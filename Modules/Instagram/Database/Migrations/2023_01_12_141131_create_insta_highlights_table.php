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
        Schema::create('insta_highlights', function (Blueprint $table) {
            $table->id();
            $table->boolean("isNew");
            $table->boolean("isLoaded");
            $table->boolean("isLoadEmpty");
            $table->boolean("isFake");
            $table->boolean("isAutoConstruct");
            $table->integer("modified");
            $table->string("insta_id");
            $table->string("title");
            $table->string("imageThumbnailUrl");
            $table->string("imageCroppedThumbnailUrl");
            $table->integer("ownerId");
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
        Schema::dropIfExists('insta_highlights');
    }
};

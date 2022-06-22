<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->string('font_size')->nullable()->after('background');
            $table->string('color')->nullable()->after('font_size');
            $table->string('free_turns')->nullable()->after('font_size');
            $table->string('code_prefix')->nullable()->after('font_size');
            $table->string('title_game')->nullable()->after('font_size');
            $table->string('reward_form')->nullable()->after('font_size');
            $table->boolean('show_suffix')->default(1)->nullable()->after('font_size');
            $table->string('banner_image_share')->nullable()->after('font_size');
            $table->string('content_share')->nullable()->after('font_size');
            $table->json('hashtag')->nullable()->after('font_size');
            $table->boolean('create_winner')->default(1)->nullable()->after('font_size');
            $table->boolean('is_publish')->default(1)->nullable()->after('font_size');
            $table->string('frame')->nullable()->after('background');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn([
                'color', 'font_size', 'title_game', 'code_prefix', 'free_turns', 'reward_form',
                'banner_image_share', 'content_share', 'hashtag', 'create_winner', 'is_publish', 'show_suffix'
            ]);
        });
    }
};

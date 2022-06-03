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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->string('description', 500);
            $table->string('banner')->nullable();
            $table->string('background')->nullable();
            $table->text('email_template')->nullable();
            $table->text('rule')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->tinyInteger('status')->default(1);
            $table->boolean('reward_use_image')->default(true);
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->string('redirect_url');
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
        Schema::dropIfExists('games');
    }
};

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
        Schema::create('api_credentials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->integer('source_type')->nullable();
            $table->integer('destination_type')->nullable();
            $table->text('token1')->nullable(); // main token
            $table->text('token2')->nullable(); // refresh token
            $table->text('token3')->nullable(); // access token (for some apis)
            $table->text('identifier')->nullable(); // external user id or username
            $table->text('identifier2')->nullable(); // external user id or username
            $table->integer('type'); // see ApiCredentialType
            $table->boolean('active')->default(false);
            $table->dateTime('expires_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_credentials');
    }
};

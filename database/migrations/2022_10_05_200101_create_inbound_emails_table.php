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
        Schema::create('inbound_emails', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->mediumText('to');
            $table->mediumText('from');
            $table->mediumText('subject');
            $table->text('body');
            $table->tinyText('confirmation_code')->nullable();
            $table->mediumText('confirmation_url')->nullable();
            $table->text('headers');
            $table->tinyText('sender_ip')->nullable();
            $table->uuid();
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
        Schema::dropIfExists('inbound_emails');
    }
};

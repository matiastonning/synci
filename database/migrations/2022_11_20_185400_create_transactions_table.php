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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('source_id');
            $table->string('external_id', 128)->nullable();

            $table->integer('status')->default(0);

            $table->decimal('amount', 18, 2);
            $table->string('currency', 3)->nullable();

            $table->string('description_long', 256)->nullable();
            $table->string('description_short', 128)->nullable();
            $table->string('category', 128)->nullable();
            $table->string('reference', 256)->nullable();

            $table->mediumText('merchant_category')->nullable();
            $table->mediumText('merchant_name')->nullable();

            $table->dateTime('payment_date');
            $table->dateTime('booking_date')->nullable();

            $table->uuid();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('source_id')->references('id')->on('sources');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};

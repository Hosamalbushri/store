<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attribute_order_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order__details_id');
            $table->unsignedBigInteger('attribute_option_id');
            $table->timestamps();

            $table->foreign('order__details_id')->references('id')->on('order__details')->onDelete('cascade');
            $table->foreign('attribute_option_id')->references('id')->on('attribute_options')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_order_detail');
    }
};

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
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('status_changed_by')->nullable()->after('order__status_id');
            $table->timestamp('status_changed_at')->nullable()->after('status_changed_by');
            $table->foreign('status_changed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['status_changed_by']);
            $table->dropColumn('status_changed_by');
            $table->dropColumn('status_changed_at');
        });
    }
};

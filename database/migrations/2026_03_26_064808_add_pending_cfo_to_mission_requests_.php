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
        Schema::table('mission_requests', function (Blueprint $table) {
             $table->enum('status', ['pending_tl','pending_ceo','pending_cfo', 'pending_hr', 'approved', 'rejected'])->default('pending_tl')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mission_requests', function (Blueprint $table) {
             $table->enum('status', ['pending_tl','pending_ceo', 'pending_hr', 'approved', 'rejected'])->default('pending_tl')->change();
        });
    }
};

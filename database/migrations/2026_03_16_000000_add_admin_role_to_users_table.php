<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('role')->default('staff')->change();
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->enum('role', ['user', 'approver'])->default('user')->change();
    });
}
};

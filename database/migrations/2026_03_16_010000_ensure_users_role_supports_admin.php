<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    // ប្តូរពី ENUM មកជា VARCHAR(255) ដើម្បីដាក់ Role អ្វីក៏បាន
    Schema::table('users', function (Blueprint $table) {
        $table->string('role')->default('staff')->change();
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        // បើចង់ត្រឡប់ទៅ ENUM វិញ (មិនណែនាំទេ បើចង់ប្រើ Workflow វែង)
        DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('user','approver','admin') NOT NULL DEFAULT 'user'");
    });
}
};

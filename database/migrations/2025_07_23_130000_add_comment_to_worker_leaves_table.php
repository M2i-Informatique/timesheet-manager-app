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
        Schema::table('worker_leaves', function (Blueprint $table) {
            $table->text('comment')->nullable()->after('type_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('worker_leaves', function (Blueprint $table) {
            $table->dropColumn('comment');
        });
    }
};
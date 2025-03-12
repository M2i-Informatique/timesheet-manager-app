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
        Schema::create('time_sheetables', function (Blueprint $table) {
            $table->id();

            $table->date('date');
            $table->decimal('hours', 4, 2);
            $table->enum('category', ['day', 'night']);

            $table->foreignIdFor(\App\Models\Project::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedBigInteger('timesheetable_id');
            $table->string('timesheetable_type');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_sheetables');
    }
};

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
        Schema::create('projectables', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(\App\Models\Project::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedBigInteger('projectable_id');
            $table->string('projectable_type');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projectables');
    }
};

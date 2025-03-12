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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            $table->integer('code');
            $table->enum('category', ['mh', 'go', 'other']);
            $table->string('name', 255);
            $table->string('address', 255);
            $table->string('city', 255);
            $table->decimal('distance', 10, 2);
            $table->enum('status', ['active', 'inactive']);

            $table->foreignIdFor(\App\Models\Zone::class)
                ->constrained()
                ->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};

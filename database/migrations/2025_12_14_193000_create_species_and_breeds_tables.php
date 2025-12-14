<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('species', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('breeds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('species_id')->constrained('species')->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(['species', 'breed']);
            $table->foreignId('species_id')->nullable()->constrained('species')->nullOnDelete();
            $table->foreignId('breed_id')->nullable()->constrained('breeds')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropForeign(['species_id']);
            $table->dropForeign(['breed_id']);
            $table->dropColumn(['species_id', 'breed_id']);
            $table->string('species')->nullable();
            $table->string('breed')->nullable();
        });

        Schema::dropIfExists('breeds');
        Schema::dropIfExists('species');
    }
};

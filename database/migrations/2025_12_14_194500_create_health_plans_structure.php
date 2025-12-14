<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vaccinations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('health_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('species_id')->constrained('species')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('health_plan_vaccination', function (Blueprint $table) {
            $table->id();
            $table->foreignId('health_plan_id')->constrained('health_plans')->cascadeOnDelete();
            $table->foreignId('vaccination_id')->constrained('vaccinations')->cascadeOnDelete();
            $table->integer('frequency_days')->nullable()->comment('Days between vaccinations');
            $table->integer('start_age_weeks')->default(0)->comment('Age in weeks to start');
            $table->timestamps();
        });

        Schema::create('vaccination_visit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained('visits')->cascadeOnDelete();
            $table->foreignId('vaccination_id')->constrained('vaccinations')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::table('breeds', function (Blueprint $table) {
            $table->foreignId('health_plan_id')->nullable()->constrained('health_plans')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('breeds', function (Blueprint $table) {
            $table->dropForeign(['health_plan_id']);
            $table->dropColumn('health_plan_id');
        });

        Schema::dropIfExists('vaccination_visit');
        Schema::dropIfExists('health_plan_vaccination');
        Schema::dropIfExists('health_plans');
        Schema::dropIfExists('vaccinations');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('medications', function (Blueprint $table) {
            $table->string('strength')->nullable()->after('sku');
            $table->string('category')->nullable()->after('strength');
            $table->string('dosage')->nullable()->after('category');
            $table->dropColumn('description');
        });
    }

    public function down(): void
    {
        Schema::table('medications', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
            $table->dropColumn(['strength', 'category', 'dosage']);
        });
    }
};

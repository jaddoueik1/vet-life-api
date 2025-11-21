<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('medications', function (Blueprint $table) {
            $table->string('sku')->unique()->after('name');
            $table->integer('current_stock')->default(0)->after('description');
            $table->decimal('price', 10, 2)->default(0)->after('current_stock');
            $table->integer('reorder_level')->default(0)->after('price');
            $table->string('category')->nullable()->after('sku');
            $table->dropColumn('unit_price');
        });
    }

    public function down(): void
    {
        Schema::table('medications', function (Blueprint $table) {
            $table->dropColumn(['sku', 'current_stock', 'price', 'reorder_level', 'category']);
            $table->decimal('unit_price', 10, 2)->default(0)->after('description');
        });
    }
};

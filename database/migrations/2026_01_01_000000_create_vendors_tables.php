<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('main_contact_name')->nullable();
            $table->string('main_contact_email')->nullable();
            $table->string('main_contact_phone')->nullable();
            $table->string('secondary_contact_name')->nullable();
            $table->string('secondary_contact_email')->nullable();
            $table->string('secondary_contact_phone')->nullable();
            $table->timestamps();
        });

        Schema::create('medication_vendor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medication_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            $table->unique(['medication_id', 'vendor_id']);
        });

        Schema::create('inventory_item_vendor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            $table->unique(['inventory_item_id', 'vendor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_item_vendor');
        Schema::dropIfExists('medication_vendor');
        Schema::dropIfExists('vendors');
    }
};

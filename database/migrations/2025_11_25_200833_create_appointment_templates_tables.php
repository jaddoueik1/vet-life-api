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
        Schema::create('appointment_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('duration'); // in minutes
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('appointment_template_inventory_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_template_id')->constrained(indexName: 'apt_tmpl_inv_item_tmpl_id_fk')->cascadeOnDelete();
            $table->foreignId('inventory_item_id')->constrained(indexName: 'apt_tmpl_inv_item_inv_id_fk')->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->timestamps();
        });

        Schema::create('appointment_template_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_template_id')->constrained(indexName: 'apt_tmpl_user_tmpl_id_fk')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained(indexName: 'apt_tmpl_user_user_id_fk')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('appointment_template_id')->nullable()->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['appointment_template_id']);
            $table->dropColumn('appointment_template_id');
        });

        Schema::dropIfExists('appointment_template_user');
        Schema::dropIfExists('appointment_template_inventory_item');
        Schema::dropIfExists('appointment_templates');
    }
};

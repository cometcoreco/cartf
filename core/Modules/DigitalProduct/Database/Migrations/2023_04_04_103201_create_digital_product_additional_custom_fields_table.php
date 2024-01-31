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
        Schema::disableForeignKeyConstraints();

        Schema::create('additional_custom_fields', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('additional_field_id');
            $table->text('option_name');
            $table->text('option_value');
            $table->timestamps();

            $table->foreign('additional_field_id')->references('id')->on('additional_fields')->cascadeOnDelete();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('digital_product_additional_custom_fields');
    }
};

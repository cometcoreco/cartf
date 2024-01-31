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

        Schema::create('additional_fields', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('badge_id')->nullable();
            $table->foreign('badge_id')->references('id')->on('badges');
            $table->integer('pages')->nullable();
            $table->text('language')->nullable();
            $table->text('formats')->nullable();
            $table->text('words')->nullable();
            $table->text('tool_used')->nullable();
            $table->text('database_used')->nullable();
            $table->text('compatible_browsers')->nullable();
            $table->text('compatible_os')->nullable();
            $table->text('high_resolution')->nullable();
            $table->unsignedBigInteger('author_id')->nullable();
            $table->timestamps();

            $table->foreign('author_id')->references('id')->on('digital_authors');
            $table->foreign('product_id')->references('id')->on('digital_products')->cascadeOnDelete();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('digital_product_additional_fields');
    }
};

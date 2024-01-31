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

        Schema::create('digital_products', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->text('slug');
            $table->longText('summary')->nullable();
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('image_id');
            $table->text('included_files')->nullable();
            $table->string('version')->nullable();
            $table->date('release_date')->nullable();
            $table->date('update_date')->nullable();
            $table->text('preview_link')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('accessibility')->nullable();
            $table->boolean('is_licensable')->nullable()->default(false);
            $table->unsignedBigInteger('tax')->nullable()->comment('tax = digital_product_taxes id');
            $table->foreign('tax')->references('id')->on('digital_product_taxes');
            $table->string('file');
            $table->double('regular_price');
            $table->double('sale_price')->nullable();
            $table->timestamp('free_date')->nullable();
            $table->timestamp('promotional_date')->nullable();
            $table->double('promotional_price')->nullable();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('digital_products');
    }
};

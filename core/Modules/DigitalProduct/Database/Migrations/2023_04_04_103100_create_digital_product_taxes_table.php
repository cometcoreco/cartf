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

        Schema::create('digital_product_taxes', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->double('tax_percentage')->comment('tax amount in percent');
            $table->boolean('status')->comment('0=draft, 1=publish');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('digital_product_taxes');
    }
};
